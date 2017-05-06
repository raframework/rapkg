<?php
/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 17:25
 */

namespace Rapkg\Sql;


interface QueryInterface
{
    public function string();
    public function args();
}