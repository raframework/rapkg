<?php

/**
 * User: coderd
 * Date: 2017/6/21
 * Time: 10:15
 */

namespace tests\Config;


use Rapkg\Config\DefaultConfig;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $config = new DefaultConfig('foo');
        $this->assertEquals('foo', $config->get());
    }
}