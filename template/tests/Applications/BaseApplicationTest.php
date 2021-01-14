<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 11/1/21
 * Time: 19:18
 */

namespace {{ params.packageName }}\BrokerAPI\Tests\Applications;

use {{ params.packageName }}\BrokerAPI\Applications\Consumer;
use {{ params.packageName }}\BrokerAPI\Applications\Producer;
use {{ params.packageName }}\BrokerAPI\Common\FactoryContract;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;
use {{ params.packageName }}\BrokerAPI\Infrastructure\BrokerClientContract;
use {{ params.packageName }}\BrokerAPI\Tests\BaseTest;

class BaseApplicationTest extends BaseTest
{
    /**
     * @test
     * @dataProvider dependenciesWithSettersAndGetters
     */
    public function it_sets_and_gets_dependencies($expectedDependencies)
    {
        //Given we have setters and getters with expected dependency
        list($brokerClient, $factory, $brokerClientProphecy, $factoryProphecy) = $this->setDependenciesFromDataProvider($expectedDependencies);
        $brokerSetter = $brokerClient['setter'];
        $factorySetter = $factory['setter'];

        //When we try to set it
        $producer = new Producer($brokerClientProphecy->reveal());
        $consumer = new Consumer($brokerClientProphecy->reveal());
        $producer->$brokerSetter($brokerClientProphecy->reveal());
        $consumer->$brokerSetter($brokerClientProphecy->reveal());
        $producer->$factorySetter($factoryProphecy->reveal());
        $consumer->$factorySetter($factoryProphecy->reveal());

        //Then we assert we get it back
        $this->assertDependenciesFromDataProvider($producer, $brokerClient, $factory, $consumer);
    }

    /**
     * @test
     * @dataProvider dependenciesWithSettersAndGetters
     */
    public function it_loads_dependencies_on_constructor($expectedDependencies)
    {
        //Given we have setters and getters with expected dependency
        list($brokerClient, $factory, $brokerClientProphecy, $factoryProphecy) = $this->setDependenciesFromDataProvider($expectedDependencies);

        //When we instantiate applications
        $producer = new Producer(
            $brokerClientProphecy->reveal(),
            $factoryProphecy->reveal()
        );
        $consumer = new Consumer(
            $brokerClientProphecy->reveal(),
            $factoryProphecy->reveal()
        );

        //Then we assert we get expected dependencies back
        $this->assertDependenciesFromDataProvider($producer, $brokerClient, $factory, $consumer);
    }

    public function dependenciesWithSettersAndGetters()
    {
        return [
            [
                'expectedDependencies' => [
                    [
                        'getter'     => 'getBrokerClient',
                        'setter'     => 'setBrokerClient',
                        'dependency' => BrokerClientContract::class,
                    ],
                    [
                        'getter'     => 'getFactory',
                        'setter'     => 'setFactory',
                        'dependency' => FactoryContract::class,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $expectedDependencies
     * @return array
     */
    private function setDependenciesFromDataProvider($expectedDependencies): array
    {
        list($brokerClient, $factory) = $expectedDependencies;

        $brokerClientProphecy = $this->prophesize($brokerClient['dependency']);
        $factoryProphecy = $this->prophesize($factory['dependency']);
        return [$brokerClient, $factory, $brokerClientProphecy, $factoryProphecy];
    }

    /**
     * @param $producer
     * @param $brokerClient
     * @param $factory
     * @param $consumer
     */
    private function assertDependenciesFromDataProvider($producer, $brokerClient, $factory, $consumer): void
    {
        $brokerGetter = $brokerClient['getter'];
        $factoryGetter = $factory['getter'];
        $this->assertTrue($producer->$brokerGetter() instanceof $brokerClient['dependency']);
        $this->assertTrue($producer->$factoryGetter() instanceof $factory['dependency']);
        $this->assertTrue($consumer->$brokerGetter() instanceof $brokerClient['dependency']);
        $this->assertTrue($consumer->$factoryGetter() instanceof $factory['dependency']);
    }
}