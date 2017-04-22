<?php
/**
 * User: coderd
 * Date: 2017/4/22
 * Time: 上午12:35
 */

namespace Rapkg\Retry;


/**
 * `RetryWithPreviousException` used to retry the function called by `Retry::call()` on an exception will be thrown.
 * Throw a `RetryWithPreviousException` in the function called by `Retry::call()` will trigger retries.
 *
 * Class RetryWithPreviousException
 * @package Rapkg\Retry
 */
class RetryWithPreviousException extends \Exception
{
    /**
     * RetryWithPreviousException constructor.
     * @param \Exception $previous The previous exception passed by the called function.
     *                             It will be thrown by `Retry::call()` when the last retry fails.
     */
    public function __construct(\Exception $previous)
    {
        parent::__construct("", 0, $previous);
    }
}