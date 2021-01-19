<?php

namespace Examples\RPC\Consumer\Handlers;

use {{ params.packageName }}\BrokerAPI\Handlers\RPC\AMQPOnRequestHandler;
use {{ params.packageName }}\BrokerAPI\Messages\Merchant;

/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 15/1/21
 * Time: 19:02
 */

class RPCExampleHandler extends AMQPOnRequestHandler
{
    protected function createMessageBody(): string
    {
        $merchant = new Merchant();
        $merchant->setId(1);
        $merchant->setName('Some merchant');
        return json_encode($merchant);
    }
}