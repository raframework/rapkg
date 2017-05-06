<?php
/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 18:00
 */

namespace Rapkg\Sql;


use Rapkg\Config\ConfigInterface;

class DB
{
    protected $defaultOptions = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * DB constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $conf = $config->get();
        $options = array_diff_key($this->defaultOptions, $conf['options']) + $conf['options'];

        $this->pdo = new \PDO(
            $conf['dsn'],
            $conf['username'],
            $conf['password'],
            $options
        );
    }

    public function execReturningRows(QueryInterface $query)
    {
        $stmt = $this->pdo->prepare($query->string());
        if ($stmt === false) {
            return false;
        }
        if ($stmt->execute($query->args()) === false) {
            return false;
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param QueryInterface $query
     * @return array|bool
     */
    public function execWithoutReturningRows(QueryInterface $query)
    {
        $stmt = $this->pdo->prepare($query->string());
        if ($stmt === false) {
            return false;
        }
        if ($stmt->execute($query->args()) === false) {
            return false;
        }

        return [
            'row_count' => $stmt->rowCount(),
            'last_insert_id' => $this->pdo->lastInsertId(),
        ];
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }
}