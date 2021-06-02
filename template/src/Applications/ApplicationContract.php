<?php
/**
 * Base interface for all Applications
 * In BrokerAPI applications can either be Subscribers or Publishers
 * (depending on message protocol these often get called as publishers/subscribers)
 * Application classes should never be extended
 *
 * User: emiliano
 * Date: 7/1/21
 * Time: 14:38
 */

namespace {{ params.packageName }}\Applications;

use {{ params.packageName }}\Common\FactoryContract;
use {{ params.packageName }}\Handlers\HandlerContract;
use {{ params.packageName }}\Infrastructure\BrokerClientContract;

abstract class ApplicationContract
{
    /** @var BrokerClientContract $brokerClient */
    private $brokerClient;
    /** @var FactoryContract $factory */
    private $factory;

    /**
     * ApplicationContract constructor.
     * @param BrokerClientContract $brokerClient
     * @param FactoryContract|null $handler
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
