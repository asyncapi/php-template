<?php
require "../../vendor/autoload.php";

use {{ params.packageName }}\BrokerAPI\BrokerAPI;
use {{ params.packageName }}\BrokerAPI\Messages\Merchant;

$brokerAPI = new BrokerAPI();
$factory = $brokerAPI->init();

/** @var \{{ params.packageName }}\BrokerAPI\Applications\Producer $producer */
$producer = $factory->createApplication(
    PRODUCER_KEY,
    [
        BROKER_HOST_KEY         => 'localhost',
        BROKER_USER_KEY         => 'guest',
        BROKER_PASSWORD_KEY     => 'guest',
        BROKER_PORT_KEY         => '5672',
        BROKER_VIRTUAL_HOST_KEY => '/',
    ]
);

$message = $factory->createMessage(
    Merchant::class,
    [
        'id' => 1,
    ]
);

$producer->requestMerchantById($message);