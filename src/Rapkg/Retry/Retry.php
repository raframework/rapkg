<?php
/**
 * User: coderd
 * Date: 2016/12/2
 * Time: 14:59
 */

namespace Rapkg\Retry;


class Retry
{
    /**
     * @var array
     */
    private static $globalOptions = [];


    /**
     * Call the given function `$func`, and retry when the `RetryException` is thrown.
     *
     * @param callable $func function to be called
     * @param array $args args to be passed to the function `$func`
     * @param array $options options defines the `retries`(retry times) and `interval`(retry interval).
     * @return mixed
     */
    public static function call(callable $func, array $args = [], array $options = [])
    {
        $options = array_merge(self::$globalOptions, $options);
        self::checkOptions($options);
        $retries = $options['retries'];

        beginning:
        try {
            return call_user_func_array($func, $args);
        } catch (RetryException $e) {
            $retries--;
            if ($retries == 0) {
                return $e->getReturn();
            }

            usleep((int)$options['interval'] * 1e6);
            goto beginning;
        }
    }

    /**
     * Set global options.
     *
     * @param array $options
     */
    public static function setGlobalOptions(array $options)
    {
        self::checkOptions($options);
        self::$globalOptions = $options;
    }

    /**
     * Check the options, throw `InvalidArgumentException` on invalid format value.
     *
     * @param $options
     * @throws \InvalidArgumentException
     */
    private static function checkOptions($options)
    {
        if (!isset($options['retries']) || !is_int($options['retries']) || $options['retries'] <= 0) {
            throw new \InvalidArgumentException(
                "retry: the options.retries with type of integer and positive value should be provided"
            );
        }
        if (!isset($options['interval']) || !is_float($options['interval']) || $options['interval'] <= 0.0) {
            throw new \InvalidArgumentException(
                "retry: the options.interval with type of float and positive value should be provided"
            );
        }
    }
}