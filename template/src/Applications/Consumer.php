<?php
/**
 * Consumer application is the single final class that you should obtain from concrete factory
 * This application will have methods for each channel/operation and you should only
 * Pass custom handlers to those methods for any business logic needed on message received
 * Consumer = Worker = Subscriber
 * User: emiliano
 * Date: 7/1/21
 * Time: 12:24
 */

namespace {{ params.packageName }}\BrokerAPI\Applications;

use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;

final class Consumer extends ApplicationContract
{
{%- for channelName, channel in asyncapi.channels() %}
{%- if channel.hasSubscribe() %}
{%- set methodName = channel.subscribe().id() %}
    public function {{ methodName }}(HandlerContract $handler)
    {
        {%- set exchangeName = channel.subscribe().bindings().amqp.exchange.name %}
        {%- set exchangeType = channel.subscribe().bindings().amqp.exchange.type %}
        {%- set exchangeDurable = channel.subscribe().bindings().amqp.exchange.durable %}
        {%- set exchangeAutoDelete = channel.subscribe().bindings().amqp.exchange.autoDelete %}
        {%- set queueDurable = channel.subscribe().bindings().amqp.queue.durable %}
        {%- set queueExclusive = channel.subscribe().bindings().amqp.queue.exclusive %}
        {%- set queueBindingKey = channel.subscribe().bindings().amqp.queue.bindingKey %}
        {%- set queueAutoDelete = channel.subscribe().bindings().amqp.queue.autoDelete %}
        $config = [
            'queue'           => '',
            'consumerTag'     => '',
            'noLocal'         => false,
            'noAck'           => false,
            'exclusive'       => {{queueExclusive}},
            'noWait'          => false,
            'callback'        => function ($msg) use ($handler) {
                return $handler->handle($msg);
            },
            'ticket'          => 'null',
            'arguments'       => [],
            'exchangeName'    => '{{ exchangeName }}',
            'exchangeType'    => '{{ exchangeType }}',
            'exchangeDurable' => {{ exchangeDurable }},
            'exchangeAutoDelete' => {{ exchangeAutoDelete }},
            'queueDurable'    => {{ queueDurable }},
            'queueAutoDelete' => {{ queueAutoDelete }},
            'bindingKey'      => '{{ queueBindingKey }}',
        ];

        $this->getBrokerClient()->consumeThroughExchange($config);
    }
{%- endif %}
{%- endfor %}
}
