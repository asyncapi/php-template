<?php
/**
 * Publisher application is the single final class that you should obtain from concrete factory
 * This application will have methods for each channel/operation and you should only
 * Pass custom handlers to those methods for any business logic needed on message received
 * Publisher = Worker = Subscriber
 * User: emiliano
 * Date: 7/1/21
 * Time: 12:24
 */

namespace {{ params.packageName }}\Applications;

use {{ params.packageName }}\Handlers\HandlerContract;
use {{ params.packageName }}\Handlers\AMQPRPCServerHandler;

final class Subscriber extends ApplicationContract
{
{%- for channelName, channel in asyncapi.channels() %}
{%- if channel.hasPublish() %}
    {%- set methodName = channel.publish().id() %}
    {%- set methodDescription = channel.publish().description() %}
    {%- set amqpBindings = channel.publish().bindings().amqp %}
    {%- if amqpBindings["x-type"] == 'basic' %}
    /**
     * {{ methodDescription }}
     *
     * @param HandlerContract $handler
     * @param array $customConfig
     */
        public function {{ methodName }}(
            HandlerContract $handler,
            array $customConfig = []
        )
        {
            {%- set exchangeName = amqpBindings.exchange.name %}
            {%- set exchangeType = amqpBindings.exchange.type %}
            {%- set exchangeDurable = amqpBindings.exchange.durable %}
            {%- set exchangeAutoDelete = amqpBindings.exchange.autoDelete %}
            {%- set queueDurable = amqpBindings.queue.durable %}
            {%- set queueExclusive = amqpBindings.queue.exclusive %}
            {%- set queueBindingKey = amqpBindings.queue.bindingKey %}
            {%- set queueAutoDelete = amqpBindings.queue.autoDelete %}
            $config = array_merge([
                'queue'           => '',
                'publisherTag'     => '',
                'noLocal'         => false,
                'noAck'           => false,
                'exclusive'       => {{queueExclusive}},
                'noWait'          => false,
                'ticket'          => 'null',
                'arguments'       => [],
                'exchangeName'    => '{{ exchangeName }}',
                'exchangeType'    => '{{ exchangeType }}',
                'exchangeDurable' => {{ exchangeDurable }},
                'exchangeAutoDelete' => {{ exchangeAutoDelete }},
                'queueDurable'    => {{ queueDurable }},
                'queueAutoDelete' => {{ queueAutoDelete }},
                'bindingKey'      => '{{ queueBindingKey }}',
            ], $customConfig);

            $this->getBrokerClient()->basicConsume($handler, $config);
        }
    {%- elseif amqpBindings["x-type"] == 'rpc' %}

    /**
     * {{ methodDescription }}
     *
     * @param RPCHandlerContract $handler
     * @param array $customConfig
     */
        public function {{ methodName }}(
            AMQPRPCServerHandler $handler,
            array $customConfig = []
        )
        {
            {%- set queueName = amqpBindings.queue.name %}
            $config = array_merge([
                'queueName' => '{{ queueName }}'
            ], $customConfig);
            $this->getBrokerClient()->rpcConsume(
                $handler,
                $config
            );
        }
    {%- endif %}
{%- endif %}
{%- endfor %}
}
