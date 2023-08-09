<?php

namespace Lightcore\Framework\Database\Abstracts;

use Lightcore\Framework\Database\Contracts\BuilderContract;
use Lightcore\Framework\Database\Contracts\ConnectionContract;

abstract class Builder implements BuilderContract
{
    protected ConnectionContract $connection;
    protected string $table;
    protected array $wheres = [];

    public function __construct(ConnectionContract $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    abstract public function where(string $column, string $operator, $value): self;
    abstract public function get(): array;
}