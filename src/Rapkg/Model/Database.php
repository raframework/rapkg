<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 上午10:29
 */

namespace Rapkg\Model;


use Rapkg\Sql\DB;
use Rapkg\Sql\QueryInterface;
use Rapkg\Config\ConfigInterface;

class Database
{
    /**
     * @var DB
     */
    protected $sqlDb = null;

    protected function __construct(ConfigInterface $config)
    {
        $this->sqlDb = new DB($config);
    }

    public function execReturningRows(QueryInterface $query)
    {
        return $this->sqlDb->ExecReturningRows($query);
    }

    public function execWithoutReturningRows(QueryInterface $query)
    {
        return $this->sqlDb->ExecWithoutReturningRows($query);
    }

    private static $instances = [];

    /**
     * @param ConfigInterface $config
     * @return Database
     */
    public static function instance(ConfigInterface $config)
    {
        $key = md5(json_encode($config->get()));
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($config);
        }

        return self::$instances[$key];
    }
}