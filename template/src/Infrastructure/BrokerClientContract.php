<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:11
 */

namespace GA\BrokerAPI\Infrastructure;

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
    public function publish($message, array $config = []): bool;

    /**
     * @param array $config
     * @return bool
     */
    public function consume(array $config = []): bool;
}
