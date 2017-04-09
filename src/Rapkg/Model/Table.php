<?php
/**
 * User: coderd
 * Date: 2017/4/9
 * Time: 上午9:25
 */

namespace Rapkg\Model;


use Rapkg\Sql\Query;
use Rapkg\Config\ConfigInterface;

abstract class Table
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var string
     */
    private $tableName;

    public function __construct()
    {
        $this->db = Database::instance($this->dbConfig());
        $this->tableName = $this->tableName();
    }

    /**
     * @return ConfigInterface
     */
    abstract protected function dbConfig();

    /**
     * @return string
     */
    abstract protected function tableName();

    public function select(
        array $columns,
        array $wheres = [],
        array $orders = [],
        array $limit = []
    )
    {
        $q = (new Query())
            ->table($this->tableName)
            ->select($columns)
            ->where($wheres)
            ->orderBy($orders)
            ->limit($limit[0], $limit[1]);

        return $this->db->execReturningRows($q);
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

    public function update(array $values, array $wheres = [])
    {
        $q = (new Query())
            ->table($this->tableName)
            ->where($wheres)
            ->update($values);

        $result = $this->db->execWithoutReturningRows($q);
        if (empty($result)) {
            return $result;
        }

        return $result['row_count'];
    }

    public function delete(array $wheres)
    {
        $q = (new Query())
            ->table($this->tableName)
            ->where($wheres)
            ->delete();

        $result = $this->db->execWithoutReturningRows($q);
        if (empty($result)) {
            return $result;
        }

        return $result['row_count'];
    }
}