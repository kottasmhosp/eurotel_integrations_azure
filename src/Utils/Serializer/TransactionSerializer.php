<?php

namespace App\Utils\Serializer;


use App\Entity\Transaction;
use ArrayObject;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TransactionSerializer extends BaseSerializer
{
    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer()];
        parent::__construct($normalizers, $encoders);
    }

    public function normalize($data, $format = null, array $context = []): float|int|bool|ArrayObject|array|string|null
    {
        /** @var Transaction $data */
        if (get_class($data) == Transaction::class) {
            return [
                'id' => $data->getId(),
                'created' => $data->getCreated(),
                'resort' => $data->getResort(),
                'confirmationNo' => $data->getConfirmationNo(),
                'reservationId' => $data->getReservationId(),
                'nameId' => $data->getNameId(),
                'surname' => $data->getSurname(),
                'name' => $data->getName(),
                'roomNo' => $data->getRoomNo(),
                'arrivalDate' => $data->getArrivalDate(),
                'departureDate' => $data->getDepartureDate(),
                'trxCode' => $data->getTrxCode(),
                'description' => $data->getDescription(),
                'grossAmount' => $data->getGrossAmount(),
                'tcGroup' => $data->getTcGroup(),
                'transactionCategory' => $data->getTransactionCategory(),
                'hotelID' => $this->setHotelId($data->getResort())
            ];
        }

        return parent::normalize($data, $format, $context);
    }

    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        if ($type === Transaction::class) {
            $data['created'] = time();
        }

        return parent::denormalize($data, $type, $format, $context);
    }
}