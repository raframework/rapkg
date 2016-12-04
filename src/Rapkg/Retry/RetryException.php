<?php
/**
 * User: coderd
 * Date: 2016/12/2
 * Time: 17:57
 */

namespace Rapkg\Retry;


use Exception;

/**
 * `RetryException` used to retry the called function while something wrong.
 * Throw a `RetryException` in the function called by `Retry::call` will trigger retries.
 *
 * Class RetryException
 * @package Rapkg\Retry
 */
class RetryException extends \Exception
{
    /**
     * Returned value set by the called function.
     *
     * @var mixed
     */
    private $return = null;

    /**
     * New RetryException instance.
     *
     * RetryException constructor.
     * @param mixed $return  returned value should be returned in the called function.
     */
    public function __construct($return = null)
    {
        parent::__construct();

        $this->return = $return;
    }

    /**
     * Return the returned value passed by the called function.
     *
     * @return mixed|null
     */
    public function getReturn()
    {
        return $this->return;
    }
}