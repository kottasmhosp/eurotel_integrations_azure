<?php

namespace App\Consumers;


use App\Entity\HotelGroup;
use App\Entity\Transaction;
use App\Entity\TrxCode;
use App\Service\EurotelService;
use App\Service\RabbitmqService;
use App\Utils\Logger\CloudLogger;
use App\Utils\Serializer\TransactionSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Google\Cloud\Logging\PsrLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckoutConsumerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'consumers:checkoutConsumer:start';

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var RabbitmqService */
    private RabbitmqService $rabbitmqService;

    /** @var PsrLogger */
    private PsrLogger $logger;

    /** @var TransactionSerializer */
    private TransactionSerializer $serializer;

    /** @var EurotelService */
    private EurotelService $eurotelService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @param CloudLogger $logger
     * @param TransactionSerializer $serializer
     * @param EurotelService $eurotelService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RabbitmqService $rabbitmqService,
        CloudLogger $logger,
        TransactionSerializer $serializer,
        EurotelService $eurotelService)
    {
        $this->entityManager = $entityManager;
        $this->rabbitmqService = $rabbitmqService;
        $this->logger = $logger->psrLogger('eurotel-integration');
        $this->serializer = $serializer;
        $this->eurotelService = $eurotelService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Hotel Consumer for rabbitmq.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command starts a hotel consumer for checkout_hotel_queue.');
    }

    /**
     * @throws \ErrorException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $connection = $this->rabbitmqService->getConnection();
        $channel = $this->rabbitmqService->createChannel($connection, 'checkout_hotel_queue');
        $this->logger->info("[*] CheckoutConsumer waiting for messages...");

        $callback = function ($msg) {
            try {
                $this->logger->info("CheckoutConsumer: Get message: " . $msg->body);
                $message = json_decode($msg->body, true);
                $hotelGroup = $this->entityManager->getRepository(HotelGroup::class)->find($message['hotelId']);

                $this->logger->info("CheckoutConsumer: Getting transactions from Eurotel for " . $hotelGroup->getGroupName() . " ...");
                $transactions = $this->eurotelService->getTransactions($hotelGroup, $message['fromDate'], $message['toDate']);

                $this->logger->info("CheckoutConsumer: Process transactions for " . $hotelGroup->getGroupName() . " ...");
                $transactions = $this->serializer->deserialize($transactions, 'App\Entity\Transaction[]', 'json');

                /** Save transaction in database */
                /** @var Transaction $transaction */
                foreach ($transactions as $transaction) {
                    $transaction->setHotelGroup($hotelGroup);
                    /** Check if trxCode for transaction exist in trx_code table*/
                    /** @var TrxCode $trx */
                    $trx = $this->entityManager->getRepository(TrxCode::class)
                        ->findOneByHotelGroupResortTrx($hotelGroup, $transaction->getResort(), $transaction->getTrxCode());
                    if (isset($trx)) {
                        $transaction->setTransactionCategory($trx->getTransactionCategory());
                    } else {
                        $transaction->setTransactionCategory('');
                        $this->logger->warning("CheckoutConsumer: TrxCode " . $transaction->getTrxCode() . " not found for " . $transaction->getResort() . " resort.");
                    }
                    $this->entityManager->persist($transaction);
                }
                $this->entityManager->flush();

                /** Sent only transactions with transactionCategory */
                $transactionsToBeSent = [];
                foreach ($transactions as $transaction) {
                    print_r('aaaaaa-----');
                    print_r($transaction->getResort());
                    print_r("\n");
                    if (!empty($transaction->getTransactionCategory())) {
                        $transactionsToBeSent[] = $this->serializer->serialize($transaction, 'json');
                    }
                }

                /** Send valid transactions to transaction queue */
                if (!$this->rabbitmqService->sendMessagesToQueue($transactionsToBeSent, 'transaction_queue')) {
                    $this->logger->error("CheckoutConsumer: Failed to publish messages to transaction_queue ".json_encode($transactionsToBeSent));
                }

                // Message acknowledgments: Send basic_ack to queue
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $this->logger->info("CheckoutConsumer: Acknowledge for hotelGroup with id " . $hotelGroup->getId() . " was sent to queue!");
            } catch (Exception $e) {
                $this->logger->error('CheckoutConsumer: ' . $e->getMessage());
                $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag']);
            }
        };

        // Fair dispatch
        $channel->basic_qos(NULL, 1, NULL);
        $channel->basic_consume('checkout_hotel_queue', '', FALSE, FALSE, FALSE, FALSE, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}