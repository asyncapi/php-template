<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 30/12/20
 * Time: 11:39
 */

namespace GA\BrokerAPI\Common;

use GA\BrokerAPI\Handlers\HandlerContract;
use GA\BrokerAPI\Infrastructure\BrokerClientContract;
use GA\BrokerAPI\Infrastructure\AMQPBrokerClient;
use GA\BrokerAPI\Messages\MessageContract;
use GA\BrokerAPI\Applications\ApplicationContract;
use GA\BrokerAPI\Applications\Consumer;
use GA\BrokerAPI\Applications\Producer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPFactory implements FactoryContract
{
    /** @var AMQPStreamConnection $brokerConnection */
    private $brokerConnection;

    /**
     * AMQPFactory constructor.
     * @param AMQPStreamConnection|null $brokerConnection
     */
    public function __construct(
        AMQPStreamConnection $brokerConnection = null
    ) {
        $this->brokerConnection = $brokerConnection;
    }

    /**
     * /**
     * @param array $config
     * @param null $brokerConnection
     * @return BrokerClientContract
     */
    public function createBrokerClient(
        array $config = []
    ): BrokerClientContract {

        if (empty($this->brokerConnection)) {
            $this->brokerConnection = new AMQPStreamConnection(
                $config[BROKER_HOST_KEY] ?? BROKER_HOST_DEFAULT,
                $config[BROKER_PORT_KEY] ?? BROKER_PORT_DEFAULT,
                $config[BROKER_USER_KEY] ?? BROKER_USER_DEFAULT,
                $config[BROKER_PASSWORD_KEY] ?? BROKER_PASSWORD_DEFAULT,
                $config[BROKER_VIRTUAL_HOST_KEY] ?? BROKER_VIRTUAL_HOST_DEFAULT
            );
        }

        return new AMQPBrokerClient($this->brokerConnection);
    }

    /**
     * @param string $applicationType
     * @param array $config
     * @return ApplicationContract
     */
    public function createApplication(
        string $applicationType,
        array $config = []
    ): ApplicationContract {
        $application = null;

        $brokerClient = $this->createBrokerClient($config);

        switch ($applicationType) {
            case PRODUCER_KEY:
                $application = new Producer(
                    $brokerClient
                );
                break;
            case CONSUMER_KEY:
                $application = new Consumer(
                    $brokerClient
                );
                break;
        }
        $application->setFactory($this);

        return $application;
    }

    /**
     * @param array $config
     * @return HandlerContract
     */
    public function createHandler(array $config = []): HandlerContract
    {
        // TODO: Implement createHandler() method.
    }

    /**
     * @param MessageContract $message
     * @param array $settings
     * @return MessageContract
     */
    public function createMessage(
        MessageContract $message,
        array $settings = []
    ): MessageContract {
        $message->setSettings($settings)
            ->setPayload(
                new AMQPMessage(json_encode($message))
            );

        return $message;
    }
}
