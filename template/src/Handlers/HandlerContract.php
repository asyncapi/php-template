<?php
/**
 * Handlers are usually passed through to worker as a means to implement
 * Any required business logic On response or request for any given message
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\BrokerAPI\Handlers;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;

interface HandlerContract
{
    /**
     * @param $message
     * @return bool
     */
    public function handle($message): bool;
}
