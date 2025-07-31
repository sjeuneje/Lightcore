<?php

namespace Core\Database;

use RuntimeException;

class DB
{
    /**
     * Connection to the DB.
     *
     * @var Connection|null
     */
    private static ?Connection $connection = null;

    public static function table(string $table): QueryBuilder
    {
        return new QueryBuilder(self::getConnection(), $table);
    }

    public static function setConnection(Connection $connection): void
    {
        self::$connection = $connection;
    }

    public static function getConnection(): Connection
    {
        if (self::$connection === null) {
            throw new RuntimeException('Database connection not set. Call DB::setConnection() first.');
        }

        return self::$connection;
    }
}