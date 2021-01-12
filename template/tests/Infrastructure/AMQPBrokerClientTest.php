<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 14:42
 */

namespace GA\BrokerAPI\Tests\Infrastructure\AMQP;

use GA\BrokerAPI\Infrastructure\AMQPBrokerClient;
use GA\BrokerAPI\Tests\BaseTest;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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
    public function it_publishes_message_by_given_settings(
        $message,
        $settings
    ) {
        //Given we have a valid amqp connection & broker client instance
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        //and prophecies of the underlying amqp lib
        $amqpChannelStub
            ->basic_publish(
                $message,
                ...array_values($settings)
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
        $amqpBrokerClient->connect();

        //When we try to publish
        $result = $amqpBrokerClient->publish(new \stdClass(), $settings);

        //Then we assert we got a valid bool back
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider brokerClientConsumeSettings
     */
    public function it_consumes_messages_by_given_settings($settings)
    {
        //Given we have a valid amqp connection, broker instance and prophecies
        $amqpStreamConnectionStub = $this->prophesize(AMQPStreamConnection::class);
        $amqpChannelStub = $this->prophesize(AMQPChannel::class);
        //and prophecies of the underlying amqp lib
        $amqpChannelStub
            ->basic_consume(
                ...array_values($settings)
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
        $amqpBrokerClient->connect();

        //When we try to consume messages
        $amqpBrokerClient->consume($settings);

        //Then we assert we have the worker running
        //prophecy fulfilled, means worker ran :)
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
                'message'  => new \stdClass(),
                'settings' => [
                    'exchange'   => '',
                    'routingKey' => '',
                    'mandatory'  => false,
                    'immediate'  => false,
                    'ticket'     => null,
                ],
            ],
        ];
    }

    public function brokerClientConsumeSettings()
    {
        return [
            [
                'settings' => [
                    'queue'       => '',
                    'consumerTag' => '',
                    'noLocal'     => false,
                    'noAck'       => false,
                    'exclusive'   => false,
                    'noWait'      => 'false',
                    'callback'    => null,
                    'ticket'      => null,
                    'arguments'   => [],
                ],
            ],
        ];
    }
}
