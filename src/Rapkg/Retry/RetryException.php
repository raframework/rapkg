<?php
/**
 * User: coderd
 * Date: 2017/4/22
 * Time: 上午12:38
 */

namespace Rapkg\Retry;


/**
 * `RetryException` used to retry the function called by `Retry::call()` on a value should be returned or none.
 * Throw a `RetryException` in the function called by `Retry::call` will trigger retries.
 *
 * Class RetryException
 * @package Rapkg\Retry
 */
class RetryException extends \Exception
{
    /**
     * @var mixed Returned value set by the called function.
     */
    private $return = null;

    /**
     * RetryException constructor.
     * @param mixed $return Returned value should be returned in the called function.
     *                      It will be returned by `Retry::call()` when the last retry fails.
     */
    public function __construct($return = null)
    {
        $this->return = $return;
    }

    /**
     * @return mixed|null Return the returned value passed by the called function.
     */
    public function getReturn()
    {
        return $this->return;
    }
}