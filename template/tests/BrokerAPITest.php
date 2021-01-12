<?php

namespace GA\BrokerAPI\Tests;

use GA\BrokerAPI\BrokerAPI;
use GA\BrokerAPI\Common\AMQPFactory;

class BrokerAPITest extends BaseTest
{
    /**
     * @test
     * @dataProvider apiProtocolDataProvider
     */
    public function it_creates_factory_by_protocol(
        $protocolConstantName,
        $expectedFactoryFqn
    ) {
        //Given we have a valid protocol string and a BrokerAPI instance
        $protocol = constant($protocolConstantName);
        $brokerAPI = new BrokerAPI($protocol);

        //When we try to create the factory
        $factory = $brokerAPI->init();

        //Then we assert we got the expected factory
        $this->assertTrue($factory instanceof $expectedFactoryFqn);
    }

    public function apiProtocolDataProvider()
    {
        return [
            [
                'protocolConstantName' => 'AMQP_PROTOCOL_KEY',
                'expectedFactoryFqn'   => AMQPFactory::class,
            ],
        ];
    }
}
