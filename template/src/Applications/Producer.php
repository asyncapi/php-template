<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 20:22
 */

namespace {{ params.packageName }}\BrokerAPI\Applications;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Handlers\RPC\RPCHandlerContract;

final class Producer extends ApplicationContract
{
{%- for channelName, channel in asyncapi.channels() %}
{%- if channel.hasPublish() %}
    {%- set methodName = channel.publish().id() %}
    {%- set methodDescription = channel.publish().description() %}
    {%- set amqpBindings = channel.subscribe().bindings().amqp %}
    {%- if amqpBindings.type == 'basic' %}
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
    {%- elseif amqpBindings.type == 'rpc' %}
    /**
     * {{ methodDescription }}
     *
     * @param MessageContract $message
     * @param RPCHandlerContract $handler
     */
    public function {{ methodName }}(
        MessageContract $message,
        RPCHandlerContract $handler,
        array $customConfig = []
    )
    {
        {%- set bindingKey = amqpBindings.queue.name %}
        return $this->getBrokerClient()->rpcPublish(
            $message,
            $handler,
            array_merge([
                'bindingKey' => '{{ bindingKey }}'
            ], $customConfig)
        );
    }
    {%- endif %}
{%- endif %}
{%- endfor %}
}
