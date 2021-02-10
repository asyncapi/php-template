<?php
require "../../../vendor/autoload.php";

use {{ params.packageName }}\BrokerAPI\BrokerAPI;
use {{ params.packageName }}\BrokerAPI\Messages\Example;

$brokerAPI = new BrokerAPI();
$factory = $brokerAPI->init();

/** @var \{{ params.packageName }}\BrokerAPI\Applications\Producer $producer */
$producer = $factory->createApplication(
    PRODUCER_KEY,
    [
        BROKER_HOST_KEY         => $_ENV[ENV_BROKER_HOST_KEY] ?? BROKER_HOST_DEFAULT,
        BROKER_USER_KEY         => $_ENV[ENV_BROKER_USER_KEY] ?? BROKER_USER_DEFAULT,
        BROKER_PASSWORD_KEY     => $_ENV[ENV_BROKER_PASSWORD_KEY] ?? BROKER_PASSWORD_DEFAULT,
        BROKER_PORT_KEY         => $_ENV[ENV_BROKER_PORT_KEY] ?? BROKER_PORT_DEFAULT,
        BROKER_VIRTUAL_HOST_KEY => $_ENV[ENV_BROKER_VIRTUAL_HOST_KEY] ?? BROKER_VIRTUAL_HOST_DEFAULT,
    ]
);

$message = $factory->createMessage(
    Example::class,
    [
        'id' => 1,
    ]
);
/** @var \PhpAmqpLib\Message\AMQPMessage $return */
$return = $producer->requestExampleByIdRPC($message);
print_r($return->getBody());