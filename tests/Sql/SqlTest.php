<?php

/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 19:34
 */


use Rapkg\Sql\DB;
use Rapkg\Sql\Query;

class SqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return DB
     */
    private function createDB()
    {
        $database = 'test_gsql';
        $host = '127.0.0.1';
        $port = 3306;
        $charset = 'utf8';
        $config = [
            'dsn' =>  "mysql:dbname={$database};host={$host};port={$port};charset={$charset}",
            'username' => 'gsql_rw',
            'password' => '1',
            'options' => []
        ];

        return new DB($config);
    }

    public function testSelect()
    {
        $db = $this->createDB();
        $q = (new Query())
            ->table('user')
            ->select([
                'id',
                'email',
                'name',
                'status',
                'updated_at',
                'created_at'
            ])
            ->where([
                'id' => ['<', 100000],
                'status' => 0,
            ])
            ->orderBy([
                'id' => 'DESC',
            ])->limit(0, 10);

        $result = $db->ExecReturningRows($q);
        var_dump($result);
    }

}