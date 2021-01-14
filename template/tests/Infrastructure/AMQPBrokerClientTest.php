<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 14:42
 */

namespace {{ params.packageName }}\BrokerAPI\Tests\Infrastructure\AMQP;

use {{ params.packageName }}\BrokerAPI\Infrastructure\AMQPBrokerClient;
use {{ params.packageName }}\BrokerAPI\Tests\BaseTest;
use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;

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
        $amqpBrokerClient = new AMQPBrokerClient($amqpStreamConnectionStub->reveal());

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

        $amqpBrokerClient = new AMQPBrokerClient($amqpStreamConnectionStub->reveal());

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

        $amqpBrokerClient = new AMQPBrokerClient($amqpStreamConnectionStub->reveal());

        //When we try to publish
        $result = $amqpBrokerClient->publishToExchange($messageStub->reveal(), $settings);

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
                $callback ?? null,
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

        $amqpBrokerClient = new AMQPBrokerClient($amqpStreamConnectionStub->reveal());

        //When we try to consume messages
        $amqpBrokerClient->consumeThroughExchange($settings);

        //Then we assert we have the worker running
        //prophecy fulfilled, means worker ran just fine :)
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
                    'callback'     => null,
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
