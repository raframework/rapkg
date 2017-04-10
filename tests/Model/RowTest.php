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

    public function get($id)
    {
        $result = $this->select(
            $this->selectColumns, [self::COL_ID => $id], [0, 1]
        );
        if ($result) {
            return $result[0];
        }

        return $result;
    }

    protected function dbConfig()
    {
        return new DBConfig('test_gsql');
    }

    public function tableName()
    {
        return 'user';
    }

    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

class UserRow extends Row
{
    /**
     * @var UserTable
     */
    private static $table;

    private $id;

    public function __construct($id)
    {
        self::$table = UserTable::getInstance();

        if (empty($id)) {
            throw new \InvalidArgumentException('Invalid argument id');
        }
        $this->id = $id;
    }

    protected function cacheKey()
    {
        return self::$table->tableName() . $this->id;
    }

    public static function create($values = [])
    {
        $id = UserTable::getInstance()->insert($values);
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
        return self::$table->get($this->id);
    }

    public function update($values)
    {
        return self::$table->update($values, ['id' => $this->id]);
    }

    public function delete()
    {
        return self::$table->delete(['id' => $this->id]);
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
}