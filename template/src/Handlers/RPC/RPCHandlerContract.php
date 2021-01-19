<?php
/**
 * RPCHandlers are a concretion made from the base interface
 * These handlers will tipically have more methods as they're exclusively used for RPC comms
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\BrokerAPI\Handlers\RPC;

use {{ params.packageName }}\BrokerAPI\Messages\MessageContract;
use {{ params.packageName }}\BrokerAPI\Handlers\HandlerContract;

abstract class RPCHandlerContract implements HandlerContract
{
    private $message;
    private $correlationId;

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;
    }
}
