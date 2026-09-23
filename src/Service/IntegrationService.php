<?php

namespace App\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class IntegrationService
{
    /**
     * @var Client
     */
    private Client $integrationClient;

    /**
     * IntegrationService constructor.
     */
    public function __construct()
    {
        $this->integrationClient = new Client();
    }

    /**
     * This function gets an eurotel reservation as json and post it to the
     * integration service (/api/eurotel) using the eurotel jwt
     *
     * @param $payload
     *
     * @return Response
     *
     * @throws GuzzleException
     */
    public function postReservation($payload): Response
    {
        $url = $_ENV['INTEGRATION_URL'] . '/api/eurotel';
        $bearer = $_ENV['EUROTEL_TOKEN'];

        return $this->integrationClient->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearer,
                'Content-Type' => 'application/json'
            ],
            'body' => $payload
        ]);
    }

    /**
     * This function gets an eurotel transaction as json and post it to the
     * integration service (/api/eurotel/transactions) using the eurotel jwt
     *
     * @param $payload
     *
     * @return Response
     *
     * @throws GuzzleException
     */
    public function postTransaction($payload): Response
    {
        $url = getenv('INTEGRATION_URL') . '/api/eurotel/transactions';
        $bearer = getenv('EUROTEL_TOKEN');

        return $this->integrationClient->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearer,
                'Content-Type' => 'application/json'
            ],
            'body' => $payload
        ]);
    }
}