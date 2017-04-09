<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 上午9:25
 */

namespace Rapkg\Model;


abstract class Row
{
    protected $cache;
    protected $cacheTimeout;

    abstract protected function cacheKey();

    protected function withCache($value)
    {
        $this->cache = $value;
    }

    protected function cache()
    {
        return $this->cache;
    }

    protected function withCacheTimeout($timeout)
    {
        $this->cacheTimeout = $timeout;
    }

    protected function cacheTimeout()
    {
        return $this->cacheTimeout;
    }
}