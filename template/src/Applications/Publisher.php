<?php
/**
 * Subscriber application is the single final class that you should obtain from concrete factory
 * This application will have methods for each channel/operation and you should only
 * pass messages and handlers that will implement any business logic needed (eg: rpc calls)
 * Subscriber = Publisher
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 20:22
 */

namespace {{ params.packageName }}\Applications;

use {{ params.packageName }}\Messages\MessageContract;
use {{ params.packageName }}\Handlers\AMQPRPCClientHandler;

final class Publisher extends ApplicationContract
{
{%- for channelName, channel in asyncapi.channels() %}
{%- if channel.hasSubscribe() %}
    {%- set methodName = channel.subscribe().id() %}
    {%- set methodDescription = channel.subscribe().description() %}
    {%- set amqpBindings = channel.subscribe().bindings().amqp %}
    {%- if amqpBindings["x-type"] == 'basic' %}
    /**
     * {{ methodDescription }}
     *
     * @param MessageContract $message
     */
    public function {{ methodName }}(
        MessageContract $message,
        array $customConfig = []
    )
    {
        {%- set exchangeName = amqpBindings.exchange.name %}
        {%- set exchangeType = amqpBindings.exchange.type %}
        {%- set exchangeDurable = amqpBindings.exchange.durable %}
        {%- set exchangeAutoDelete = amqpBindings.exchange.autoDelete %}
        {%- set queueBindingKey = amqpBindings.queue.bindingKey %}
        $this->getBrokerClient()->basicPublish(
            $message,
            array_merge([
                'exchangeName' => '{{ exchangeName }}',
                'exchangeType' => '{{ exchangeType }}',
                'bindingKey'   => '{{ queueBindingKey }}',
                'passive'      => false,
                'durable' => {{ exchangeDurable if exchangeDurable else "false" }},
                'autoDelete' => {{ exchangeAutoDelete if exchangeAutoDelete else "true" }},
                'internal'     => false,
                'noWait'       => false,
                'arguments'    => [],
                'ticket'       => null,
                'mandatory'    => false,
                'immediate'    => false,
            ], $customConfig)
        );
    }
    {%- elseif amqpBindings["x-type"] == 'rpc' %}
    /**
     * {{ methodDescription }}
     *
     * @param MessageContract $message
     * @param RPCHandlerContract $handler
     */
    public function {{ methodName }}(
        MessageContract $message,
        array $customConfig = []
    )
    {
        {%- set bindingKey = amqpBindings.queue.name %}
        return $this->getBrokerClient()->rpcPublish(
            $message,
            array_merge([
                'bindingKey' => '{{ bindingKey }}'
            ], $customConfig)
        );
    }
    {%- endif %}
{%- endif %}
{%- endfor %}
}
