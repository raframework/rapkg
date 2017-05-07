<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 上午9:25
 */

namespace Rapkg\Model;


use Rapkg\Sql\DB;
use Rapkg\Sql\Query;
use Rapkg\Sql\QueryInterface;
use Rapkg\Config\ConfigInterface;

abstract class Table
{
    /**
     * @var DB
     */
    protected $db;

    /**
     * @var string
     */
    private $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $this->db = Manager::db($this->dbConfig());
    }

    /**
     * @return ConfigInterface
     */
    abstract protected function dbConfig();

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param array|string $columns
     * @param array $wheres
     * @param array $orders
     * @param array $limit
     * @return array|bool
     */
    public function select(
        $columns,
        array $wheres = [],
        array $orders = [],
        array $limit = []
    )
    {
        $offset = isset($limit[0]) ? $limit[0] : 0;
        $rowCount = isset($limit[1]) ? $limit[1] : 0;

        $q = (new Query())
            ->table($this->tableName)
            ->select($columns)
            ->where($wheres)
            ->orderBy($orders)
            ->limit($offset, $rowCount);

        return $this->db->execReturningRows($q);
    }

    /**
     * @param array|string $columns
     * @param array $wheres
     * @param array $orders
     * @return array|bool|mixed|null
     */
    public function first(
        $columns,
        array $wheres = [],
        array $orders = []
    )
    {
        $result = $this->select($columns, $wheres, $orders, [0, 1]);

        // Failure
        if ($result === false) {
            return $result;
        }

        return isset($result[0]) ? $result[0] : null;
    }

    public function count(array $wheres)
    {
        $result = $this->select('count(*) AS `count`', $wheres);
        if ($result === false) {
            return false;
        }

        if (isset($result[0]['count'])) {
            return $result[0]['count'];
        }

        return false;
    }

    public function insert(array $values)
    {
        $q = (new Query())
            ->table($this->tableName)
            ->insert($values);

        $result = $this->db->execWithoutReturningRows($q);
        if (empty($result)) {
            return $result;
        }

        return $result['last_insert_id'];
    }

    public function bulkInsert(array $columns, array $values)
    {
        $q = (new Query())
            ->table($this->tableName)
            ->bulkInsert($columns, $values);

        return $this->db->execWithoutReturningRows($q);
    }

    public function update(
        array $values,
        array $wheres = [],
        array $orders = [],
        array $limit = []
    )
    {
        $offset = isset($limit[0]) ? $limit[0] : 0;
        $rowCount = isset($limit[1]) ? $limit[1] : 0;

        $q = (new Query())
            ->table($this->tableName)
            ->where($wheres)
            ->orderBy($orders)
            ->limit($offset, $rowCount)
            ->update($values);

        $result = $this->db->execWithoutReturningRows($q);
        if (empty($result)) {
            return $result;
        }

        return $result['row_count'];
    }

    public function delete(array $wheres, array $orders = [], array $limit = [])
    {
        $offset = isset($limit[0]) ? $limit[0] : 0;
        $rowCount = isset($limit[1]) ? $limit[1] : 0;

        $q = (new Query())
            ->table($this->tableName)
            ->where($wheres)
            ->orderBy($orders)
            ->limit($offset, $rowCount)
            ->delete();

        $result = $this->db->execWithoutReturningRows($q);
        if (empty($result)) {
            return $result;
        }

        return $result['row_count'];
    }

    protected function execReturningRows(QueryInterface $query)
    {
        return $this->db->execReturningRows($query);
    }

    protected function execWithoutReturningRows(QueryInterface $query)
    {
        return $this->db->execWithoutReturningRows($query);
    }
}