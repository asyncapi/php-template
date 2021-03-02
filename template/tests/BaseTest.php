<?php
/**
 * Created by PhpStorm.
 * User: emiliano
 * Date: 7/1/21
 * Time: 11:02
 */

namespace {{ params.packageName }}\Tests;

use Prophecy\PhpUnit\ProphecyTrait;


abstract class BaseTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;
}
