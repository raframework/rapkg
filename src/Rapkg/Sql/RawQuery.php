<?php
/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 17:57
 */

namespace Rapkg\Sql;


class RawQuery implements QueryInterface
{
    private $queryString;
    private $args;

    public function __construct($queryString, array $args = [])
    {
        $this->queryString = $queryString;
        $this->args = $args;
    }

    public function string()
    {
        return $this->queryString;
    }

    public function args()
    {
        return $this->args;
    }
}