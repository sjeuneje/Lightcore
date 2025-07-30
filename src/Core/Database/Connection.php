<?php

namespace Core\Database;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database connection wrapper for PDO.
 *
 * Provides a simple interface for database connectivity with automatic
 * connection management and PDO instance access.
 *
 * @version 1.0.0
 */
class Connection
{
    /**
     * PDO database connection instance.
     *
     * @var PDO|null
     */
    private ?PDO $pdo = null;

    /**
     * Database configuration parameters.
     *
     * Expected keys: DB_HOST, DB_NAME, DB_USER, DB_PASS
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Current connection state flag.
     *
     * @var bool
     */
    private bool $isConnected = false;

    /**
     * Initialize connection with database configuration.
     *
     * @param array<string, mixed> $config Database configuration array
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Establish database connection using PDO.
     *
     * @return void
     * @throws PDOException When connection fails
     */
    public function connect(): void
    {
        $dsn = "mysql:host={$this->config['DB_HOST']};dbname={$this->config['DB_NAME']}";

        try {
            $this->pdo = new PDO($dsn, $this->config['DB_USER'], $this->config['DB_PASS']);
            $this->isConnected = true;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->prepare($sql);
        $this->execute($statement, $params);

        return $statement;
    }

    /**
     * Prepare an SQL statement for execution.
     *
     * @param string $sql SQL statement with parameter placeholders
     * @return PDOStatement Prepared statement ready for execution
     * @throws PDOException When preparation fails
     */
    public function prepare(string $sql): PDOStatement
    {
        return $this->getPdo()->prepare($sql);
    }


    /**
     * Execute a prepared statement with optional parameters.
     *
     * @param PDOStatement $statement Prepared PDO statement
     * @param array<string|int, mixed> $params Parameters to bind (named or positional)
     * @return bool True on success, false on failure
     * @throws PDOException When execution fails
     */
    public function execute(PDOStatement $statement, array $params = []): bool
    {
        return $statement->execute($params);
    }

    /**
     * Check if database connection is established.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    /**
     * Get PDO instance with automatic connection.
     *
     * Automatically establishes connection if not already connected.
     *
     * @return PDO Active PDO database connection
     * @throws PDOException When connection fails
     */
    public function getPdo(): PDO
    {
        if (!$this->isConnected || $this->pdo === null) {
            $this->connect();
        }

        return $this->pdo;
    }

    /**
     * Close database connection and cleanup resources.
     *
     * @return void
     */
    public function disconnect(): void
    {
        if ($this->pdo !== null) {
            $this->pdo = null;
        }

        $this->isConnected = false;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
