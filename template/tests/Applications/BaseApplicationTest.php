<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 11/1/21
 * Time: 19:18
 */

namespace {{ params.packageName }}\Tests\Applications;

use {{ params.packageName }}\Applications\Publisher;
use {{ params.packageName }}\Applications\Subscriber;
use {{ params.packageName }}\Common\FactoryContract;
use {{ params.packageName }}\Handlers\HandlerContract;
use {{ params.packageName }}\Infrastructure\BrokerClientContract;
use {{ params.packageName }}\Tests\BaseTest;

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
        $subscriber = new Subscriber($brokerClientProphecy->reveal());
        $publisher = new Publisher($brokerClientProphecy->reveal());
        $subscriber->$brokerSetter($brokerClientProphecy->reveal());
        $publisher->$brokerSetter($brokerClientProphecy->reveal());
        $subscriber->$factorySetter($factoryProphecy->reveal());
        $publisher->$factorySetter($factoryProphecy->reveal());

        //Then we assert we get it back
        $this->assertDependenciesFromDataProvider($subscriber, $brokerClient, $factory, $publisher);
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
        $subscriber = new Subscriber(
            $brokerClientProphecy->reveal(),
            $factoryProphecy->reveal()
        );
        $publisher = new Publisher(
            $brokerClientProphecy->reveal(),
            $factoryProphecy->reveal()
        );

        //Then we assert we get expected dependencies back
        $this->assertDependenciesFromDataProvider($subscriber, $brokerClient, $factory, $publisher);
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
     * @param $subscriber
     * @param $brokerClient
     * @param $factory
     * @param $publisher
     */
    private function assertDependenciesFromDataProvider($subscriber, $brokerClient, $factory, $publisher): void
    {
        $brokerGetter = $brokerClient['getter'];
        $factoryGetter = $factory['getter'];
        $this->assertTrue($subscriber->$brokerGetter() instanceof $brokerClient['dependency']);
        $this->assertTrue($subscriber->$factoryGetter() instanceof $factory['dependency']);
        $this->assertTrue($publisher->$brokerGetter() instanceof $brokerClient['dependency']);
        $this->assertTrue($publisher->$factoryGetter() instanceof $factory['dependency']);
    }
}
