<?php
require "../../../vendor/autoload.php";
require "Handlers/RPCExampleHandler.php";

use {{ params.packageName }};

$brokerAPI = new BrokerAPI();
$factory = $brokerAPI->init();

/** @var \{{ params.packageName }}\Applications\Subscriber $subscriber */
$subscriber = $factory->createApplication(
    SUBSCRIBER_KEY,
    [
        BROKER_HOST_KEY         => $_ENV[ENV_BROKER_HOST_KEY] ?? BROKER_HOST_DEFAULT,
        BROKER_USER_KEY         => $_ENV[ENV_BROKER_USER_KEY] ?? BROKER_USER_DEFAULT,
        BROKER_PASSWORD_KEY     => $_ENV[ENV_BROKER_PASSWORD_KEY] ?? BROKER_PASSWORD_DEFAULT,
        BROKER_PORT_KEY         => $_ENV[ENV_BROKER_PORT_KEY] ?? BROKER_PORT_DEFAULT,
        BROKER_VIRTUAL_HOST_KEY => $_ENV[ENV_BROKER_VIRTUAL_HOST_KEY] ?? BROKER_VIRTUAL_HOST_DEFAULT,
    ]
);
$handler = new \Examples\RPC\Subscriber\Handlers\RPCExampleHandler();
$subscriber->retrieveExampleByIdRPC($handler);
