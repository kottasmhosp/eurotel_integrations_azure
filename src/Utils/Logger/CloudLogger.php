<?php

namespace App\Utils\Logger;


use Google\Cloud\Logging\LoggingClient;

class CloudLogger extends LoggingClient
{
    public function __construct(array $config = ['projectId' => 'mhosp-integration'])
    {
        parent::__construct($config);
    }
}