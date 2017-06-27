<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 下午12:47
 */

namespace tests\Model;


use Rapkg\Model\Row;
use Rapkg\Model\Table;
use Rapkg\Config\ConfigInterface;

class DBConfig implements ConfigInterface
{
    private static $conf = [
        'test_gsql' => [
            'dsn' => "mysql:dbname=test_gsql;host=127.0.0.1;port=3306;charset=utf8",
            'username' => 'gsql_rw',
            'password' => '1',
            'options' => []
        ],
    ];

    private $databaseName;

    public function __construct($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    public function get()
    {
        return self::$conf[$this->databaseName];
    }
}

class UserTable extends Table
{
    const COL_ID = 'id';
    const COL_EMAIL = 'email';
    const COL_NAME = 'name';
    const COL_STATUS = 'status';
    const COL_UPDATED_AT = 'updated_at';
    const COL_CREATED_AT = 'created_at';

    private $selectColumns = [
        self::COL_ID,
        self::COL_EMAIL,
        self::COL_NAME,
        self::COL_STATUS,
        self::COL_UPDATED_AT,
        self::COL_CREATED_AT,
    ];

    public function __construct()
    {
        parent::__construct('user');
    }

    protected function dbConfig()
    {
        return new DBConfig('test_gsql');
    }

    public function get($id)
    {
        $result = $this->select(
            $this->selectColumns, [self::COL_ID => $id], [], [0, 1]
        );
        if ($result) {
            return $result[0];
        }

        return $result;
    }
}

class UserRow extends Row
{
    /**
     * @var UserTable
     */
    private $table;

    private $id;

    public function __construct($id)
    {
        $this->table = new UserTable();

        if (empty($id)) {
            throw new \InvalidArgumentException('Invalid argument id');
        }
        $this->id = $id;
    }

    protected function cacheKey()
    {
        return $this->table->getTableName() . $this->id;
    }

    public static function create($values = [])
    {
        $id = (new UserTable())->insert($values);
        if ($id) {
            return new UserRow($id);
        }

        return $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function get()
    {
        return $this->table->get($this->id);
    }

    public function update($values)
    {
        return $this->table->update($values, ['id' => $this->id]);
    }

    public function delete()
    {
        return $this->table->delete(['id' => $this->id]);
    }

    public static function count()
    {
        return (new UserTable())->count([]);
    }
}

class RowTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $nowUnix = time();
        $userRow = UserRow::create(
            [
                'email' => $this->randomEmail(),
                'name' => '',
                'updated_at' => $nowUnix,
                'created_at' => $nowUnix,
            ]
        );

        $this->assertNotEmpty($userRow);
        $this->assertNotEmpty($userRow->get());

        return $userRow;
    }

    /**
     * @depends testCreate
     * @param UserRow $userRow
     * @return UserRow
     */
    public function testUpdate(UserRow $userRow)
    {
        $result = $userRow->update(
            [
                'status' => mt_rand(0, 1),
                'updated_at' => time(),
            ]
        );
        $this->assertNotFalse($result);

        return $userRow;
    }

    /**
     * @depends testCreate
     * @param UserRow $userRow
     * @return UserRow
     */
    public function testUpdateStringValues(UserRow $userRow)
    {
        $result = $userRow->update(
            sprintf("`status` = '%d', `updated_at` = '%d'", mt_rand(2, 3), time())
        );
        $this->assertNotEmpty($result);

        return $userRow;
    }

    /**
     * @depends testCreate
     * @param UserRow $userRow
     * @return UserRow
     */
    public function testGet(UserRow $userRow)
    {
        $result = $userRow->get();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('id', $result);

        return $userRow;
    }

    /**
     * @depends testGet
     * @param UserRow $userRow
     */
    public function testDelete(UserRow $userRow)
    {
        $result = $userRow->delete();

        $this->assertNotFalse($result);
    }

    private function randomEmail()
    {
        return microtime(true) . mt_rand(10000, 99999)
            . '@gsql.com';
    }

    public function testCount()
    {
        $this->assertTrue(UserRow::count() > 0);
    }
}