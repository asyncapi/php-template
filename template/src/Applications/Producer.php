<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 20:22
 */

namespace {{ params.packageName }}\BrokerAPI\Applications;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;

final class Producer extends ApplicationContract
{
{%- for channelName, channel in asyncapi.channels() %}
{%- if channel.hasPublish() %}
{%- set methodName = channel.publish().id() %}
    public function {{ methodName }}(MessageContract $message)
    {
        {%- set exchangeName = channel.subscribe().bindings().amqp.exchange.name %}
        {%- set exchangeType = channel.subscribe().bindings().amqp.exchange.type %}
        {%- set exchangeDurable = channel.subscribe().bindings().amqp.exchange.durable %}
        {%- set exchangeAutoDelete = channel.subscribe().bindings().amqp.exchange.autoDelete %}
        {%- set queueBindingKey = channel.subscribe().bindings().amqp.queue.bindingKey %}
        $message = $this->getFactory()->createMessage($message);
        $this->getBrokerClient()->publishToExchange(
            $message,
            [
                'exchangeName' => '{{ exchangeName }}',
                'exchangeType' => '{{ exchangeType }}',
                'bindingKey'   => '{{ queueBindingKey }}',
                'passive'      => false,
                'durable' => {{ exchangeDurable }},
                'autoDelete' => {{ exchangeAutoDelete }},
                'internal'     => false,
                'noWait'       => false,
                'arguments'    => [],
                'ticket'       => null,
                'mandatory'    => false,
                'immediate'    => false,
            ]
        );
    }
{%- endif %}
{%- endfor %}
}
