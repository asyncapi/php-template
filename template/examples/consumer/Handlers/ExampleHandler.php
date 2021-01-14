<?php

namespace Consumer\Examples;

use GA\BrokerAPI\Handlers\HandlerContract;
use GA\BrokerAPI\Messages\MessageContract;

class ExampleHandler implements HandlerContract
{
    /**
     * @param MessageContract $message
     * @return bool
     */
    public function handle(MessageContract $message): bool
    {
        //TODO: Implement business logic here dude!
    }
}