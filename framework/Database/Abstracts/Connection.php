<?php

namespace Lightcore\Framework\Database\Abstracts;

use Lightcore\Framework\Database\Contracts\ConnectionContract;

abstract class Connection implements ConnectionContract
{
    protected \PDO $pdo;

    public function __construct(string $host, string $database, string $username, string $password)
    {
        $dsn = "mysql:host={$host};dbname={$database}";
        $this->pdo = new \PDO($dsn, $username, $password);
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}