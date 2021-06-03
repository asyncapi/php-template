<?php

namespace Examples\Basic\Publisher\Handlers;

use {{ params.packageName }}\Handlers\HandlerContract;
use {{ params.packageName }}\Messages\MessageContract;
use PhpAmqpLib\Message\AMQPMessage;

class ExampleHandler implements HandlerContract
{
    /**
     * @param AMQPMessage $message
     * @return bool
     */
    public function handle($message): bool
    {
        try {
            echo "Receiving message...\r\n";
            print_r($message->getBody());
            return true;
        } catch (\Throwable $t) {
            echo $t->getMessage();
            return false;
        }

    }
}
