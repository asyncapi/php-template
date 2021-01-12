<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace GA\BrokerAPI\Handlers;

use GA\BrokerAPI\Messages\MessageContract;

interface HandlerContract
{
    /**
     * @param MessageContract $message
     * @return bool
     */
    public function handle(MessageContract $message): bool;
}
