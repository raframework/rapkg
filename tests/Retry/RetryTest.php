<?php

/**
 * User: coderd
 * Date: 2016/12/2
 * Time: 19:33
 */

use Rapkg\Retry\Retry;
use Rapkg\Retry\RetryException;
use Rapkg\Retry\RetryWithPreviousException;

class RetryTest extends PHPUnit_Framework_TestCase
{
    public function testRetryException()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1.0,
        ];

        $func = function() use (&$i) {
            $i++;
            throw new RetryException();
        };

        $result = Retry::call(
            $func,
            [],
            $options
        );

        $this->assertSame(2, $i);
        $this->assertNull($result);
    }

    public function testArgs()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];

        $func = function($arg1, $arg2) use (&$i) {
            $i++;
            return $arg1 + $arg2;
        };

        $args = [2, 3];
        $return = Retry::call($func, $args, $options);

        $this->assertSame(1, $i);
        $this->assertSame(5, $return);
    }

    public function testDefaultArgs()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];

        $func = function($arg1, $arg2 = 3) use (&$i) {
            $i++;
            return $arg1 + $arg2;
        };

        $args = [2];
        $return = Retry::call($func, $args, $options);

        $this->assertSame(1, $i);
        $this->assertSame(5, $return);
    }

    public function testPassMoreArgs()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];

        $func = function($arg1, $arg2) use (&$i) {
            $i++;
            return $arg1 + $arg2;
        };

        $args = [2, 3, 8];
        $return = Retry::call($func, $args, $options);

        $this->assertSame(1, $i);
        $this->assertSame(5, $return);
    }

    public function testReturnByException()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];

        $func = function($arg1, $arg2) use (&$i) {
            $i++;
            $return = $arg1 + $arg2;
            throw new RetryException($return);
        };

        $args = [2, 3];
        $return = Retry::call($func, $args, $options);

        $this->assertSame(2, $i);
        $this->assertSame(5, $return);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.retries must be an integer with a positive value
     */
    public function testInvalidOptionRetriesEmpty()
    {
        $i = 0;
        $options = [
            'interval' => 0.01,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.retries must be an integer with a positive value
     */
    public function testInvalidOptionRetriesZero()
    {
        $i = 0;
        $options = [
            'retries' => 0,
            'interval' => 0.01,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.retries must be an integer with a positive value
     */
    public function testInvalidOptionRetriesInvalidType()
    {
        $i = 0;
        $options = [
            'retries' => 2.0,
            'interval' => 0.01,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval must be a float with a positive value
     */
    public function testInvalidOptionIntervalEmpty()
    {
        $i = 0;
        $options = [
            'retries' => 2,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval must be a float with a positive value
     */
    public function testInvalidOptionIntervalZero()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.0,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval must be a float with a positive value
     */
    public function testInvalidOptionIntervalInvalidType()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1,
        ];

        $func = function() use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    public function testSetGlobalOptions()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];
        Retry::setGlobalOptions($options);

        $func = function() use (&$i) {
            $i++;
            throw new RetryException();
        };

        Retry::call($func);

        $this->assertSame(2, $i);
    }

    public function testSetGlobalOptionsWithGivenOptions()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.01,
        ];
        Retry::setGlobalOptions($options);

        $func = function() use (&$i) {
            $i++;
            throw new RetryException();
        };

        Retry::call(
            $func,
            [],
            [
                'retries' => 3,
            ]
        );

        $this->assertSame(3, $i);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testRetryWithPreviousException()
    {
        $i = 0;
        $func = function() use (&$i) {
            $i++;
            throw new RetryWithPreviousException(new RuntimeException());
        };

        try {
            Retry::call(
                $func,
                [],
                [
                    'retries' => 2,
                    'interval' => 0.01,
                ]
            );
        } finally {
            $this->assertSame(2, $i);
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testNoRetryExceptionButOtherException()
    {
        $i = 0;
        $func = function() use (&$i) {
            $i++;
            throw new \Exception();
        };

        try {
            Retry::call(
                $func,
                [],
                [
                    'retries' => 2,
                    'interval' => 0.01,
                ]
            );
        } finally {
            $this->assertSame(1, $i);
        }
    }

    public function testNoRetryExceptionButReturningValue()
    {
        $i = 0;
        $func = function() use (&$i) {
            $i++;
            return $i;
        };

        $result = Retry::call(
            $func,
            [],
            [
                'retries' => 2,
                'interval' => 0.01,
            ]
        );

        $this->assertSame(1, $i);
        $this->assertSame(1, $result);
    }

    public function testNoRetryExceptionButReturningNull()
    {
        $i = 0;
        $func = function() use (&$i) {
            $i++;
        };

        $result = Retry::call(
            $func,
            [],
            [
                'retries' => 2,
                'interval' => 0.01,
            ]
        );

        $this->assertSame(1, $i);
        $this->assertNull($result);
    }
}