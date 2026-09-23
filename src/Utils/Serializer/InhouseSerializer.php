<?php

namespace App\Utils\Serializer;


use App\Entity\Reservation;
use ArrayObject;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class InhouseSerializer extends BaseSerializer
{
    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer()];
        parent::__construct($normalizers, $encoders);
    }

    public function normalize($data, $format = null, array $context = []): float|int|bool|ArrayObject|array|string|null
    {
        /** @var Reservation $data */
        if (get_class($data) == Reservation::class) {
            return [
                'created' => $data->getCreated(),
                'reservationId' => $data->getReservationId(),
                'confirmationNo' => $data->getConfirmationNo(),
                'nameId' => $data->getNameId(),
                'title' => $data->getTitle(),
                'last' => $data->getLast(),
                'first' => $data->getFirst(),
                'memberNo' => $data->getMemberNo(),
                'memberLevel' => $data->getMemberLevel(),
                'vip' => $data->getVip(),
                'lastStay' => $data->getLastStay(),
                'roomNo' => $data->getRoomNo(),
                'roomType' => $data->getRoomType(),
                'adults' => $data->getAdults(),
                'children' => $data->getChildren(),
                'arrivalDate' => $data->getArrivalDate(),
                'departureDate' => $data->getDepartureDate(),
                'email' => $data->getEmail(),
                'mobilePhone' => $data->getMobilePhone(),
                'gender' => $data->getGender(),
                'nationality' => $data->getNationality(),
                'birthdate' => $data->getBirthdate(),
                'address' => $data->getAddress(),
                'city' => $data->getCity(),
                'postalCode' => $data->getPostalCode(),
                'country' => $data->getCountry(),
                'rateCode' => $data->getRateCode(),
                'marketCode' => $data->getMarketCode(),
                'sourceCode' => $data->getSourceCode(),
                'travelAgentName' => $data->getTravelAgentName(),
                'companyName' => $data->getCompanyName(),
                'srcName' => $data->getSrcName(),
                'preferences' => $data->getPreferences(),
                'remarks' => $data->getRemarks(),
                'resort' => $data->getResort(),
                'status' => $data->getStatus(),
                'hotelID' => $this->setHotelId($data->getResort()),
                'CrsNo' => $data->getCrsNo(),
                'PostingAllowedYN' => $data->getPostingAllowedYN(),
                'CustomReference' => $data->getCustomReference(),
                'InsertDate' => $data->getInsertDate(),
            ];
        }

        return parent::normalize($data, $format, $context);
    }

    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        if ($type === Reservation::class) {
            $data['status'] = !empty($data['RESV_STATUS']) ? $this->getStatus($data['RESV_STATUS']) : 'checked-in';
        }

        return parent::denormalize($data, $type, $format, $context);
    }

    private function getStatus($status): string
    {
        return match ($status) {
            "RESERVED" => "reserved",
            "CANCELLED" => "cancelled",
            default => "checked-in",
        };
    }
}