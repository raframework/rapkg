<?php
/**
 * User: coderd
 * Date: 2017/6/21
 * Time: 10:12
 */

namespace Rapkg\Config;


class DefaultConfig implements ConfigInterface
{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }
}