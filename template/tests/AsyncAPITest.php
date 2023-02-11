<?php

namespace {{ params.packageName }}\Tests;

use {{ params.packageName }}\AsyncAPI;
use {{ params.packageName }}\Common\AMQPFactory;

class AsyncAPITest extends BaseTest
{
    /**
     * @test
     * @dataProvider apiProtocolDataProvider
     */
    public function it_creates_factory_by_protocol(
        $protocolConstantName,
        $expectedFactoryFqn
    ) {
        //Given we have a valid protocol string and a AsyncAPI instance
        $protocol = constant($protocolConstantName);
        $brokerAPI = new AsyncAPI($protocol);

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
