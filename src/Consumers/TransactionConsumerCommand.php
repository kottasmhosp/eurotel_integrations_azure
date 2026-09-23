<?php

namespace App\Consumers;


use App\Service\IntegrationService;
use App\Service\RabbitmqService;
use App\Utils\Logger\CloudLogger;
use ErrorException;
use Exception;
use Google\Cloud\Logging\PsrLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransactionConsumerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'consumers:transactionConsumer:start';

    /** @var RabbitmqService */
    private RabbitmqService $rabbitmqService;

    /** @var PsrLogger */
    private PsrLogger $logger;

    /** @var IntegrationService */
    private IntegrationService $integrationService;

    /**
     * @param RabbitmqService $rabbitmqService
     * @param CloudLogger $logger
     * @param IntegrationService $integrationService
     */
    public function __construct(RabbitmqService $rabbitmqService, CloudLogger $logger, IntegrationService $integrationService)
    {
        $this->rabbitmqService = $rabbitmqService;
        $this->integrationService = $integrationService;
        $this->logger = $logger->psrLogger('eurotel-integration');

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Transaction Consumer for rabbitmq.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command starts a transaction consumer.');
    }

    /**
     * @throws ErrorException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $connection = $this->rabbitmqService->getConnection();
        $channel = $this->rabbitmqService->createChannel($connection, 'transaction_queue');
        $this->logger->info("[*] TransactionConsumer waiting for messages...");

        $callback = function ($msg) {
            try {
                $this->logger->info("TransactionConsumer: Sending to GIS " . $msg->body);
                $response = $this->integrationService->postTransaction($msg->body);
                $this->logger->info("TransactionConsumer: " . $response->getReasonPhrase() . " " . $response->getStatusCode());
                // Message acknowledgments: Send basic_ack to queue
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $this->logger->info("TransactionConsumer: Acknowledge was sent to queue!");
            } catch (Exception $e) {
                $this->logger->error('TransactionConsumer: ' . $e->getMessage());
                $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
            }
        };

        // Fair dispatch
        $channel->basic_qos(NULL, 1, NULL);
        $channel->basic_consume('transaction_queue', '', FALSE, FALSE, FALSE, FALSE, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}