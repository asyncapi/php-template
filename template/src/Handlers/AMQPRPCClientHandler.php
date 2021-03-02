<?php
/**
 * This class is used by a Consumer as a means to implement business logic "OnRequest"
 * From any given RPC client (Publisher)
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\Handlers;

use {{ params.packageName }}\Messages\MessageContract;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPRPCClientHandler implements HandlerContract
{
    /** @var AMQPMessage $message */
    private $message;
    private $correlationId;

    public function handle($message): bool
    {
        /** @var AMQPMessage $message */
        if ($message->get('correlation_id') == $this->getCorrelationId()) {
            $this->setMessage($message);
        }

        return true;
    }

    public function setMessage(AMQPMessage $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): ?AMQPMessage
    {
        return $this->message;
    }

    public function setCorrelationId(string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }
}
