<?php
/**
 * This class is used by a Consumer as a means to implement business logic "OnRequest"
 * From any given RPC client (Publisher)
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\BrokerAPI\Handlers\RPC;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AMQPOnRequestHandler extends RPCHandlerContract
{
    abstract protected function createMessageBody(): string;
    /**
     * @param AMQPMessage $message
     * @return bool
     */
    public function handle($message): bool
    {
        $this->setMessage($message);
        $amqpMesssage = new AMQPMessage(
            $this->createMessageBody(),
            ['correlation_id' => $message->get('correlation_id')]
        );

        $message->delivery_info['channel']->basic_publish(
            $amqpMesssage,
            '',
            $message->get('reply_to')
        );
        $message->delivery_info['channel']->basic_ack(
            $message->delivery_info['delivery_tag']
        );
    }
}
