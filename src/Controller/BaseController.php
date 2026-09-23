<?php

namespace App\Controller;

use App\Utils\Logger\CloudLogger;
use Google\Cloud\Logging\PsrLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 * Provides common features needed in controllers.
 *
 */
abstract class BaseController extends AbstractController
{
    /**
     * @var PsrLogger
     */
    protected PsrLogger $logger;

    public function __construct(CloudLogger $logger)
    {
        $this->logger = $logger->psrLogger('eurotel-integration');
    }
}
