<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 7/1/21
 * Time: 10:41
 */

namespace GA\BrokerAPI;

use GA\BrokerAPI\Common\AMQPFactory;
use GA\BrokerAPI\Common\FactoryContract;

final class BrokerAPI
{
    /** @var string $protocol */
    private $protocol;

    public function __construct(string $protocol = '')
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
