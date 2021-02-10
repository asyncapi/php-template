<?php
/**
 * This class is used by a Consumer as a means to implement business logic "OnRequest"
 * From any given RPC client (Publisher)
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\BrokerAPI\Handlers;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AMQPRPCServerHandler implements HandlerContract
{
    abstract public function handle($message): bool;

    /**
     * @param AMQPMessage $request
     * @param MessageContract $message
     * @return bool
     */
    protected function sendRPCResponse(
        AMQPMessage $request,
        MessageContract $message
    ): bool
    {
        /** @var AMQPMessage $amqpMessage */
        $amqpMessage = $message->getPayload();

        $request->delivery_info['channel']->basic_publish(
            $amqpMessage,
            '',
            $request->get('reply_to')
        );
        $request->ack();

        return true;
    }
}
