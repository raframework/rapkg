<?php
/**
 * User: coderd
 * Date: 2017/4/6
 * Time: 18:00
 */

namespace Rapkg\Sql;


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

    public function __construct(array $config)
    {
        $options = array_diff_key($this->defaultOptions, $config['options']) + $config['options'];

        $this->pdo = new \PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            $options
        );
    }

    public function ExecReturningRows(QueryInterface $query)
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

    public function ExecWithoutReturningRows(QueryInterface $query)
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
}