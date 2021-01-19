<?php
require "../../../vendor/autoload.php";
require "Handlers/RPCExampleHandler.php";

use {{ params.packageName }}\BrokerAPI\BrokerAPI;

$brokerAPI = new BrokerAPI();
$factory = $brokerAPI->init();

/** @var \{{ params.packageName }}\BrokerAPI\Applications\Consumer $consumer */
$consumer = $factory->createApplication(
    CONSUMER_KEY,
    [
        BROKER_HOST_KEY         => 'localhost',
        BROKER_USER_KEY         => 'guest',
        BROKER_PASSWORD_KEY     => 'guest',
        BROKER_PORT_KEY         => '5672',
        BROKER_VIRTUAL_HOST_KEY => '/',
    ]
);
$handler = new \Examples\RPC\Consumer\Handlers\RPCExampleHandler();
$consumer->retrieveMerchantByIdRPC($handler);