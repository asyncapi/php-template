<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 7/1/21
 * Time: 14:38
 */

namespace GA\BrokerAPI\Applications;

use GA\BrokerAPI\Common\FactoryContract;
use GA\BrokerAPI\Handlers\HandlerContract;
use GA\BrokerAPI\Infrastructure\BrokerClientContract;

abstract class ApplicationContract
{
    /** @var BrokerClientContract $brokerClient */
    private $brokerClient;
    /** @var FactoryContract $factory */
    private $factory;

    /**
     * ApplicationContract constructor.
     * @param BrokerClientContract $brokerClient
     * @param HandlerContract|null $handler
     */
    public function __construct(
        BrokerClientContract $brokerClient,
        FactoryContract $factory = null
    ) {
        $this->setBrokerClient($brokerClient);

        if (!is_null($factory)) {
            $this->setFactory($factory);
        }
    }

    /**
     * @param BrokerClientContract $brokerClient
     * @return ApplicationContract
     */
    public function setBrokerClient(BrokerClientContract $brokerClient): ApplicationContract
    {
        $this->brokerClient = $brokerClient;
        return $this;
    }

    /**
     * @param FactoryContract $factory
     * @return ApplicationContract
     */
    public function setFactory(FactoryContract $factory): ApplicationContract
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @return BrokerClientContract
     */
    public function getBrokerClient(): BrokerClientContract
    {
        return $this->brokerClient;
    }

    /**
     * @return FactoryContract
     */
    public function getFactory(): FactoryContract
    {
        return $this->factory;
    }
}
