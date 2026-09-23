<?php

namespace App\Service;

use App\Utils\Logger\CloudLogger;
use Exception;
use Google\Cloud\Logging\PsrLogger;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqService
{
    private PsrLogger $logger;

    public function __construct(CloudLogger $logger)
    {
        $this->logger = $logger->psrLogger('eurotel-integration');
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASSWORD'],
            $_ENV['RABBITMQ_VHOST'],
            false,
            'AMQPLAIN',
            null,
            'en_US',
            300.0,
            310.0,
            null,
            true,
            150.0
        );
    }

    /**
     * @param AMQPStreamConnection $connection
     * @param string $queue Name of the queue
     * @return AMQPChannel
     */
    public function createChannel(AMQPStreamConnection $connection, string $queue): AMQPChannel
    {
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        return $channel;
    }

    /**
     * @param $message
     * @param string $queue
     * @return bool
     */
    public function sendOneMessageToQueue($message, string $queue): bool
    {
        try {
            $connection = $this->getConnection();
            $channel = $connection->channel();
            $channel->queue_declare($queue, false, true, false, false);
            $rabbitmqMessage = new AMQPMessage(
                $message,
                array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
            );
            $channel->basic_publish($rabbitmqMessage, '', $queue);
            $channel->close();
            $connection->close();
            return true;
        } catch (Exception $e) {
            $this->logger->error("RabbitmqService (sendOneMessageToQueue): ".$e->getMessage());
            return false;
        }
    }

    /**
     * @param array $messages
     * @param string $queue
     * @return bool
     */
    public function sendMessagesToQueue(array $messages, string $queue): bool
    {
        try {
            $connection = $this->getConnection();
            $channel = $connection->channel();
            $channel->queue_declare($queue, false, true, false, false);
            foreach ($messages as $message) {
                $rabbitmqMessage = new AMQPMessage(
                    $message,
                    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
                );
                $channel->basic_publish($rabbitmqMessage, '', $queue);
            }
            $channel->close();
            $connection->close();
            return true;
        } catch (Exception $e) {
            $this->logger->error("RabbitmqService (sendMessagesToQueue): ".$e->getMessage());
            return false;
        }
    }
}