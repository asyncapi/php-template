<?php
require "../../../vendor/autoload.php";
require "Handlers/RPCExampleHandler.php";

use {{ params.packageName }}\BrokerAPI\BrokerAPI;
use {{ params.packageName }}\BrokerAPI\Messages\Merchant;

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
    Merchant::class,
    [
        'id' => 1,
    ]
);
$handler = new \Examples\RPC\Producer\Handlers\RPCExampleHandler();
/** @var \PhpAmqpLib\Message\AMQPMessage $return */
$return = $producer->requestMerchantByIdRPC(
    $message,
    $handler
);
print_r($return->getBody());