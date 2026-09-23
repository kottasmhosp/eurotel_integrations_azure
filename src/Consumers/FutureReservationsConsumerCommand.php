<?php

namespace App\Consumers;


use App\Entity\FutureReservation;
use App\Entity\HotelGroup;
use App\Service\EurotelService;
use App\Service\RabbitmqService;
use App\Utils\Exception\EmptyContentException;
use App\Utils\Logger\CloudLogger;
use App\Utils\Serializer\FutureReservationSerializer;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use Google\Cloud\Logging\PsrLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FutureReservationsConsumerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'consumers:futureReservationConsumer:start';

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var RabbitmqService */
    private RabbitmqService $rabbitmqService;

    /** @var PsrLogger */
    private PsrLogger $logger;

    /** @var FutureReservationSerializer */
    private FutureReservationSerializer $serializer;

    /** @var EurotelService */
    private EurotelService $eurotelService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @param CloudLogger $logger
     * @param FutureReservationSerializer $serializer
     * @param EurotelService $eurotelService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RabbitmqService $rabbitmqService,
        CloudLogger $logger,
        FutureReservationSerializer $serializer,
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
            ->setHelp('This command starts a hotel consumer for future_reservations_queue.');
    }

    /**
     * @throws ErrorException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $connection = $this->rabbitmqService->getConnection();
        $channel = $this->rabbitmqService->createChannel($connection, 'future_reservations_queue');
        $this->logger->info("[*] Future Reservations waiting for messages...");

        $callback = function ($msg) {
            try {
                // Starting clock time in seconds
                $start_time = microtime(true);

                $message = json_decode($msg->body, true);
                $this->logger->info("Future Reservations: Get message: " . $message['hotelId']);
                /** @var HotelGroup $hotelGroup */
                $hotelGroup = $this->entityManager->getRepository(HotelGroup::class)->find($message['hotelId']);
                $this->logger->info("Future Reservations: Hotel Found: " . $message['hotelId']);

                if (!empty($message['type']) && $message['type'] == 'creation') {
                    $futureGuests = $this->eurotelService->getInsertDateReservations($hotelGroup, $message['fromDate'], $message['toDate']);
                } else {
                    $futureGuests = $this->eurotelService->getFutureReservations($hotelGroup, $message['fromDate'], $message['toDate']);
                }

                $this->logger->info("Future Reservations: Received future reservations: " . $message['hotelId']);

                // When a hotel closes, then eurotel returns an empty content response
                // If that happens then throw a new EmptyContentException and set in progress to false
                if ($futureGuests == "false") {
                    $this->logger->warning("Future Reservations: Received Empty Response for future reservations: " . $msg->body);
                    if(!empty($message['type'] ) && $message['type'] == 'creation'){
                        $hotelGroup->setCreationDateIntegrationInProgress(false);
                    } else {
                        $hotelGroup->setFutureReservationsIntegrationInProgress(false);
                    }
                    $hotelGroup->setModified(time());
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                } elseif ($futureGuests == "null") {
                    $this->logger->warning("Future Reservations: Received Empty Response for future reservations: " . $msg->body);
                    if(!empty($message['type'] ) && $message['type']  == 'creation'){
                        $hotelGroup->setCreationDateIntegrationInProgress(false);
                    } else {
                        $hotelGroup->setFutureReservationsIntegrationInProgress(false);
                    }
                    $hotelGroup->setModified(time());
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }

                $futureGuests = $this->serializer->deserialize($futureGuests, 'App\Entity\FutureReservation[]', 'json');
                $this->logger->info("Future Reservations: Deserialized future reservations: " . $message['hotelId']);


                $reservationsToBeSent = [];
                /** @var FutureReservation $incomingReservation */
                foreach ($futureGuests as $incomingReservation) {
                    // Check if there is an existingReservation with the reservationId of the incomingReservation
                    $existingReservation = $this->entityManager->getRepository(FutureReservation::class)
                        ->findLastByReservationId($incomingReservation->getReservationId());

                    // If the incoming reservation is new or modified store a new entry to db and add the reservation to 'reservationsToBeSent' array
                    if (!(isset($existingReservation) && $this->isEqual($incomingReservation, $existingReservation))) {
                        $incomingReservation->setHotelGroup($hotelGroup);
                        $incomingReservation->setCreated(time());
                        $this->entityManager->persist($incomingReservation);
                        $reservationsToBeSent[] = $this->serializer->serialize($incomingReservation, 'json');
                    }
                }
                $this->logger->info("Future Reservations: Processed future reservations: " . $message['hotelId']);
                // End clock time in seconds
                $end_time = microtime(true);
                // Calculate script execution time
                $execution_time = ($end_time - $start_time);
                $this->logger->info("Future Reservations: Execution time of script before flush = ".$execution_time." sec\n");
                $this->entityManager->flush();
                $this->logger->info("Future Reservations: Flushed Entries\n");
                // End clock time in seconds
                $end_time = microtime(true);
                // Calculate script execution time
                $execution_time = ($end_time - $start_time);
                $this->logger->info("Future Reservations: Execution time of script = ".$execution_time." sec\n");

                $this->logger->info("Future Reservations: Send Reservation to Reservation Queue: " . $message['hotelId']);
                // Send reservationsToBeSent to reservation_queue
                $this->rabbitmqService->sendMessagesToQueue($reservationsToBeSent, 'reservation_queue');
                $this->logger->info("Future Reservations: Send Reservations to Queue: " . $message['hotelId']);
                // Update integrationInProgress status in hotelGroup entity
                if(!empty($message['type'] ) && $message['type']  == 'creation'){
                    $hotelGroup->setCreationDateIntegrationInProgress(false);
                } else {
                    $hotelGroup->setFutureReservationsIntegrationInProgress(false);
                }
                $hotelGroup->setModified(time());
                $this->entityManager->flush();
                $this->entityManager->clear();
                $this->logger->info("Future Reservations: Integration Completed: " . $message['hotelId']);
                // Message acknowledgments: Send basic_ack to queue
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $this->logger->info("Future Reservations: Acknowledge for ".$hotelGroup->getGroupName()." was sent to queue!");
            } catch (EmptyContentException $exception) {
                $this->logger->warning('Future Reservations: Get empty content for the Future Reservations request of ' . $hotelGroup->getGroupName() . ' hotel! with error: ' . $exception->getMessage());
                // Update FutureReservationsIntegrationInProgress status in hotelGroup entity
                if(!empty($message['type'] ) && $message['type']  == 'creation'){
                    $hotelGroup->setCreationDateIntegrationInProgress(false);
                } else {
                    $hotelGroup->setFutureReservationsIntegrationInProgress(false);
                }
                $hotelGroup->setModified(time());
                $this->entityManager->flush();
                $this->entityManager->clear();
                // Message acknowledgments: Send basic_ack to queue
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $this->logger->info("Future Reservations: Acknowledge for ".$hotelGroup->getGroupName()." was sent to queue!");
            } catch (Exception $e) {
                $this->logger->error('Future Reservations: ' . $e->getMessage());
                $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
                throw $e;
            }
        };

        // Fair dispatch
        $channel->basic_qos(NULL, 1, NULL);
        $channel->basic_consume('future_reservations_queue', '', FALSE, FALSE, FALSE, FALSE, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * Check if two Reservations differ in any field.
     *
     * @param FutureReservation $r1
     * @param FutureReservation $r2
     * @return bool
     */
    private function isEqual(FutureReservation $r1, FutureReservation $r2): bool
    {
        //TODO Comment out MemberNo && MemberLevel if eurotel fixed multiple entries with same reservation_id
        return (
            $r1->getReservationId() == $r2->getReservationId() &&
            $r1->getConfirmationNo() == $r2->getConfirmationNo() &&
            $r1->getNameId() == $r2->getNameId() &&
            $r1->getTitle() == $r2->getTitle() &&
            $r1->getLast() == $r2->getLast() &&
            $r1->getFirst() == $r2->getFirst() &&
//            $r1->getMemberNo() == $r2->getMemberNo() &&
//            $r1->getMemberLevel() == $r2->getMemberLevel() &&
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
            $r1->getStatus() == $r2->getStatus() &&
            $r1->getCrsNo() == $r2->getCrsNo() &&
            $r1->getPostingAllowedYN() == $r2->getPostingAllowedYN() &&
            $r1->getCustomReference() == $r2->getCustomReference() &&
            $r1->getInsertDate() == $r2->getInsertDate()
        );
    }
}