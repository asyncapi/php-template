<?php

namespace Examples\RPC\Consumer\Handlers;

use PhpAmqpLib\Message\AMQPMessage;
use {{ params.packageName }}\BrokerAPI\Common\AMQPFactory;
use {{ params.packageName }}\BrokerAPI\Handlers\AMQPRPCServerHandler;
use {{ params.packageName }}\BrokerAPI\Messages\Example;

/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 15/1/21
 * Time: 19:02
 */

class RPCExampleHandler extends AMQPRPCServerHandler
{
    /**
     * @param AMQPMessage $message
     * @return bool
     */
    public function handle($message): bool
    {
        /** @var AMQPFactory $factory */
        $payload = json_decode($message->getBody());
        $factory = new AMQPFactory();
        $newMessage = $factory->createMessage(
            Example::class,
            [
                'id'   => $payload->id,
                'name' => 'Some merchant',
            ],
            [
                'correlationId' => $message->get('correlation_id'),
                'replyTo'       => $message->get('reply_to'),
            ]
        );

        $this->sendRPCResponse($message, $newMessage);
        return true;
    }
}