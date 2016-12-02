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
     * @param $retries
     * @param callable $func
     * @return mixed|null
     */
    public static function run($retries, callable $func)
    {
        beginning:
        try {
            return $func();
        } catch (RetryException $e) {
            $retries--;
            if ($retries == 0) {
                return $e->getReturn();
            }

            goto beginning;
        }
    }
}