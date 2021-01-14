<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 7/1/21
 * Time: 10:41
 */

namespace {{ params.packageName }}\BrokerAPI;

use {{ params.packageName }}\BrokerAPI\Common\AMQPFactory;
use {{ params.packageName }}\BrokerAPI\Common\FactoryContract;

final class BrokerAPI
{
    /** @var string $protocol */
    private $protocol;

    /**
     * BrokerAPI constructor.
     * @param string $protocol
     */
    public function __construct(string $protocol = '{{-asyncapi | getDefaultProtocol}}')
    {
        $this->protocol = $protocol;
    }

    public function init(): FactoryContract
    {
        $factory = null;
        switch ($this->protocol) {
            case AMQP_PROTOCOL_KEY:
                $factory = new AMQPFactory();
                break;
        }

        return $factory;
    }
}
