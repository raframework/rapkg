<?php

/**
 * User: coderd
 * Date: 2016/12/2
 * Time: 19:33
 */

use Rapkg\Retry\Retry;
use Rapkg\Retry\RetryException;

class RetryTest extends PHPUnit_Framework_TestCase
{
    public function testRetry()
    {
        $i = 0;
        Retry::run(2, function() use (&$i) {
            $i++;
            throw new RetryException();
        });

        $this->assertSame(2, $i);
    }
}