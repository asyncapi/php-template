<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:03
 */

namespace GA\BrokerAPI\Messages;

abstract class MessageContract implements \JsonSerializable
{
    /** @var array $settings */
    private $settings = [];
    private $payload;

    /**
     * @param array $settings
     * @return MessageContract
     */
    public function setSettings(array $settings = []): MessageContract
    {
        $this->settings = $settings;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

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

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
