<?php
/**
 * Interface for all Broker Clients.
 * Broker clients are an abstraction for any underlying library used to communicate with brokers
 * This abstraction is a Wrapper
 * For AMQP => PHP AMQP Lib
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:11
 */

namespace {{ params.packageName }}\BrokerAPI\Infrastructure;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Handlers\AMQPRPCServerHandler;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;
use {{ params.packageName }}\BrokerAPI\Common\FactoryContract;


interface BrokerClientContract
{
    /**
     * @param $connection
     * @return BrokerClientContract
     */
    public function setConnection($connection): BrokerClientContract;

    public function getConnection();

    public function connect();

    public function close();

    public function setFactory(FactoryContract $factory): BrokerClientContract;

    public function getFactory(): FactoryContract;

    /**
     * @param $message
     * @param array $config
     * @return bool
     */
    public function basicPublish(MessageContract $message, array $config = []): bool;

    /**
     * @param HandlerContract $handler
     * @param array $config
     * @return bool
     */
    public function basicConsume(HandlerContract $handler, array $config = []): bool;

    /**
     * @param MessageContract $message
     * @param array $config
     * @return mixed
     */
    public function rpcPublish(
        MessageContract $message,
        array $config = []
    );

    /**
     * @param AMQPRPCServerHandler $handler
     * @param array $config
     * @return mixed
     */
    public function rpcConsume(AMQPRPCServerHandler $handler, array $config = []);
}
