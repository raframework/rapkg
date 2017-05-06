<?php

/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 19:34
 */


use Rapkg\Sql\DB;
use Rapkg\Sql\Query;
use Rapkg\Sql\RawQuery;
use Rapkg\Config\ConfigInterface;

class DBConfig implements ConfigInterface
{
    public function get()
    {
        $database = 'test_gsql';
        $host = '127.0.0.1';
        $port = 3306;
        $charset = 'utf8';
        $config = [
            'dsn' =>  "mysql:dbname={$database};host={$host};port={$port};"
                . "charset={$charset}",
            'username' => 'gsql_rw',
            'password' => '1',
            'options' => []
        ];

        return $config;
    }
}

class SqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return DB
     */
    private function createDB()
    {
        return new DB(new DBConfig());
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

        $result = $db->execReturningRows($q);
        $this->assertNotEmpty($result);
    }

    public function testRawQuerySelect()
    {
        $db = $this->createDB();
        $q = new RawQuery(
            "SELECT `id`, `email`, `name`, `status`, `updated_at`, `created_at`"
            . " FROM `user` where `id` < ? AND `status` = ?"
            . " ORDER BY `id` DESC LIMIT 0, 10",
            [100000, 0]
        );

        $result = $db->execReturningRows($q);
        $this->assertNotEmpty($result);
    }

    private function randomEmail()
    {
        return microtime(true) . mt_rand(10000, 99999) . '@gsql.com';
    }

    public function testInsert()
    {
        $db = $this->createDB();
        $nowUnix = time();
        $q = (new Query())
            ->table('user')
            ->insert([
                'email' => $this->randomEmail(),
                'name' => '',
                'updated_at' => $nowUnix,
                'created_at' => $nowUnix,
            ]);

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['last_insert_id']);
    }

    public function testRawQueryInsert()
    {
        $db = $this->createDB();
        $nowUnix = time();
        $q = new RawQuery(
            "INSERT INTO `user` (`email`, `name`, `updated_at`, `created_at`) "
            . "VALUES (?, ?, ?, ?)",
            [
                $this->randomEmail(), "", $nowUnix, $nowUnix
            ]
        );

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['last_insert_id']);
    }

    public function testUpdate()
    {
        $db = $this->createDB();
        $q = (new Query())
            ->table('user')
            ->update([
                'status' => mt_rand(0, 1),
                'updated_at' => time(),
            ])
            ->where([
                'id' => ['>', 1],
            ])
            ->orderBy([
                'id' => 'DESC',
            ])->limit(0, 5);

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['row_count']);
    }


    public function testRawQueryUpdate()
    {
        $db = $this->createDB();
        $nowUnix = time();
        $q = new RawQuery(
            "UPDATE `user` SET `status` = ?, `updated_at` = ? WHERE `id` > ?"
            . " ORDER BY `id` DESC LIMIT 5",
            [mt_rand(0, 1), $nowUnix, 1]
        );

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['row_count']);
    }

    public function testDelete()
    {
        $db = $this->createDB();
        $q = (new Query())
            ->table('user')
            ->delete()
            ->where([
                'id' => ['>', 1],
            ])
            ->orderBy([
                'id' => 'DESC',
            ])->limit(0, 1);

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['row_count']);
    }


    public function testRawQueryDelete()
    {
        $db = $this->createDB();
        $q = new RawQuery(
            "DELETE FROM `user` WHERE `id` > ? ORDER BY `id` DESC LIMIT 1",
            [1]
        );

        $result = $db->execWithoutReturningRows($q);
        $this->assertNotEmpty($result['row_count']);
    }

    public function testTransactionCommit()
    {
        $db = $this->createDB();
        $beginTxResult = $db->beginTransaction();
        $this->assertTrue($beginTxResult);

        $nowUnix = time();
        $q = new RawQuery(
            "INSERT INTO `user` (`email`, `name`, `updated_at`, `created_at`) "
            . "VALUES (?, ?, ?, ?)",
            [
                $this->randomEmail(), "", $nowUnix, $nowUnix
            ]
        );
        $insertResult = $db->execWithoutReturningRows($q);
        $this->assertNotFalse($insertResult);

        $this->assertTrue($db->inTransaction());

        $commitResult = $db->commit();
        $this->assertTrue($commitResult);

        $selectResult = $db->execReturningRows(
            new RawQuery(
                'SELECT * FROM `user` where `id` = ?',
                [$insertResult['last_insert_id']])
        );
        $this->assertNotEmpty($selectResult);
        $this->assertEquals($insertResult['last_insert_id'], $selectResult[0]['id']);
    }

    public function tesTransactionRollback()
    {
        $db = $this->createDB();
        $beginTxResult = $db->beginTransaction();
        $this->assertTrue($beginTxResult);

        $nowUnix = time();
        $q = new RawQuery(
            "INSERT INTO `user` (`email`, `name`, `updated_at`, `created_at`) "
            . "VALUES (?, ?, ?, ?)",
            [
                $this->randomEmail(), "", $nowUnix, $nowUnix
            ]
        );
        $insertResult = $db->execWithoutReturningRows($q);
        $this->assertNotFalse($insertResult);

        $this->assertTrue($db->inTransaction());

        $rollbackResult = $db->rollback();
        $this->assertTrue($rollbackResult);

        $selectResult = $db->execReturningRows(
            new RawQuery(
                'SELECT * FROM `user` where `id` = ?',
                [$insertResult['last_insert_id']])
        );
        $this->assertEmpty($selectResult);
    }
}