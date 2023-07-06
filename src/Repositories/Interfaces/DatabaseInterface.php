<?php

interface DatabaseInterface
{
    public function connect(string $db, string $dbname, string $host, string $username, string $password): void;

    public function insert(string $table, mixed $data): void;

    public function read(string $table, mixed $data): void;

    public function update(string $table, mixed $data): void;

    public function delete(string $table, mixed $data): void;

    public function select(mixed ...$columns): string;

    public function where(string $column, string $operator, mixed $value): string;

    public function orderBy(string $column, string $order): string;

    public function getLastInsertId(): int;
}