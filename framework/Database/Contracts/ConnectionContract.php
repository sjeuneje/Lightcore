<?php

namespace Lightcore\Framework\Database\Contracts;

/**
 * Interface defining a contract for database connection classes.
 */
interface ConnectionContract
{
    public function getPdo(): \PDO;
}