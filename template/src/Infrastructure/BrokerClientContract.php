<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:11
 */

namespace {{ params.packageName }}\BrokerAPI\Infrastructure;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Handlers\RPC\RPCHandlerContract;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;


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
     * @param RPCHandlerContract $handler
     * @param array $config
     * @return mixed
     */
    public function rpcPublish(
        MessageContract $message,
        RPCHandlerContract $handler,
        array $config = []
    );

    /**
     * @param RPCHandlerContract $handler
     * @param array $config
     * @return mixed
     */
    public function rpcConsume(RPCHandlerContract $handler, array $config = []);
}
