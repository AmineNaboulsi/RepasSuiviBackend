<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhrabbitmqost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'user'),
            env('RABBITMQ_PASSWORD', 'password'));
        $this->channel = $this->connection->channel();
    }

    public function publish($queue, $data)
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $this->channel->basic_publish($msg, '', $queue);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
