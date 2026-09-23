<?php

namespace App\Controller;

use App\Entity\HotelGroup;
use App\Service\EurotelService;
use App\Service\RabbitmqService;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class EurotelController extends BaseController
{
    /**
     * @Route("/get-inhouse-guests", name="get_inhouse_guests")
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @return Response
     */
    #[Route("/get-inhouse-guests", name:"get_inhouse_guests")]
    public function getInhouseGuests(EntityManagerInterface $entityManager, RabbitmqService $rabbitmqService): Response
    {
        try {
            $this->logger->info('EurotelController: getInhouseGuests() started!');

            $hotelGroups = $entityManager->getRepository(HotelGroup::class)->findHotelGroupsReadyToBeProcessed();
            // Set 'integrationInProgressStatus' to 'true' in each hotel
            foreach ($hotelGroups as $hotelGroup) {
                $hotelGroup->setIntegrationInProgress(true);
            }
            $entityManager->flush();
            // Add each hotel to 'hotel queue'
            foreach ($hotelGroups as $hotelGroup) {
                $this->logger->info('EurotelController (getInhouseGuests): Publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue!');
                if (!$rabbitmqService->sendOneMessageToQueue($hotelGroup->getId(), 'inhouse_hotel_queue')) {
                    $hotelGroup->setIntegrationInProgress(false);
                    $this->logger->error('EurotelController (getInhouseGuests): Failed to publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue');
                }
            }
            $entityManager->flush();

            $response = new Response(
                json_encode(['status' => 'OK']),
                Response::HTTP_OK, ['Content-Type' => 'application/json']
            );
        } catch (Throwable $e) {
            $this->logger->error('EurotelController (getInhouseGuests): ' . $e->getMessage());
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @Route("/get-future-reservations", name="get-future-reservations")
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @return Response
     */
    #[Route("/get-future-reservations", name:"get-future-reservations")]
    public function getFutureReservations(EntityManagerInterface $entityManager, RabbitmqService $rabbitmqService): Response
    {
        try {
            $this->logger->info('EurotelController: getFutureReservations() started!');

            $hotelGroups = $entityManager->getRepository(HotelGroup::class)->findHotelGroupsReadyToBeProcessedForFutureReservations();
            // Set 'integrationInProgressStatus' to 'true' in each hotel
            foreach ($hotelGroups as $hotelGroup) {
                $hotelGroup->setFutureReservationsIntegrationInProgress(true);
            }
            $entityManager->flush();

            // Add each hotel to 'hotel queue'
            foreach ($hotelGroups as $hotelGroup) {
                /**
                 * Create a message for the futureReservationsConsumer with the hotelGroupId
                 * and a specified time interval for which we request the transactions (one day)
                 */
                $message = [
                    'hotelId' => $hotelGroup->getId(),
                    'fromDate' => strtotime("today +0000"),
                    'toDate' => strtotime("tomorrow +" . $hotelGroup->getDaysAhead()  ." days +0000"),
                    'type' => 'future',
                ];
                $this->logger->info('EurotelController (getFutureReservations): Publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue!');
                if (!$rabbitmqService->sendOneMessageToQueue(json_encode($message), 'future_reservations_queue')) {
                    $hotelGroup->setFutureReservationsIntegrationInProgress(false);
                    $this->logger->error('EurotelController (getFutureReservations): Failed to publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue');
                }
            }
            $entityManager->flush();

            $response = new Response(
                json_encode(['status' => 'OK']),
                Response::HTTP_OK, ['Content-Type' => 'application/json']
            );
        } catch (Throwable $e) {
            $this->logger->error('EurotelController (getFutureReservations): ' . $e->getMessage());
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @Route("/get-reservations-by-creation-date", name="get-reservations-by-creation-date")
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @return Response
     */
    #[Route("/get-reservations-by-creation-date", name:"get-reservations-by-creation-date")]
    public function getReservationsByCreateDate(EntityManagerInterface $entityManager, RabbitmqService $rabbitmqService): Response
    {
        try {
            $this->logger->info('EurotelController: getFutureReservations() started!');

            $hotelGroups = $entityManager->getRepository(HotelGroup::class)->findHotelGroupsReadyToBeProcessedForCreationDateReservations();
            // Set 'integrationInProgressStatus' to 'true' in each hotel
            foreach ($hotelGroups as $hotelGroup) {
                $hotelGroup->setCreationDateIntegrationInProgress(true);
            }
            $entityManager->flush();

            // Add each hotel to 'hotel queue'
            foreach ($hotelGroups as $hotelGroup) {
                /**
                 * Create a message for the futureReservationsConsumer with the hotelGroupId
                 * and a specified time interval for which we request the transactions (one day)
                 */
                $message = [
                    'hotelId' => $hotelGroup->getId(),
                    'fromDate' => strtotime("today +0000"),
                    'toDate' => strtotime("today 23:59:59"),
                    'type' => 'creation',
                ];
                $this->logger->info('EurotelController (getFutureReservations): Publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue!');
                if (!$rabbitmqService->sendOneMessageToQueue(json_encode($message), 'future_reservations_queue')) {
                    $hotelGroup->setCreationDateIntegrationInProgress(false);
                    $this->logger->error('EurotelController (getFutureReservations): Failed to publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue');
                }
            }
            $entityManager->flush();

            $response = new Response(
                json_encode(['status' => 'OK']),
                Response::HTTP_OK, ['Content-Type' => 'application/json']
            );
        } catch (Throwable $e) {
            $this->logger->error('EurotelController (getFutureReservations): ' . $e->getMessage());
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @Route("/get-transactions", name="get_transactions")
     * @param EntityManagerInterface $entityManager
     * @param RabbitmqService $rabbitmqService
     * @return Response
     */
    #[Route("/get-transactions", name:"get_transactions")]
    public function getTransactions(EntityManagerInterface $entityManager, RabbitmqService $rabbitmqService): Response
    {
        try {
            $this->logger->info('EurotelController: getTransactions() started!');

            $hotelGroups = $entityManager->getRepository(HotelGroup::class)->findHotelGroupsWithTransactionsEnabled();
            foreach ($hotelGroups as $hotelGroup) {
                $this->logger->info('EurotelController (getTransactions): Publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue!');
                /**
                 * Create a message for the transactionConsumer with the hotelGroupId
                 * and a specified time interval for which we request the transactions (one day)
                 */
                $message = [
                    'hotelId' => $hotelGroup->getId(),
                    'fromDate' => strtotime("yesterday +0000"),
                    'toDate' => strtotime("today +0000")
                ];
                if (!$rabbitmqService->sendOneMessageToQueue(json_encode($message), 'checkout_hotel_queue')) {
                    $this->logger->error('EurotelController (getTransactions): Failed to publish HotelGroup with id ' . $hotelGroup->getId() . ' to hotel queue');
                }
            }

            $response = new Response(
                json_encode(['status' => 'OK']),
                Response::HTTP_OK, ['Content-Type' => 'application/json']
            );
        } catch (Throwable $e) {
            $this->logger->error('EurotelController (getTransactions): ' . $e->getMessage());
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @Route("/get-room-reservation", name="get_room_reservations")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param EurotelService $eurotelService
     * @return Response
     */
    #[Route("/get-room-reservation", name:"get_room_reservation_by_criteria")]
    public function getReservation(Request $request, EntityManagerInterface $entityManager, EurotelService $eurotelService): Response
    {
        $responsePayload = [
            'status' => 'OK',
            'reservationId' => null,
            'confirmationNo' => null,
            'crsNo' => null,
            'customReference' => null,
            'message' => 'SUCCESS'
        ];
        try {
            $this->logger->info('EurotelController: getRoomReservations() Request: ' . $request->getContent());
            $payload = json_decode($request->getContent());

            /** @var HotelGroup $hotelGroup */
            $hotelGroup = $entityManager->getRepository(HotelGroup::class)->findOneBy([
                "tenant" => $payload->groupName,
            ]);
            if(empty($hotelGroup)){
                return new Response(
                    json_encode(['status' => 'OK', 'reservationId' => null, 'confirmationNo' => null, 'crsNo' => null, 'customReference' => null]),
                    Response::HTTP_OK, ['Content-Type' => 'application/json']
                );
            }

            $startDate = new DateTime($payload->checkIn, new DateTimeZone($payload->hotelTimeZone));
            $startDate->setTimezone(new DateTimeZone('UTC'));
            $endDate = new DateTime($payload->checkIn, new DateTimeZone($payload->hotelTimeZone));
            $endDate->add(new DateInterval('P1D'));
            $endDate->setTimezone(new DateTimeZone('UTC'));

            $reservations = json_decode($eurotelService->getFutureReservations($hotelGroup, $startDate->format('U'), $endDate->format('U')));
            if(!empty($reservations)) {
                foreach ($reservations as $reservation) {
                    if(
                        $reservation->ARRIVAL_DATE == $payload->checkIn . 'T00:00:00Z'
                        && $reservation->DEPARTURE_DATE == $payload->checkOut . 'T00:00:00Z'
                        && $hotelGroup->getTenant() . '-' . strtolower($reservation->RESORT) == $payload->hotelId
                        && !empty($payload->bookingReferenceId)
                        && (
                            $reservation->RESERVATION_ID == $payload->bookingReferenceId
                            || $reservation->CONFIRMATION_NO == $payload->bookingReferenceId
                            || $reservation?->CRS_NO == $payload->bookingReferenceId
                            || $reservation?->CUSTOM_REFERENCE == $payload->bookingReferenceId
                        )
                    ) {
                        $responsePayload['reservationId'] = $reservation?->RESERVATION_ID;
                        $responsePayload['confirmationNo'] = $reservation?->CONFIRMATION_NO;
                        $responsePayload['crsNo'] = $reservation?->CRS_NO;
                        $responsePayload['customReference'] = $reservation?->CUSTOM_REFERENCE;
                        $this->logger->info('EurotelController: sendToTitan() Response: ' . $eurotelService->sendToTitan($hotelGroup, $reservation, $payload->hotelId));
                        $eurotelService->sendToTitan($hotelGroup, $reservation, $payload->hotelId);
                        break;
                    }
                }
            }

            $response = new Response(
                json_encode(
                    $responsePayload,
                ),
                Response::HTTP_OK, ['Content-Type' => 'application/json']
            );
        } catch (Throwable $e) {
            $this->logger->error('EurotelController (getRoomReservations): ' . $e->getMessage());
            $responsePayload['status'] = 'ERROR';
            $responsePayload['message'] = 'EurotelController (getReservation): ' . $e->getMessage();
            $response = new Response(
                json_encode(
                    $responsePayload
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $response;
    }
}
