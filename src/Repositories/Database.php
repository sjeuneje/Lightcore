<?php

abstract class Database implements DatabaseInterface
{
    private Database $db;

    private string $dbname;

    private string $host;

    private string $username;

    private string $password;

    public function __construct(string $db, string $dbname, string $host, string $username, string $password)
    {
        $this->db = $db;
        $this->dbname = $dbname;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect(string $db, string $dbname, string $host, string $username, string $password): void
    {
        //
    }

    public function insert(string $table, mixed $data): void
    {
        //
    }

    public function read(string $table, mixed $data): void
    {
        //
    }

    public function update(string $table, mixed $data): void
    {
        //
    }

    public function delete(string $table, mixed $data): void
    {
        //
    }

    public function select(...$columns): string
    {
        return "";
    }

    public function where(string $column, string $operator, mixed $value): string
    {
        return "";
    }

    public function orderBy(string $column, string $order = 'DESC'): string
    {
        return "";
    }

    public function getLastInsertId(): int
    {
        return 0;
    }
}