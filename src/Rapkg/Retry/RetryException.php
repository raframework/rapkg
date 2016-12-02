<?php
/**
 * User: coderd
 * Date: 2016/12/2
 * Time: 17:57
 */

namespace Rapkg\Retry;


use Exception;

class RetryException extends \Exception
{
    /**
     * @var mixed
     */
    private $return = null;

    public function __construct($return = null)
    {
        parent::__construct();

        $this->return = $return;
    }

    public function getReturn()
    {
        return $this->return;
    }
}