<?php


namespace App\Command;


use App\Entity\HotelGroup;
use App\Entity\Reservation;
use App\Entity\Transaction;
use App\Entity\TrxCode;
use App\Service\EurotelService;
use App\Service\RabbitmqService;
use App\Utils\Logger\CloudLogger;
use App\Utils\Serializer\InhouseSerializer;
use App\Utils\Serializer\TransactionSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Google\Cloud\Logging\PsrLogger;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PastDataFeedCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:pastDataFeed';

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var RabbitmqService */
    private RabbitmqService $rabbitmqService;

    /** @var PsrLogger */
    private PsrLogger $logger;

    /** @var InhouseSerializer */
    private InhouseSerializer $inhouseSerializer;

    /** @var TransactionSerializer */
    private TransactionSerializer $transactionSerializer;

    /** @var EurotelService */
    private EurotelService $eurotelService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @param CloudLogger $logger
     * @param InhouseSerializer $inhouseSerializer
     * @param TransactionSerializer $transactionSerializer
     * @param EurotelService $eurotelService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RabbitmqService $rabbitmqService, CloudLogger $logger,
        InhouseSerializer $inhouseSerializer,
        TransactionSerializer $transactionSerializer,
        EurotelService $eurotelService)
    {
        // Set entityManager's SQLLogger to null to avoid memory leaks
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->entityManager = $entityManager;
        $this->rabbitmqService = $rabbitmqService;
        $this->logger = $logger->psrLogger('eurotel-integration');
        $this->inhouseSerializer = $inhouseSerializer;
        $this->transactionSerializer = $transactionSerializer;
        $this->eurotelService = $eurotelService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Past data feed command.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is used to feed a hotelgroup with past data.')
            // command's arguments
            ->addArgument('name', InputArgument::REQUIRED, 'Which hotel group do you want to feed?')
            ->addArgument('fromDate', InputArgument::REQUIRED, 'Start date (Y-m-d)?')
            ->addArgument('toDate', InputArgument::REQUIRED, 'End date (Y-m-d)?')
        ;
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        date_default_timezone_set('UTC');
        $groupName = $input->getArgument('name');
        $fromDate = $input->getArgument('fromDate');
        $toDate = $input->getArgument('toDate');
        $output->writeln('Data feed input: '.$groupName.' '.$fromDate.' '.$toDate);

        $hotelGroup = $this->entityManager->getRepository(HotelGroup::class)->findOneBy(array('groupName' => $groupName));
        $fromDate = strtotime($fromDate);
        $toDate = strtotime($toDate);
        $dateIntervals = [];
        while ( $fromDate < $toDate) {
            $dateLimit = strtotime('+ 7 days', $fromDate);
            $dateIntervals[] = $dateLimit < $toDate ? ['fromDate' => $fromDate,'toDate' => $dateLimit] : ['fromDate' => $fromDate,'toDate' => $toDate];
            $fromDate = $dateLimit;
        }
        // Get in-house guests [fromDate, toDate)
        foreach ($dateIntervals as $dateInterval) {
            $fromDate = $dateInterval['fromDate'];
            $toDate = $dateInterval['toDate'];
            $output->writeln(gmdate('Y-m-d', $fromDate).' '.gmdate('Y-m-d', $toDate).': Get inhouse guests ...');
            $this->getInhouseGuests($hotelGroup, $fromDate, $toDate - 1);
        }
        // Get transactions [fromDate, toDate)
        foreach ($dateIntervals as $dateInterval) {
            $fromDate = $dateInterval['fromDate'];
            $toDate = $dateInterval['toDate'];
            $output->writeln(gmdate('Y-m-d', $fromDate).' '.gmdate('Y-m-d', $toDate).': Get transactions ...');
            $this->getTransactions($hotelGroup, $fromDate, $toDate - 1);
        }
    }

    /**
     * @param HotelGroup $hotelGroup
     * @param $fromDate
     * @param $toDate
     * @throws NonUniqueResultException
     * @throws GuzzleException
     */
    private function getInhouseGuests(HotelGroup $hotelGroup, $fromDate, $toDate)
    {
        $inhouseGuests = $this->eurotelService->getInhouseGuestsFromTo($hotelGroup, $fromDate, $toDate);
        $inhouseGuests = $this->inhouseSerializer->deserialize($inhouseGuests, 'App\Entity\Reservation[]', 'json');

        $reservationsToBeSent = [];
        /** @var Reservation $incomingReservation */
        foreach ($inhouseGuests as $incomingReservation) {
            // Check if there is an existingReservation with the reservationId of the incomingReservation
            $existingReservation = $this->entityManager->getRepository(Reservation::class)
                ->findLastByReservationId($incomingReservation->getReservationId());
            // If the incoming reservation is new or modified store a new entry to db and add the reservation to 'reservationsToBeSent' array
            if (!(isset($existingReservation) && $this->isEqual($incomingReservation, $existingReservation))) {
                $incomingReservation->setHotelGroup($hotelGroup);
                $incomingReservation->setCreated(time());
                $this->entityManager->persist($incomingReservation);
                $reservationsToBeSent[] = $this->inhouseSerializer->serialize($incomingReservation, 'json');
            }
        }
        $this->entityManager->flush();
        // Send reservationsToBeSent to reservation_queue
        $this->rabbitmqService->sendMessagesToQueue($reservationsToBeSent, 'reservation_queue');
        $this->entityManager->flush();
    }

    /**
     * @param HotelGroup $hotelGroup
     * @param $fromDate
     * @param $toDate
     * @throws NonUniqueResultException
     * @throws GuzzleException
     */
    private function getTransactions(HotelGroup $hotelGroup, $fromDate, $toDate): void
    {
        $transactions = $this->eurotelService->getTransactions($hotelGroup, $fromDate, $toDate);
        $transactions = $this->transactionSerializer->deserialize($transactions, 'App\Entity\Transaction[]', 'json');

        /** Save transaction in database */
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $transaction->setHotelGroup($hotelGroup);
            /** Check if trxCode for transaction exist in trx_code table*/
            $trx = $this->entityManager->getRepository(TrxCode::class)
                ->findOneByHotelGroupResortTrx($hotelGroup, $transaction->getResort(), $transaction->getTrxCode());
            if (isset($trx)) {
                $transaction->setTransactionCategory($trx->getTransactionCategory());
            } else {
                $this->logger->warning("CheckoutConsumer: TrxCode " . $transaction->getTrxCode() . " not found for " . $transaction->getResort() . " resort.");
            }
            $this->entityManager->persist($transaction);
        }
        $this->entityManager->flush();

        /** Sent only transactions with transactionCategory */
        $transactionsToBeSent = [];
        foreach ($transactions as $transaction) {
            if ($transaction->getTransactionCategory() !== null) {
                $transactionsToBeSent[] = $this->transactionSerializer->serialize($transaction, 'json');
            }
        }

        /** Send valid transactions to transaction queue */
        if (!$this->rabbitmqService->sendMessagesToQueue($transactionsToBeSent, 'transaction_queue')) {
            $this->logger->error("CheckoutConsumer: Failed to publish messages to transaction_queue ".json_encode($transactionsToBeSent));
        }
    }

    /**
     * Check if two Reservations differ in any field.
     *
     * @param Reservation $r1
     * @param Reservation $r2
     * @return bool
     */
    private function isEqual(Reservation $r1, Reservation $r2): bool
    {
        return (
            $r1->getReservationId() == $r2->getReservationId() &&
            $r1->getConfirmationNo() == $r2->getConfirmationNo() &&
            $r1->getNameId() == $r2->getNameId() &&
            $r1->getTitle() == $r2->getTitle() &&
            $r1->getLast() == $r2->getLast() &&
            $r1->getFirst() == $r2->getFirst() &&
            $r1->getMemberNo() == $r2->getMemberNo() &&
            $r1->getMemberLevel() == $r2->getMemberLevel() &&
            $r1->getVip() == $r2->getVip() &&
            $r1->getLastStay() == $r2->getLastStay() &&
            $r1->getRoomNo() == $r2->getRoomNo() &&
            $r1->getRoomType() == $r2->getRoomType() &&
            $r1->getAdults() == $r2->getAdults() &&
            $r1->getChildren() == $r2->getChildren() &&
            $r1->getArrivalDate() == $r2->getArrivalDate() &&
            $r1->getDepartureDate() == $r2->getDepartureDate() &&
            $r1->getEmail() == $r2->getEmail() &&
            $r1->getMobilePhone() == $r2->getMobilePhone() &&
            $r1->getGender() == $r2->getGender() &&
            $r1->getNationality() == $r2->getNationality() &&
            $r1->getBirthdate() == $r2->getBirthdate() &&
            $r1->getAddress() == $r2->getAddress() &&
            $r1->getCity() == $r2->getCity() &&
            $r1->getPostalCode() == $r2->getPostalCode() &&
            $r1->getCountry() == $r2->getCountry() &&
            $r1->getRateCode() == $r2->getRateCode() &&
            $r1->getMarketCode() == $r2->getMarketCode() &&
            $r1->getSourceCode() == $r2->getSourceCode() &&
            $r1->getTravelAgentName() == $r2->getTravelAgentName() &&
            $r1->getCompanyName() == $r2->getCompanyName() &&
            $r1->getSrcName() == $r2->getSrcName() &&
            $r1->getPreferences() == $r2->getPreferences() &&
            $r1->getRemarks() == $r2->getRemarks() &&
            $r1->getResort() == $r2->getResort() &&
            $r1->getStatus() == $r2->getStatus()
        );
    }
}