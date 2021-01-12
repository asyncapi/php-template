<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 14:37
 */

namespace GA\BrokerAPI\Infrastructure;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AMQPBrokerClient implements BrokerClientContract
{
    /** @var AMQPStreamConnection $connection */
    private $connection;
    /** @var AMQPChannel $channel */
    private $channel;

    /**
     * AMQPBrokerClient constructor.
     * @param AMQPStreamConnection|null $connection
     */
    public function __construct(
        AMQPStreamConnection $connection = null
    ) {
        $this->setConnection($connection);
    }

    /**
     * @param $connection
     * @return BrokerClientContract
     */
    public function setConnection($connection): BrokerClientContract
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function connect()
    {
        $this->channel = $this->connection->channel();
        return $this->channel;
    }

    /**
     * @param $message
     * @param array $config
     * @return bool
     */
    public function publish($message, array $config = []): bool
    {
        /**
         * @var string|null $exchange
         * @var string|null $routingKey
         * @var bool|null $mandatory
         * @var bool|null $immediate
         * @var $ticket
         */
        extract($config);
        $this->channel->basic_publish(
            $message,
            $exchange ?? '',
            $routingKey ?? '',
            $mandatory ?? false,
            $immediate ?? false,
            $ticket ?? null
        );
        $this->channel->close();
        $this->connection->close();
        return true;
    }

    /**
     * @param array $config
     * @return bool
     */
    public function consume(array $config = []): bool
    {
        /**
         * @var string|null $queue
         * @var string|null $consumerTag
         * @var bool|null $noLocal
         * @var bool|null $noAck
         * @var bool|null $exclusive
         * @var bool|null $noWait
         * @var $callback
         * @var $ticket
         * @var array|null $arguments
         */
        extract($config);
        $this->channel->basic_consume(
            $queue ?? '',
            $consumerTag ?? '',
            $noLocal ?? false,
            $noAck ?? false,
            $exclusive ?? false,
            $noWait ?? false,
            $callback ?? null,
            $ticket ?? null,
            $arguments ?? []
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();

        return true;
    }
}
