<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:35
 */

namespace GA\BrokerAPI\Common;

use GA\BrokerAPI\Handlers\HandlerContract;
use GA\BrokerAPI\Infrastructure\BrokerClientContract;
use GA\BrokerAPI\Messages\MessageContract;
use GA\BrokerAPI\Applications\ApplicationContract;

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
     * @param array $config
     * @return HandlerContract
     */
    public function createHandler(array $config = []): HandlerContract;

    /**
     * @param MessageContract $message
     * @param array $settings
     * @return MessageContract
     */
    public function createMessage(
        MessageContract $message,
        array $settings = []
    ): MessageContract;
}
