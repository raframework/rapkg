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
    public function testRetryException()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1.0,
        ];
        Retry::call(function () use (&$i) {
            $i++;
            throw new RetryException();
        },
            [],
            $options
        );

        $this->assertSame(2, $i);
    }

    public function testArgs()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1.0,
        ];

        $func = function ($arg1, $arg2) use (&$i) {
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
            'interval' => 1.0,
        ];

        $func = function ($arg1, $arg2 = 3) use (&$i) {
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
            'interval' => 1.0,
        ];

        $func = function ($arg1, $arg2) use (&$i) {
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
            'interval' => 1.0,
        ];

        $func = function ($arg1, $arg2) use (&$i) {
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
     * @expectedExceptionMessage retry: the options.retries with type of integer and positive value should be provided
     */
    public function testInvalidOptionRetriesEmpty()
    {
        $i = 0;
        $options = [
            'interval' => 1.0,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.retries with type of integer and positive value should be provided
     */
    public function testInvalidOptionRetriesZero()
    {
        $i = 0;
        $options = [
            'retries' => 0,
            'interval' => 1.0,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.retries with type of integer and positive value should be provided
     */
    public function testInvalidOptionRetriesInvalidType()
    {
        $i = 0;
        $options = [
            'retries' => 2.0,
            'interval' => 1.0,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval with type of float and positive value should be provided
     */
    public function testInvalidOptionIntervalEmpty()
    {
        $i = 0;
        $options = [
            'retries' => 2,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval with type of float and positive value should be provided
     */
    public function testInvalidOptionIntervalZero()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 0.0,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage retry: the options.interval with type of float and positive value should be provided
     */
    public function testInvalidOptionIntervalInvalidType()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1,
        ];

        $func = function () use (&$i) {
            $i++;
        };
        Retry::call($func, [], $options);
    }

    public function testSetGlobalOptions()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1.0,
        ];
        Retry::setGlobalOptions($options);

        Retry::call(function () use (&$i) {
            $i++;
            throw new RetryException();
        }
        );

        $this->assertSame(2, $i);
    }

    public function testSetGlobalOptionsWithDefaultOptions()
    {
        $i = 0;
        $options = [
            'retries' => 2,
            'interval' => 1.0,
        ];
        Retry::setGlobalOptions($options);

        Retry::call(function () use (&$i) {
            $i++;
            throw new RetryException();
        },
            [],
            [
                'retries' => 5,
            ]
        );

        $this->assertSame(5, $i);
    }

}