<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:11
 */

namespace {{ params.packageName }}\BrokerAPI\Infrastructure;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;

interface BrokerClientContract
{
    /**
     * @param $connection
     * @return BrokerClientContract
     */
    public function setConnection($connection): BrokerClientContract;

    public function getConnection();

    public function connect();

    /**
     * @param $message
     * @param array $config
     * @return bool
     */
    public function publishToExchange(MessageContract $message, array $config = []): bool;

    /**
     * @param array $config
     * @return bool
     */
    public function consumeThroughExchange(array $config = []): bool;
}
