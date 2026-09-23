<?php

namespace App\Service;


use App\Entity\HotelGroup;
use App\Utils\Logger\CloudLogger;
use DateInterval;
use DateInvalidOperationException;
use DateMalformedIntervalStringException;
use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Google\Cloud\Logging\PsrLogger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EurotelService
{
    /**
     * @var Client
     */
    private Client $eurotelClient;

    /** @var PsrLogger */
    private PsrLogger $logger;

    public function __construct(CloudLogger $logger)
    {
        $this->logger = $logger->psrLogger('eurotel-integration');
        $this->eurotelClient = new Client([
            'timeout' => 120.0
        ]);
    }

    /**
     * @param HotelGroup $hotelGroup
     * @return string
     * @throws GuzzleException
     */
    public function getAuthenticationBearer(HotelGroup $hotelGroup): string
    {
        $loginUrl = $hotelGroup->getBaseUrl() . $hotelGroup->getLoginUrl();
        $response = $this->eurotelClient->request('POST', $loginUrl, [
            "verify" => false,
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'username' => $hotelGroup->getUsername(),
                'password' => $hotelGroup->getPassword()
            ]
        ]);

        $response = json_decode($response->getBody(), true);
        return $response['sessionKey'];
    }

    /**
     * @param HotelGroup $hotelGroup
     * @return string
     * @throws GuzzleException
     */
    public function getInhouseGuests(HotelGroup $hotelGroup): string
    {
        $this->logger->info("InhouseConsumer: Getting InHouseGuests: " . $hotelGroup->getId());
        if($hotelGroup->getMh3AsProxyV2()){
            $url = $hotelGroup->getBaseUrl();
            $logIn = json_decode($this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getLoginUrl(),
                    'method' => 'POST',
                    'isLogin' => 1,
                    'bearerName' => '',
                    'token' => '',
                    'payload' => [
                        'username' => $hotelGroup->getUsername(),
                        'password' => $hotelGroup->getPassword()
                    ]
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ])->getBody()->getContents());
            $response = $this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getInhouseUrl(),
                    'method' => 'PUT',
                    'isLogin' => 0,
                    'bearerName' => 'Authorization',
                    'token' => 'Bearer ' . $logIn->sessionKey,
                    'payload' => []
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ]);
        } elseif ($hotelGroup->getMh3AsProxy()) {
            $url = $hotelGroup->getBaseUrl();
            $response = $this->eurotelClient->request('GET', $url,[
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ]);
        } else {
            $url = $hotelGroup->getBaseUrl() . $hotelGroup->getInhouseUrl();
            $bearer = $this->getAuthenticationBearer($hotelGroup);

            $response = $this->eurotelClient->request('PUT', $url, [
                'headers' => [
                    'accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $bearer,
                    'Content-Type' => 'application/json'
                ],
                'body' => '[]',
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ]);
        }
        $this->logger->info("InhouseConsumer: Got InHouseGuests: " . $hotelGroup->getId());

        return $response->getBody()->getContents();
    }

    /**
     * @param HotelGroup $hotelGroup
     * @param string $fromDate
     * @param string $toDate
     * @return string
     * @throws GuzzleException
     * @throws DateMalformedStringException
     */
    public function getFutureReservations(HotelGroup $hotelGroup, string $fromDate, string $toDate): string
    {
        $this->logger->info("FutureReservationsConsumer: Getting FutureReservations: " . $hotelGroup->getId());
        if($hotelGroup->getMh3AsProxyV2()){
            $url = $hotelGroup->getBaseUrl();
            $logIn = json_decode($this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getLoginUrl(),
                    'method' => 'POST',
                    'isLogin' => 1,
                    'bearerName' => '',
                    'token' => '',
                    'payload' => [
                        'username' => $hotelGroup->getUsername(),
                        'password' => $hotelGroup->getPassword()
                    ]
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ])->getBody()->getContents());

            $fromDate = substr(date('c', $fromDate),0, 11).'00:00:00Z';
            $toDate = substr(date('c', $toDate), 0, 11).'00:00:00Z';
            $response = $this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getFutureReservationsUrl(),
                    'method' => 'PUT',
                    'isLogin' => 0,
                    'bearerName' => 'Authorization',
                    'token' => 'Bearer ' . $logIn->sessionKey,
                    'payload' => [
                        // Costa
                        [
                            "key" => "datefrom",
                            "value" => $fromDate,
                        ],
                        [
                            "key" => "dateto",
                            "value" => $toDate
                        ],
                        // Daios
                        [
                            "key" => "date_from",
                            "value" => $fromDate,
                        ],
                        [
                            "key" => "date_to",
                            "value" => $toDate
                        ]
                    ]
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ]);
            $this->logger->info("FutureReservations: Getting FutureReservations: from=" . $fromDate . " to=" . $toDate . " Got FutureReservations: " . $hotelGroup->getId());
        } else if ($hotelGroup->getMh3AsProxy()) {
            //TODO create endpoint in titan. Currently mh3 as proxy not supported
            $url = $hotelGroup->getFutureReservationsUrl();
            $response = $this->eurotelClient->request('GET', $url);
        } else {
            $url = $hotelGroup->getBaseUrl() . $hotelGroup->getFutureReservationsUrl();
            $bearer = $this->getAuthenticationBearer($hotelGroup);
            $fromDate = new DateTime(substr(date('c', $fromDate),0, 11).'00:00:00Z');
            $toDate = new DateTime(substr(date('c', $toDate), 0, 11).'00:00:00Z');

            $response = $this->eurotelClient->request('PUT', $url, [
                'headers' => [
                    'accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $bearer,
                    'Content-Type' => 'application/json'
                ],
                'body' => '[{"Key":"date_from","Value":"'.$fromDate->format('d-m-Y').'"},{"Key":"date_to","Value":"'.$toDate->format('d-m-Y').'"}]'
            ]);
            $this->logger->info("FutureReservations: Getting FutureReservations: from=" . $fromDate->format('d-m-Y') . " to=" . $toDate->format('d-m-Y') . " Got FutureReservations: " . $hotelGroup->getId());
        }
        $this->logger->info("FutureReservationsConsumer: Got FutureReservations: " . $hotelGroup->getId());

        return $response->getBody()->getContents();
    }

    /**
     * @param HotelGroup $hotelGroup
     * @param string $fromDate
     * @param string $toDate
     * @return string
     * @throws GuzzleException
     * @throws DateMalformedStringException
     */
    public function getInsertDateReservations(HotelGroup $hotelGroup, string $fromDate, string $toDate): string
    {
        $this->logger->info("InsertDateReservations: Getting InsertDateReservations: " . $hotelGroup->getId());
        if($hotelGroup->getMh3AsProxyV2()){
            $url = $hotelGroup->getBaseUrl();
            $logIn = json_decode($this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getLoginUrl(),
                    'method' => 'POST',
                    'isLogin' => 1,
                    'bearerName' => '',
                    'token' => '',
                    'payload' => [
                        'username' => $hotelGroup->getUsername(),
                        'password' => $hotelGroup->getPassword()
                    ]
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ])->getBody()->getContents());

            $fromDate = substr(date('c', $fromDate),0, 11).'00:00:00Z';
            $toDate = substr(date('c', $toDate), 0, 11).'00:00:00Z';
            $response = $this->eurotelClient->request('POST', $url,[
                'body' => json_encode([
                    'url' => $hotelGroup->getCreationDateReservationsUrl(),
                    'method' => 'PUT',
                    'isLogin' => 0,
                    'bearerName' => 'Authorization',
                    'token' => 'Bearer ' . $logIn->sessionKey,
                    'payload' => [
                        [
                            "key" => "datefrom",
                            "value" => $fromDate,
                        ],
                        [
                            "key" => "dateto",
                            "value" => $toDate
                        ]
                    ]
                ]),
                "verify" => false,
                'timeout' => 120.0,
                'connect_timeout' => 120.0,
            ]);
            $this->logger->info("InsertDateReservations: Got InsertDateReservations: from=" . $fromDate . " to=" . $toDate . " Got FutureReservations: " . $hotelGroup->getId());
        } else if ($hotelGroup->getMh3AsProxy()) {
            //TODO create endpoint in titan. Currently mh3 as proxy not supported
            $url = $hotelGroup->getCreationDateReservationsUrl();
            $response = $this->eurotelClient->request('GET', $url);
        } else {
            $url = $hotelGroup->getBaseUrl() . $hotelGroup->getCreationDateReservationsUrl();
            $bearer = $this->getAuthenticationBearer($hotelGroup);
            $fromDate = new DateTime(substr(date('c', $fromDate),0, 11).'00:00:00Z');
            $toDate = new DateTime(substr(date('c', $toDate), 0, 11).'00:00:00Z');

            $response = $this->eurotelClient->request('PUT', $url, [
                'headers' => [
                    'accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $bearer,
                    'Content-Type' => 'application/json'
                ],
                'body' => '[{"Key":"date_from","Value":"'.$fromDate->format('d-m-Y').'"},{"Key":"date_to","Value":"'.$toDate->format('d-m-Y').'"}]'
            ]);
            $this->logger->info("InsertDateReservations: Got InsertDateReservations: from=" . $fromDate->format('d-m-Y') . " to=" . $toDate->format('d-m-Y') . " Got FutureReservations: " . $hotelGroup->getId());
        }
        $this->logger->info("InsertDateReservations: Got InsertDateReservations: " . $hotelGroup->getId());

        return $response->getBody()->getContents();
    }

    /**
     * @param HotelGroup $hotelGroup
     * @param string $fromDate
     * @param string $toDate
     * @return string
     * @throws GuzzleException
     * @throws DateMalformedIntervalStringException
     * @throws DateInvalidOperationException
     * @throws DateMalformedStringException
     */
    public function getTransactions(HotelGroup $hotelGroup,string $fromDate,string $toDate): string
    {
        $url = $hotelGroup->getBaseUrl() . $hotelGroup->getCheckoutUrl();
        $bearer = $this->getAuthenticationBearer($hotelGroup);
        $fromDate = substr(date('c', $fromDate),0, 11).'00:00:00Z';
        $toDate = new DateTime(substr(date('c', $toDate), 0, 11).'00:00:00Z');

        //Set timezone for the fromDate datetime
        if(intval($hotelGroup->getUtfOffset()) < 0) {
            $offset = -($hotelGroup->getUtfOffset());
            $from = DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $fromDate);
            $from->setTimezone(new DateTimeZone('UTC'));
            $from->sub(new DateInterval('PT' . $offset .'H'));
            $fromDate = $from->format("d-m-Y");
        } else {
            $from = DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $fromDate);
            $from->setTimezone(new DateTimeZone('UTC'));
            $from->add(new DateInterval('PT' . $hotelGroup->getUtfOffset() .'H'));
            $fromDate = $from->format("d-m-Y");
        }
        $toDate = $toDate->format("d-m-Y");

        $response = $this->eurotelClient->request('PUT', $url, [
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearer,
                'Content-Type' => 'application/json'
            ],
            'body' => '[{"Key":"date_from","Value":"'.$fromDate.'"},{"Key":"date_to","Value":"'.$toDate.'"}]'
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * This function is used from the PastDataFeedCommand so to get
     * past in-house guests to initialize a hotel-group with data
     *
     * @param HotelGroup $hotelGroup
     * @param string $fromDate
     * @param string $toDate
     * @return string
     * @throws GuzzleException
     */
    public function getInhouseGuestsFromTo(HotelGroup $hotelGroup,string $fromDate,string $toDate): string
    {
        $fromDate = substr(date('c', $fromDate),0, 11).'00:00:00Z';
        $toDate = substr(date('c', $toDate), 0, 11).'00:00:00Z';

        $url = $hotelGroup->getBaseUrl() . $hotelGroup->getInhouseUrlFromTo();
        $bearer = $this->getAuthenticationBearer($hotelGroup);

        $response = $this->eurotelClient->request('PUT', $url, [
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearer,
                'Content-Type' => 'application/json'
            ],
            'body' => '[{"Key":"date_from","Value":"' . $fromDate . '"},{"Key":"date_to","Value":"' . $toDate . '"}]'
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @throws GuzzleException
     */
    public function sendToTitan(HotelGroup $hotelGroup, $payload , string $hotelId): string
    {
        $response = $this->eurotelClient->request('POST', $hotelGroup->getTitanReservationUrl(), [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'id' => null,
                'resID' => (string) $payload->RESERVATION_ID,
                'hotelID' => $hotelId,
                'guestInfos' => [[
                    'id'=> null,
                    'firstName' => $payload->FIRST,
                    'lastName' => $payload->LAST,
                    'pmsIntegrationData' => null,
                    'birthday' => $payload->BIRTHDATE,
                    'postalCode' => $payload->POSTAL_CODE,
                    'isMaster' => true,
                    'guestID' => (string) $payload->NAME_ID,
                    'gender' => !empty($payload->GENDER) ? $this->getGender($payload->GENDER) : null,
                    'title' => $payload->TITLE,
                    'email' => $payload->EMAIL,
                    'preferences' => $payload->PREFERENCES,
                    'nationalityCode' => $payload->NATIONALITY,
                    'street' => $payload->ADDRESS,
                    'countryCode' => $payload->COUNTRY,
                    'vip' => !empty($payload->VIP),
                    'phone' => $payload->MOBILE_PHONE,
                    'loyaltyInfos' => [],
                    'documents' => [],
                    'metadata' => []
                ]],
                'reservationPlans' => $this->buildReservationPlans($payload->ARRIVAL_DATE, $payload->DEPARTURE_DATE, $payload->ROOM_NO, $payload->ROOM_TYPE, $payload->RATE_CODE),
                'arrival' => isset($payload->ARRIVAL_DATE) ?  DateTime::createFromFormat('Y-m-d+', $payload->ARRIVAL_DATE)->format('Y-m-d') : null,
                'departure' => isset($payload->DEPARTURE_DATE) ? DateTime::createFromFormat('Y-m-d+', $payload->DEPARTURE_DATE)->format('Y-m-d') : null,
                'marketCode' => $payload->MARKET_CODE,
                'marketSource' => $payload->SOURCE_CODE,
                'travelAgentName' => $payload->TRAVEL_AGENT_NAME,
                'companyName' => $payload->COMPANY_NAME,
                'status' => $this->statusConversion($payload->RESV_STATUS),
                'adults' => 2,
                'children' => 0,
                'created' => time(),
                'transactions' => [],
                'metadata' => [],
                'bookinDate' => isset($payload->INSERT_DATE) ?  DateTime::createFromFormat('Y-m-d+', $payload->ARRIVAL_DATE)->format('Y-m-d') : null,
                'confirmationNumber' => (string) $payload->CONFIRMATION_NO,
                'externalBookingReferenceId' => !empty($payload->CRS_NO) && strtolower($payload->CRS_NO) != strtolower('NULL') ? $payload->CRS_NO : null,
                'PostingAllowedYN' => (string) $payload?->POSTING_ALLOWED_YN,
                'CustomReference' => (string) $payload->CUSTOM_REFERENCE,
            ])
        ]);

        return $response->getBody()->getContents();
    }


    public function getGender(string $gender): string
    {
        return match ($gender) {
            "M" => "male",
            "F" => "female",
            default => "other",
        };
    }

    /**
     * @param $arrivalDate
     * @param $departureDate
     * @param $room
     * @param $roomTypeCode
     * @param $rateCode
     * @return array|null
     */
    private function buildReservationPlans($arrivalDate, $departureDate, $room, $roomTypeCode, $rateCode ): ?array
    {
        if (isset($arrivalDate) && isset($departureDate)) {
            $arrivalDate = DateTime::createFromFormat('Y-m-d+', $arrivalDate);
            $departureDate = DateTime::createFromFormat('Y-m-d+', $departureDate);

            $plans = [];
            $nights = $departureDate->diff($arrivalDate, true)->days;
            $current_date = $arrivalDate->format('Y-m-d');
            $x = 1;
            while ($x <= $nights) {
                $plans[$x] = [
                    'room' => $room,
                    'planDate' => $current_date,
                    'roomTypeCode' => $roomTypeCode,
                    'ratePlanCode' => $rateCode
                ];
                $x++;
                $current_date = date('Y-m-d', strtotime($current_date . ' + 1 days'));
            }
            return $plans;
        }
        return null;
    }

    private function statusConversion(?string $status): string
    {
        return match ($status) {
            "checked-in" => 'checked-in',
            default => $status,
        };
    }
}