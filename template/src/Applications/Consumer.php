<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 7/1/21
 * Time: 12:24
 */

namespace {{ params.packageName }}\BrokerAPI\Applications;

final class Consumer extends ApplicationContract
{
    {% for channel in asyncapi.channels() %}
    {% endfor %}
}
