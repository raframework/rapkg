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
     * Call the given function `$func`,
     * retry when a `RetryException` or `RetryWithPreviousException` is thrown by `$func`,
     * and return what `$func` returned.
     *
     * @param callable $func    Function to be called
     * @param array    $args    Args to be passed to the function `$func`
     * @param array    $options Options defines the `retries`(retry times) and `interval`(retry interval).
     *                          It must be an associative in the format:
     *                          [
     *                              'retries' => int    retry times
     *                              'interval' => float retry interval
     *                          ]
     * @return mixed            A value the called function `$func` returned.
     * @throws \Exception
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
        } catch (RetryWithPreviousException $e) {
            $retries--;
            if ($retries == 0) {
                throw $e->getPrevious();
            }
        }

        usleep((int)$options['interval'] * 1e6);
        goto beginning;
    }

    /**
     * Set global options.
     *
     * @param array $options Options defines the `retries`(retry times) and `interval`(retry interval).
     *                       It must be an associative in the format:
     *                       [
     *                           'retries' => int    retry times
     *                           'interval' => float retry interval
     *                       ]
     */
    public static function setGlobalOptions(array $options)
    {
        self::checkOptions($options);
        self::$globalOptions = $options;
    }

    /**
     * Check the options, throw an `InvalidArgumentException` on invalid format value.
     *
     * @param $options
     * @throws \InvalidArgumentException
     */
    private static function checkOptions($options)
    {
        if (!isset($options['retries']) || !is_int($options['retries']) || $options['retries'] <= 0) {
            throw new \InvalidArgumentException(
                "retry: the options.retries must be an integer with a positive value"
            );
        }
        if (!isset($options['interval']) || !is_float($options['interval']) || $options['interval'] <= 0.0) {
            throw new \InvalidArgumentException(
                "retry: the options.interval must be a float with a positive value"
            );
        }
    }
}