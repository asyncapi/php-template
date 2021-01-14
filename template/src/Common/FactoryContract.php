<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:35
 */

namespace {{ params.packageName }}\BrokerAPI\Common;

use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;
use {{ params.packageName }}\BrokerAPI\Infrastructure\BrokerClientContract;
use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Applications\ApplicationContract;

interface FactoryContract
{
    /**
     * @param array $config
     * @param null $brokerConnection
     * @return BrokerClientContract
     */
    public function createBrokerClient(
        array $config = []
    ): BrokerClientContract;

    /**
     * @param string $applicationType
     * @param array $config
     * @return ApplicationContract
     */
    public function createApplication(
        string $applicationType,
        array $config = []
    ): ApplicationContract;

    /**
     * @param string $handlerType
     * @param array $config
     * @return HandlerContract
     */
    public function createHandler(
        string $handlerType,
        array $config = []
    ): HandlerContract;

    /**
     * @param $messageType
     * @param array $properties
     * @return MessageContract
     */
    public function createMessage(
        $messageType,
        array $properties = []
    ): MessageContract;
}
