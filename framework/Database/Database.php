<?php

namespace Lightcore\Framework\Database;

use Lightcore\Framework\Database\QueryBuilder;
use Lightcore\Framework\Database\Abstracts\Connection;
use Lightcore\Framework\Database\Contracts\BuilderContract;

class DB
{
    public static function table(string $tableName): BuilderContract
    {
        $config = [
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ];

        $connection = new Connection($config['host'], $config['database'], $config['username'], $config['password']);
        return new QueryBuilder($connection, $tableName);
    }
}