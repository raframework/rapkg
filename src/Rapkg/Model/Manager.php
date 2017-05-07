<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 上午10:29
 */

namespace Rapkg\Model;


use Rapkg\Sql\DB;
use Rapkg\Config\ConfigInterface;

class Manager
{
    private static $dbInstances = [];

    /**
     * db return DB instance initialized with `$dbConfig` given
     *
     * @param ConfigInterface $dbConfig
     * @return DB
     */
    public static function db(ConfigInterface $dbConfig)
    {
        $key = md5(json_encode($dbConfig->get()));
        if (!isset(self::$dbInstances[$key])) {
            self::$dbInstances[$key] = new DB($dbConfig);
        }

        return self::$dbInstances[$key];
    }
}