<?php
/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 16:21
 */

namespace Rapkg\Sql;


class Query implements QueryInterface
{
    const ACTION_SELECT      = 1;
    const ACTION_INSERT      = 2;
    const ACTION_UPDATE      = 3;
    const ACTION_DELETE      = 4;
    const ACTION_BULK_INSERT = 5;

    protected $action;

    protected $table = '';
    protected $wheres = [];
    protected $orders = [];
    protected $offset = 0;
    protected $rowCount = 0;
    /**
     * @var array|string
     */
    protected $columns;
    protected $values = [];

    protected $isProcessed = false;
    protected $processed = [
        'where_expr' => '',
        'where_args' => [],
        'value_expr' => '',
        'value_args' => [],
        'order_expr' => '',

        'query_string' => '',
        'args' => [],
    ];

    /**
     * @param $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param array|string $wheres
     * @return $this
     */
    public function where($wheres)
    {
        $this->wheres = $wheres;

        return $this;
    }

    /**
     * @param array $orders
     * @return $this
     */
    public function orderBy(array $orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param $offset
     * @param $rowCount
     * @return $this
     */
    public function limit($offset, $rowCount)
    {
        $this->offset = $offset;
        $this->rowCount = $rowCount;

        return $this;
    }

    /**
     * @param array|string $columns
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function select($columns)
    {
        if (empty($columns)) {
            throw new \InvalidArgumentException(
                'Query->select() method requires a not-empty argument `$columns`.'
            );
        }

        $this->action = self::ACTION_SELECT;
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function insert(array $values)
    {
        if (empty($values)) {
            throw new \InvalidArgumentException('Query->insert() method requires at least 1 value, got 0.');
        }

        $this->action = self::ACTION_INSERT;
        $this->values = $values;

        return $this;
    }

    public function bulkInsert(array $columns, array $values)
    {
        if (empty($columns)) {
            throw new \InvalidArgumentException(
                'Query->bulkInsert() method requires at least 1 column, got 0.'
            );
        }
        if (empty($values)) {
            throw new \InvalidArgumentException(
                'Query->bulkInsert() method requires at least 1 value, got 0.'
            );
        }

        $this->action = self::ACTION_BULK_INSERT;
        $this->columns = $columns;
        $this->values = $values;

        return $this;
    }

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function update(array $values)
    {
        if (empty($values)) {
            throw new \InvalidArgumentException(
                'Query->update() method requires at least 1 value, got 0.'
            );
        }

        $this->action = self::ACTION_UPDATE;
        $this->values = $values;

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $this->action = self::ACTION_DELETE;

        return $this;
    }

    public function string()
    {
        $this->process();

        return $this->processed['query_string'];
    }

    public function args()
    {
        $this->process();

        return $this->processed['args'];
    }

    protected function process()
    {
        if ($this->isProcessed) {
            return;
        }

        switch ($this->action) {
            case self::ACTION_SELECT:
                $this->processSelect();
                break;
            case self::ACTION_INSERT:
                $this->processInsert();
                break;
            case self::ACTION_UPDATE:
                $this->processUpdate();
                break;
            case self::ACTION_DELETE:
                $this->processDelete();
                break;
            case self::ACTION_BULK_INSERT:
                $this->processBulkInsert();
                break;
            default:
                throw new \RuntimeException(
                    'gsql: you must call one of these methods: '
                    . 'Query->[select(), insert(), update(), delete()] first.'
                );
        }

        $this->isProcessed = true;
    }

    protected function processSelect()
    {
        if (is_array($this->columns)) {
            $columns = [];
            foreach ($this->columns as $column) {
                $columns[] = '`' . $column . '`';
            }
            $select = implode(', ', $columns);
        } else {
            $select = $this->columns;
        }

        $this->processWheres();
        $this->processOrders();

        $queryString = sprintf('SELECT %s FROM %s', $select, $this->table);
        if ($this->processed['where_expr'] != '') {
            $queryString .= ' WHERE ' . $this->processed['where_expr'];
        }
        if ($this->processed['order_expr'] != '') {
            $queryString .= ' ORDER BY ' . $this->processed['order_expr'];
        }

        if ($this->rowCount > 0) {
            $queryString .= ' LIMIT ' . $this->offset . ', ' . $this->rowCount;
        }

        $this->processed['query_string'] = $queryString;
        $this->processed['args'] = $this->processed['where_args'];
    }

    protected function processInsert()
    {
        $this->processInsertValues();

        $this->processed['query_string'] = 'INSERT INTO `' . $this->table . '` ' . $this->processed['value_expr'];
        $this->processed['args'] = $this->processed['value_args'];
    }

    protected function processUpdate()
    {
        $this->processUpdateValues();
        $this->processWheres();

        $queryString = sprintf('UPDATE `%s` %s', $this->table, $this->processed['value_expr']);
        if ($this->processed['where_expr'] != '') {
            $queryString .= ' WHERE ' . $this->processed['where_expr'];
        }
        if ($this->processed['order_expr'] != '') {
            $queryString .= ' ORDER BY ' . $this->processed['order_expr'];
        }

        if ($this->rowCount > 0) {
            $queryString .= ' LIMIT ' . $this->rowCount;
        }

        $this->processed['query_string'] = $queryString;
        if ($this->processed['value_args']) {
            $this->processed['args'] = array_merge($this->processed['args'], $this->processed['value_args']);
        }
        if ($this->processed['where_args']) {
            $this->processed['args'] = array_merge($this->processed['args'], $this->processed['where_args']);
        }
    }

    protected function processDelete()
    {
        $this->processWheres();

        $queryString = 'DELETE FROM ' . $this->table;
        if ($this->processed['where_expr'] != '') {
            $queryString .= ' WHERE ' . $this->processed['where_expr'];
        }
        if ($this->processed['order_expr'] != '') {
            $queryString .= ' ORDER BY ' . $this->processed['order_expr'];
        }
        if ($this->rowCount > 0) {
            $queryString .= ' LIMIT ' . $this->rowCount;
        }

        $this->processed['query_string'] = $queryString;
        $this->processed['args'] = $this->processed['where_args'];
    }

    protected function processBulkInsert()
    {
        $cols = [];
        $columnCount = count($this->columns);
        foreach ($this->columns as $column) {
            $cols[] = '`' . $column . '`';
        }

        $args = [];
        $exprList = [];
        foreach ($this->values as $value) {
            if (!is_array($value) || count($value) !== $columnCount) {
                throw new \InvalidArgumentException(
                    'gsql: invalid argument `$values` for bulkInsert()'
                );
            }
            $exprList[] = '(' . rtrim(str_repeat('?, ', $columnCount), ', ') . ')';
            $args = array_merge($args, $value);
        }

        $this->processed['query_string'] = 'INSERT INTO `' . $this->table
            . '` (' . implode(', ', $cols) . ') VALUES '
            . implode(', ', $exprList);
        $this->processed['args'] = $args;
    }

    protected function processWheres()
    {
        if (empty($this->wheres) || (!is_array($this->wheres) && !is_string($this->wheres))) {
            return;
        }

        if (is_string($this->wheres)) {
            $this->processed['where_expr'] = $this->wheres;
            $this->processed['where_args'] = [];
            return;
        }

        $expr = '';
        $args = [];
        $i = 0;
        foreach ($this->wheres as $column => $value) {
            $i++;
            if ($i > 1) {
                $expr .= ' AND ';
            }
            if (is_array($value)) {
                if ($value[0] === 'IN') {
                    if (!is_array($value[1])) {
                        throw new \InvalidArgumentException(
                            'gsql: the value of IN operator should be an array'
                        );
                    }
                    $vLen = count($value[1]);
                    $expr .= '`' . $column. '` IN ('
                        . ltrim(str_repeat(', ?', $vLen), ', ') . ')';
                    $args = array_merge($args, $value[1]);
                } else {
                    $expr .= '`' . $column . '` ' . $value[0] . ' ?';
                    $args[] = $value[1];
                }
            } else {
                $expr .= '`' . $column . '` = ?';
                $args[] = $value;
            }
        }

        $this->processed['where_expr'] = $expr;
        $this->processed['where_args'] = $args;
    }

    protected function processInsertValues()
    {
        if (empty($this->values) || !is_array($this->values)) {
            return;
        }

        $columns = [];
        $markers = [];
        $args = [];
        foreach ($this->values as $column => $value) {
            $columns[] = '`' . $column . '`';
            $markers[] = '?';
            $args[] = $value;
        }

        $this->processed['value_expr'] = '(' . implode(',', $columns)
            . ') VALUES (' . implode(', ', $markers) . ')';
        $this->processed['value_args'] = $args;
    }

    protected function processUpdateValues()
    {
        if (empty($this->values) || !is_array($this->values)) {
            return;
        }

        $args = [];
        $kvs = [];
        foreach ($this->values as $column => $value) {
            $kvs[] = '`' . $column . '` = ?';
            $args[] = $value;
        }

        $this->processed['value_expr'] = 'SET ' . implode(', ', $kvs);
        $this->processed['value_args'] = $args;
    }

    protected function processOrders()
    {
        if (empty($this->orders) || !is_array($this->orders)) {
            return;
        }

        $orders = [];
        foreach ($this->orders as $column => $direction) {
            $orders[] = '`' . $column . '` ' . strtoupper($direction);
        }

        $this->processed['order_expr'] = implode(', ', $orders);
    }
}