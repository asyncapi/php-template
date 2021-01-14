<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 14:37
 */

namespace {{ params.packageName }}\BrokerAPI\Infrastructure;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;

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
     * @param MessageContract $message
     * @param array $config
     * @return bool
     */
    public function publishToExchange(MessageContract $message, array $config = []): bool
    {
        /**
         * @var bool|null $mandatory
         * @var bool|null $immediate
         * @var $ticket
         * @var string $exchangeName
         * @var string $exchangeType
         * @var string $bindingKey
         */
        extract($config);
        $this->connect();
        $this->channel->exchange_declare(
            $exchangeName,
            $exchangeType,
            $passive ?? false,
            $durable ?? false,
            $autoDelete ?? true,
            $internal ?? false,
            $noWait ?? false,
            $arguments ?? [],
            $ticket ?? null
        );
        /** @var \PhpAmqpLib\Message\AMQPMessage $amqpMessage */
        $amqpMessage = $message->getPayload();
        $this->channel->basic_publish(
            $amqpMessage,
            $exchangeName ?? '',
            $bindingKey ?? '',
            $mandatory ?? false,
            $immediate ?? false,
            $ticket ?? null
        );
        $this->channel->close();
        $this->connection->close();
        return true;
    }

    /**
     * Basic consume function will default to topic through exchange with binding keys.
     * If other types of consumption are needed, refactor is needed.
     * Refactoring this functions is easy tho, please follow TDD best practices in order to do so
     *
     * @param array $config
     * @return bool
     */
    public function consumeThroughExchange(array $config = []): bool
    {
        /**
         * @var string|null $consumerTag
         * @var bool|null $noLocal
         * @var bool|null $noAck
         * @var bool|null $exclusive
         * @var bool|null $noWait
         * @var $callback
         * @var $ticket
         * @var array|null $arguments
         * @var string $exchangeName
         * @var string $exchangeType
         * @var string $bindingKey
         * @var bool $exchangeDurable
         * @var bool $queueDurable
         * @var bool $autoDelete
         */
        extract($config);
        $this->connect();
        $this->channel->exchange_declare(
            $exchangeName,
            $exchangeType,
            false,
            $exchangeDurable ?? false,
            $autoDelete ?? true,
            false,
            $noWait ?? false,
            $arguments ?? [],
            $ticket ?? null
        );
        list($queueName) = $this->channel->queue_declare(
            "",
            false,
            $queueDurable ?? false,
            $autoDelete ?? true,
            false
        );
        $this->channel->queue_bind($queueName, $exchangeName, $bindingKey);
        $this->channel->basic_consume(
            $queueName,
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
