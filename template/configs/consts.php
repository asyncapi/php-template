<?php
//NO LITERALS SHOULD BE USED IN CODE. IF LITERAL IS NEEDED, PLEASE ADD A NEW CONST
/** MSG BROKER CONFIG KEYS */
const BROKER_HOST_KEY = 'host';
const BROKER_USER_KEY = 'user';
const BROKER_PASSWORD_KEY = 'password';
const BROKER_PORT_KEY = 'port';
const BROKER_VIRTUAL_HOST_KEY = 'virtual_host';
const ENV_BROKER_HOST_KEY = 'BROKER_HOST';
const ENV_BROKER_USER_KEY = 'BROKER_USER';
const ENV_BROKER_PASSWORD_KEY = 'BROKER_PASSWORD';
const ENV_BROKER_PORT_KEY = 'BROKER_PORT';
const ENV_BROKER_VIRTUAL_HOST_KEY = 'BROKER_VIRTUAL_HOST';
/** END OF MSG BROKER CONFIG KEYS */

/** MSG BROKER DEFAULT_VALUES */
const BROKER_HOST_DEFAULT = 'localhost';
const BROKER_USER_DEFAULT = 'guest';
const BROKER_PASSWORD_DEFAULT = 'guest';
const BROKER_PORT_DEFAULT = '5672';
const BROKER_VIRTUAL_HOST_DEFAULT = '/';
/** END OF MSG BROKER DEFAULT_VALUES */

/** PROTOCOL LITERALS */
const AMQP_PROTOCOL_KEY = 'amqp';
const KAFKA_PROTOCOL_KEY = 'kafka';
const MQTT_PROTOCOL_KEY = 'mqtt';
/** END OF PROTOCOL LITERALS */

/** APPLICATION LITERALS */
const SUBSCRIBER_KEY = 'subscriber';
const PUBLISHER_KEY = 'publisher';
/** END OF APPLICATION LITERALS */
