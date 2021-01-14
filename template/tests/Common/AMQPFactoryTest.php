<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:32
 */

namespace {{ params.packageName }}\BrokerAPI\Tests\Common\AMQP;

use {{ params.packageName }}\BrokerAPI\Common\AMQPFactory;
use {{ params.packageName }}\BrokerAPI\Infrastructure\AMQPBrokerClient;
use {{ params.packageName }}\BrokerAPI\Infrastructure\BrokerClientContract;
use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Applications\Consumer;
use {{ params.packageName }}\BrokerAPI\Applications\Producer;
use {{ params.packageName }}\BrokerAPI\Tests\BaseTest;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;

class AMQPFactoryTest extends BaseTest
{
    /**
     * @test
     */
    public function it_creates_amqp_broker_client()
    {
        //Given we have a valid amqp factory and a AMQPStreamConnection stub
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());

        //When we ask the factory to create it
        $brokerClientInstance = $factory->createBrokerClient();

        //Then we assert we got a valid BrokerClientContract and specified concretion back
        $this->assertTrue($brokerClientInstance instanceof BrokerClientContract);
        $this->assertTrue($brokerClientInstance instanceof AMQPBrokerClient);
    }

    /**
     * @test
     * @dataProvider applicationTypesDataProvider
     * @param $applicationTypeConstant
     * @param $expectedApplicationFqn
     */
    public function it_creates_application_by_type_with_dependencies(
        $applicationTypeConstant,
        $expectedApplicationFqn,
        $expectedDependencies
    ) {
        //Given we have a valid application type
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());

        //When we try to create it through the factory
        $application = $factory->createApplication(constant($applicationTypeConstant));

        //Then we assert we got the expected concrete fqn
        $this->assertTrue($application instanceof $expectedApplicationFqn);
        foreach ($expectedDependencies as $dependency => $options) {
            $getter = $options['getter'];
            $retrievedDependency = $application->$getter();
            $this->assertTrue($retrievedDependency instanceof $options['expectedDependency']);
        }
    }

    /** @test */
    public function it_creates_amqp_message_with_props_as_body()
    {
        //Given we have our factory and a prophesized MessageContract
        $factory = new AMQPFactory();
        $message = $this->prophesize(MessageContract::class);
        $message->setSettings(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($message->reveal());
        $message->setPayload(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($message->reveal());
        $message->getPayload()
            ->willReturn(new AMQPMessage('null'));
        $message->jsonSerialize()
            ->willReturn(null);

        //When we try to request it's creation from the factory
        $createdMessage = $factory->createMessage($message->reveal());
        /** @var AMQPMessage $amqpMessage */
        $amqpMessage = $createdMessage->getPayload();
        //Then we assert we got it as expected
        $this->assertTrue($amqpMessage instanceof AMQPMessage);
        //And we assert that the body has all the props from the original MessageContract
        $this->assertEquals(json_encode($message->reveal()), $amqpMessage->getBody());
    }

    /**
     * @test
     * @dataProvider requestedHandlerDataProvider
     */
    public function it_creates_requested_handler(
        $requestedHandler,
        $expectedHandler
    ) {
        //Given we have a valid factory
        $factory = $this->prophesize(AMQPFactory::class);
        $instantiatedHandler = $this->prophesize($requestedHandler);
        $factory
            ->createHandler($requestedHandler)
            ->shouldBeCalledOnce()
            ->willReturn($instantiatedHandler->reveal());

        //When we request for a given handler
        $instantiatedHandler = $factory->reveal()->createHandler($requestedHandler);

        //Then we assert we got the handler expected back
        $this->assertTrue($instantiatedHandler instanceof $expectedHandler);
    }

    public function requestedHandlerDataProvider()
    {
        return [
            [
                'requestedHandler' => HandlerContract::class,
                'expectedHandler'  => HandlerContract::class,
            ],
        ];
    }

    public function applicationTypesDataProvider()
    {
        return [
            [
                'applicationTypeConstant' => 'PRODUCER_KEY',
                'expectedApplicationFqn'  => Producer::class,
                'expectedDependencies'    => [
                    'brokerClient' => [
                        'getter'             => 'getBrokerClient',
                        'expectedDependency' => AMQPBrokerClient::class,
                    ],
                    'factory'      => [
                        'getter'             => 'getFactory',
                        'expectedDependency' => AMQPFactory::class,
                    ],
                ],
            ],
            [
                'applicationTypeConstant' => 'CONSUMER_KEY',
                'expectedApplicationFqn'  => Consumer::class,
                'expectedDependencies'    => [
                    'brokerClient' => [
                        'getter'             => 'getBrokerClient',
                        'expectedDependency' => AMQPBrokerClient::class,
                    ],
                    'factory'      => [
                        'getter'             => 'getFactory',
                        'expectedDependency' => AMQPFactory::class,
                    ],
                ],
            ],
        ];
    }
}
