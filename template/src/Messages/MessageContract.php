<?php
/**
 * Base contract for all messages in AsyncAPI.
 * Messages are constructed from the messages schema in async api
 *
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace {{ params.packageName }}\Messages;

abstract class MessageContract implements \JsonSerializable
{
    private $payload;

    /**
     * @return array
     */
    abstract public function getters(): array;

    /**
     * @return array
     */
    abstract public function setters(): array;

    /**
     * @param null|string $payload
     * @return MessageContract
     */
    public function setPayload($payload): MessageContract
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
