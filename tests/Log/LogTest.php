<?php

/**
 * User: coderd
 * Date: 2017/6/21
 * Time: 9:42
 */

namespace tests\Log;


use Rapkg\Log\Logger;

class LogTest extends \PHPUnit_Framework_TestCase
{
    public function testLogger()
    {
        $testFilePath = '/tmp/rapkg_log_test_' . date('YmdHis')
            . mt_rand(1000, 9999) . '.log';

        $logger = new Logger($testFilePath);
        $logger->error('foo');
        $logger->flush();

        $result = file_get_contents($testFilePath);
        unlink($testFilePath);

        $this->assertContains('foo', $result);
    }
}