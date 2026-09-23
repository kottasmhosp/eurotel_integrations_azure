<?php

namespace App\Utils\Serializer;


use Symfony\Component\Serializer\Serializer;

class BaseSerializer extends Serializer
{
    /**
     * Set hotelID for GIS based on hotelGroup name and resort name
     * If hotelID is not set returns an empty string
     *
     * @param $resort
     * @return string
     */
    protected function setHotelId($resort): string
    {

        if (in_array($resort, ['CRETA', 'SANTO', 'CANDIA', 'CRKITRO'])) {
            $hotelID = strtolower( "marisgroup-" . $resort);
        } else if (in_array($resort, ['KLXLC', 'KLXWH', 'KLXWI', 'NDRES'])) {
            $hotelID = strtolower( "costanavarino-" . $resort);
        } else if (in_array($resort, ['EKF', 'EMA', 'EPA', 'EPR', 'EPS', 'EHA'])) {
            $hotelID = strtolower( "electra-" . $resort);
        } else {
            $hotelID = "1";
        }
        return $hotelID;
    }
}