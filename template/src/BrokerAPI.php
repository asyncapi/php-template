<?php
/**
 * This is the single entry point for the BrokerAPI.
 * This class will be in charge of instantiating whatever Factory is needed
 * depending on what's the default protocol on async api file
 * This default protocol can be overwritten by just sending it while instantiating this class
 * User: emiliano
 * Date: 7/1/21
 * Time: 10:41
 */

namespace {{ params.packageName }}\BrokerAPI;

use Dotenv\Dotenv;
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
        $this->initDotEnv();
        $factory = null;
        switch ($this->protocol) {
            case AMQP_PROTOCOL_KEY:
                $factory = new AMQPFactory();
                break;
        }

        return $factory;
    }

    private function initDotEnv()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__, './../.env');
            $dotenv->load();
        } catch (\Throwable $t) {
            //if no file is found, throw no errors
            return false;
        }
    }
}
