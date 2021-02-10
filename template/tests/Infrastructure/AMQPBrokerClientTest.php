<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 14:42
 */

namespace {{ params.packageName }}\BrokerAPI\Tests\Infrastructure;

use {{ params.packageName }}\BrokerAPI\Infrastructure\AMQPBrokerClient;
use {{ params.packageName }}\BrokerAPI\Tests\BaseTest;
use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Handlers\AMQPRPCClientHandler;
use {{ params.packageName }}\BrokerAPI\Handlers\AMQPRPCServerHandler;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;
use {{ params.packageName }}\BrokerAPI\Common\AMQPFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class AMQPBrokerClientTest extends BaseTest
{
    /**
     * @test
     * @dataProvider brokerClientConfigArray
     */
    public function it_sets_and_gets_amqp_connection($config)
    {
        //Given we have a valid amqp conection
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);

        //When we instantiate the broker client
        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();

        //Then we assert we get a valid AMQPStreamConnection back
        $this->assertTrue($amqpBrokerClient->getConnection() instanceof AMQPStreamConnection);
    }

    /**
     * @test
     * @dataProvider brokerClientConfigArray
     */
    public function it_returns_amqp_channel_when_connection_made($config)
    {
        //Given we have a valid amqp connection & broker client instance
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        $amqpStreamConnectionStub
            ->channel(Argument::any())
            ->willReturn($amqpChannelStub->reveal());

        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();

        //When we try to connect
        $channel = $amqpBrokerClient->connect();

        //Then we assert we got a valid AMQPChannel back
        $this->assertTrue($channel instanceof AMQPChannel);
    }

    /**
     * @test
     * @dataProvider brokerClientPublishSettings
     * @param $message
     * @param $settings
     */
    public function it_publishes_message_to_exchange_by_given_settings(
        $settings
    ) {
        //Given we have a valid amqp connection & broker client instance
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        $messageStub = $this->prophesize(MessageContract::class);
        $amqpMessageStub = $this->prophesize(AMQPMessage::class);
        //and prophecies of the underlying amqp lib
        /**
         * @var bool|null $mandatory
         * @var bool|null $immediate
         * @var $ticket
         * @var string $exchangeName
         * @var string $exchangeType
         * @var string $bindingKey
         * @var bool $noWait
         * @var array $arguments
         * @var $ticket
         * @var $autoDelete
         */
        extract($settings);
        $messageStub
            ->getPayload()
            ->shouldBeCalledOnce()
            ->willReturn($amqpMessageStub->reveal());
        $amqpChannelStub
            ->exchange_declare(
                $exchangeName,
                $exchangeType,
                false,
                false,
                $autoDelete,
                false,
                $noWait,
                $arguments,
                $ticket
            )
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->basic_publish(
                $amqpMessageStub->reveal(),
                $exchangeName,
                $bindingKey,
                $mandatory,
                $immediate,
                $ticket
            )
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->close()
            ->shouldBeCalledOnce();
        $amqpStreamConnectionStub
            ->channel(Argument::any())
            ->willReturn($amqpChannelStub->reveal());
        $amqpStreamConnectionStub
            ->close()
            ->shouldBeCalledOnce();

        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();

        //When we try to publish
        $result = $amqpBrokerClient->basicPublish($messageStub->reveal(), $settings);

        //Then we assert we got a valid bool back
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider brokerClientConsumeSettings
     */
    public function it_consumes_messages_through_exchange_by_given_settings($settings)
    {
        //Given we have a valid amqp connection, broker instance and extracted settings
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        $handlerStub = $this->prophesize(HandlerContract::class);
        $callback = [
            $handlerStub->reveal(),
            'handle'
        ];
        /**
         * @var string|null $consumerTag
         * @var bool|null $noLocal
         * @var bool|null $noAck
         * @var bool|null $exclusive
         * @var bool|null $noWait
         * @var $callback
         * @var $ticket
         * @var array|null $arguments
         * @var string $exchangeName
         * @var string $exchangeType
         * @var string $bindingKey
         * @var bool $autoDelete
         */
        extract($settings);
        $queueName = 'queueName';
        //and prophecies of the underlying amqp lib
        $amqpChannelStub
            ->exchange_declare(
                $exchangeName,
                $exchangeType,
                false,
                false,
                $autoDelete ?? true,
                false,
                $noWait ?? false,
                $arguments ?? [],
                $ticket ?? null
            )
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->queue_declare("", false, false, true, false)
            ->shouldBeCalledOnce()
            ->willReturn([$queueName]);
        $amqpChannelStub
            ->queue_bind($queueName, $exchangeName, $bindingKey)
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->basic_consume(
                $queueName,
                $consumerTag ?? '',
                $noLocal ?? false,
                $noAck ?? false,
                $exclusive ?? false,
                $noWait ?? false,
                $callback,
                $ticket ?? null,
                $arguments ?? []
            )
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->is_consuming()
            ->shouldBeCalledTimes(2)
            ->willReturn(true, false);
        $amqpChannelStub
            ->wait()
            ->shouldBeCalledOnce();
        $amqpChannelStub
            ->close()
            ->shouldBeCalledOnce();
        $amqpStreamConnectionStub
            ->channel(Argument::any())
            ->willReturn($amqpChannelStub->reveal());
        $amqpStreamConnectionStub
            ->close()
            ->shouldBeCalledOnce();

        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();

        //When we try to consume messages
        $amqpBrokerClient->basicConsume(
            $handlerStub->reveal(),
            $settings
        );

        //Then we assert we have the worker running
        //prophecy fulfilled, means worker ran just fine :)
    }

    /**
     * @test
     */
    public function it_makes_rpc_calls_and_waits_for_response()
    {
        //Given we have a valid broker client and message
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        $messageStub = $this->prophesize(MessageContract::class);
        $amqpMessageStub = $this->prophesize(AMQPMessage::class);
        $factoryStub = $this->prophesize(AMQPFactory::class);
        $handlerStub = $this->prophesize(AMQPRPCClientHandler::class);

        //And a set of declared prophecies to be fulfilled
        //queue_declare
        $amqpChannelStub
            ->queue_declare(
                '',
                false,
                false,
                true,
                false
            )
            ->shouldBeCalledOnce();
        //basic_consume
        $amqpChannelStub
            ->basic_consume(
                null,
                '',
                false,
                true,
                false,
                false,
                [
                    $handlerStub->reveal(),
                    'handle'
                ]
            )
            ->shouldBeCalledOnce();
        //check if correlation id is set
        $amqpMessageStub
            ->has('correlation_id')
            ->shouldBeCalledOnce()
            ->willReturn(false);
        //set it
        $amqpMessageStub
            ->set('correlation_id', Argument::type('string'))
            ->shouldBeCalledOnce();
        //set reply to
        $amqpMessageStub
            ->set('reply_to', Argument::any())
            ->shouldBeCalledOnce();
        //get corr id
        $amqpMessageStub
            ->get('correlation_id')
            ->shouldBeCalledOnce()
            ->willReturn(Argument::type('string'));
        //$message->getPayload()
        $messageStub
            ->getPayload()
            ->shouldBeCalledOnce()
            ->willReturn($amqpMessageStub->reveal());
        //basic_publish
        $amqpChannelStub
            ->basic_publish($amqpMessageStub->reveal(), '', Argument::any())
            ->shouldBeCalledOnce();
        //wait till response comes back
        $amqpChannelStub
            ->wait()
            ->shouldBeCalledOnce();
        //close channel
        $amqpChannelStub
            ->close()
            ->shouldBeCalledOnce();
        //connect
        $amqpStreamConnectionStub
            ->channel(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($amqpChannelStub->reveal());
        //close
        $amqpStreamConnectionStub
            ->close(Argument::any())
            ->shouldBeCalledOnce();
        //factory
        $handlerStub
            ->setCorrelationId(Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willReturn($handlerStub->reveal());
        $handlerStub
            ->getMessage()
            ->shouldBeCalledTimes(2)
            ->willReturn(null, $amqpMessageStub->reveal());
        $factoryStub
            ->createHandler(AMQPRPCClientHandler::class)
            ->shouldBeCalledOnce()
            ->willReturn($handlerStub->reveal());

        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();
        $amqpBrokerClient->setFactory($factoryStub->reveal());

        //When we send it through an rpc call
        $receivedMessage = $amqpBrokerClient->rpcPublish(
            $messageStub->reveal(),
            [
                'bindingKey' => ''
            ]
        );
        //Then we assert we got something back and callback executed
        $this->assertEquals($amqpMessageStub->reveal(), $receivedMessage);
    }

    /** @test */
    public function it_receives_rpc_calls_and_sends_response_back()
    {
        //Given we have a valid broker client
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        $handlerStub = $this->prophesize(AMQPRPCServerHandler::class);
        $callback = [
            $handlerStub->reveal(),
            'handle'
        ];
        $queueName = "queueName";
        $prefetchCount = 1;

        //queue_declare
        $amqpChannelStub
            ->queue_declare(
                $queueName,
                false,
                false,
                false,
                false
            )
            ->shouldBeCalledOnce();
        //basic_qos
        $amqpChannelStub
            ->basic_qos(
                null,
                $prefetchCount,
                null
            )
            ->shouldBeCalledOnce();
        //basic_consume
        $amqpChannelStub
            ->basic_consume(
                $queueName,
                '',
                false,
                false,
                false,
                false,
                $callback
            )
            ->shouldBeCalledOnce();
        //is_consuming two times
        $amqpChannelStub
            ->is_consuming()
            ->shouldBeCalledTimes(2)
            ->willReturn(true, false);
        //wait
        $amqpChannelStub
            ->wait()
            ->shouldBeCalledOnce();
        //channel close
        $amqpChannelStub
            ->close()
            ->shouldBeCalledOnce();
        //conn open
        $amqpStreamConnectionStub
            ->channel(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($amqpChannelStub->reveal());
        //conn close
        $amqpStreamConnectionStub
            ->close()
            ->shouldBeCalledOnce();

        //When we try to receive rpc calls and send response back
        $factory = new AMQPFactory($amqpStreamConnectionStub->reveal());
        $amqpBrokerClient = $factory->createBrokerClient();
        $amqpBrokerClient->rpcConsume(
            $handlerStub->reveal(),
            [
                'queueName' => $queueName
            ]
        );

        //Then we assert prophecy fulfilled :)
    }

    public function brokerClientConfigArray()
    {
        return [
            [
                'config' => [
                    'user'     => 'guest',
                    'password' => 'guest',
                    'host'     => 'localhost',
                    'port'     => 5672,
                ],
            ],
        ];
    }

    public function brokerClientPublishSettings()
    {
        return [
            [
                'settings' => [
                    'exchangeName' => '',
                    'exchangeType' => '',
                    'bindingKey'   => '',
                    'mandatory'    => false,
                    'immediate'    => false,
                    'ticket'       => null,
                    'noWait'       => false,
                    'arguments'    => [],
                    'autoDelete'   => false,
                ],
            ],
        ];
    }

    public function brokerClientConsumeSettings()
    {
        return [
            [
                'settings' => [
                    'queue'        => '',
                    'consumerTag'  => '',
                    'noLocal'      => false,
                    'noAck'        => false,
                    'exclusive'    => false,
                    'noWait'       => 'false',
                    'ticket'       => null,
                    'arguments'    => [],
                    'exchangeName' => '',
                    'exchangeType' => '',
                    'bindingKey'   => '',
                    'autoDelete'   => true,
                ],
            ],
        ];
    }
}
