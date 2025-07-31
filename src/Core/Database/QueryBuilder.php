<?php

namespace Core\Database;

/**
 * Fluent SQL query builder for database operations.
 *
 * Provides a fluent interface for building and executing SQL queries with
 * support for SELECT, INSERT, UPDATE, DELETE operations, joins, conditions,
 * ordering, and limiting results.
 *
 * @version 1.0.0
 */
class QueryBuilder
{
    /**
     * Database connection instance.
     *
     * @var Connection
     */
    private Connection $conn;

    /**
     * Target table name for the query.
     *
     * @var string
     */
    private string $table;

    /**
     * WHERE conditions for the query.
     *
     * @var array<int, array{type: string, column: string, operator: string}>
     */
    private array $wheres = [];

    /**
     * Selected columns for SELECT queries.
     *
     * @var array<int, string>
     */
    private array $selects = [];

    /**
     * JOIN clauses for the query.
     *
     * @var array<int, array{type: string, table: string, first: string, operator: string, second: string}>
     */
    private array $joins = [];

    /**
     * ORDER BY clauses for the query.
     *
     * @var array<int, array{column: string, direction: string}>
     */
    private array $orderBy = [];

    /**
     * LIMIT value for the query.
     *
     * @var int|null
     */
    private ?int $limit = null;

    /**
     * Parameter bindings for prepared statements.
     *
     * @var array<int, mixed>
     */
    private array $bindings = [];

    /**
     * Initialize query builder with database connection and table.
     *
     * @param Connection $conn Database connection instance
     * @param string $table Target table name
     */
    public function __construct(Connection $conn, string $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    /**
     * Set columns to select in the query.
     *
     * @param string ...$columns Column names to select
     * @return QueryBuilder Current instance for method chaining
     */
    public function select(string ...$columns): QueryBuilder
    {
        $this->selects = $columns;
        return $this;
    }

    /**
     * Add WHERE condition with AND logic.
     *
     * @param string $column Column name
     * @param string $operator Comparison operator (=, >, <, !=, etc.)
     * @param mixed $value Value to compare against
     * @return QueryBuilder Current instance for method chaining
     */
    public function where(string $column, string $operator, mixed $value): QueryBuilder
    {
        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator
        ];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add WHERE condition with OR logic.
     *
     * @param string $column Column name
     * @param string $operator Comparison operator (=, >, <, !=, etc.)
     * @param mixed $value Value to compare against
     * @return QueryBuilder Current instance for method chaining
     */
    public function orWhere(string $column, string $operator, mixed $value): QueryBuilder
    {
        $this->wheres[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator
        ];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add INNER JOIN clause to the query.
     *
     * @param string $table Table to join
     * @param string $first First column for join condition
     * @param string $operator Join operator (usually =)
     * @param string $second Second column for join condition
     * @return QueryBuilder Current instance for method chaining
     */
    public function join(string $table, string $first, string $operator, string $second): QueryBuilder
    {
        $this->joins[] = [
            'type' => 'INNER',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add LEFT JOIN clause to the query.
     *
     * @param string $table Table to join
     * @param string $first First column for join condition
     * @param string $operator Join operator (usually =)
     * @param string $second Second column for join condition
     * @return QueryBuilder Current instance for method chaining
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): QueryBuilder
    {
        $this->joins[] = [
            'type' => 'LEFT',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add RIGHT JOIN clause to the query.
     *
     * @param string $table Table to join
     * @param string $first First column for join condition
     * @param string $operator Join operator (usually =)
     * @param string $second Second column for join condition
     * @return QueryBuilder Current instance for method chaining
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): QueryBuilder
    {
        $this->joins[] = [
            'type' => 'RIGHT',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];
        return $this;
    }

    /**
     * Add ORDER BY clause to the query.
     *
     * @param string $column Column name to order by
     * @param string $direction Sort direction (ASC or DESC)
     * @return QueryBuilder Current instance for method chaining
     */
    public function orderBy(string $column, string $direction = 'ASC'): QueryBuilder
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => strtoupper($direction)
        ];
        return $this;
    }

    /**
     * Set LIMIT for the query.
     *
     * @param int $limit Maximum number of rows to return
     * @return QueryBuilder Current instance for method chaining
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Execute the query and return all matching rows.
     *
     * @return array<int, array<string, mixed>> Array of associative arrays representing rows
     */
    public function get(): array
    {
        $query = "SELECT ";

        if (empty($this->selects)) {
            $query .= "* ";
        } else {
            $query .= implode(', ', $this->selects) . " ";
        }

        $query .= "FROM {$this->table}";

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $joinType = $join['type'] === 'INNER' ? 'JOIN' : $join['type'] . ' JOIN';
                $query .= " {$joinType} {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        if (!empty($this->wheres)) {
            $query .= " WHERE ";

            foreach ($this->wheres as $index => $where) {
                if ($index > 0) {
                    $query .= " {$where['type']} ";
                }
                $query .= "{$where['column']} {$where['operator']} ?";
            }
        }

        if (!empty($this->orderBy)) {
            $query .= " ORDER BY ";
            $orderClauses = [];
            foreach ($this->orderBy as $order) {
                $orderClauses[] = "{$order['column']} {$order['direction']}";
            }
            $query .= implode(', ', $orderClauses);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT {$this->limit}";
        }

        $data = $this->conn->query($query, $this->bindings);
        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Execute the query and return the first matching row.
     *
     * @return array<string, mixed>|null Associative array representing the row, or null if not found
     */
    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Insert a new row into the table.
     *
     * @param array<string, mixed> $data Column-value pairs to insert
     * @return bool True if insert was successful, false otherwise
     */
    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->conn->query($query, array_values($data));
        return $stmt->rowCount() > 0;
    }

    /**
     * Update existing rows in the table.
     *
     * @param array<string, mixed> $data Column-value pairs to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(array $data): bool
    {
        $setParts = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if (!empty($this->wheres)) {
            $query .= " WHERE ";
            foreach ($this->wheres as $index => $where) {
                if ($index > 0) {
                    $query .= " {$where['type']} ";
                }
                $query .= "{$where['column']} {$where['operator']} ?";
            }
            $bindings = array_merge($bindings, $this->bindings);
        }

        $stmt = $this->conn->query($query, $bindings);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete rows from the table.
     *
     * @return bool True if delete was successful, false otherwise
     */
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $query .= " WHERE ";
            foreach ($this->wheres as $index => $where) {
                if ($index > 0) {
                    $query .= " {$where['type']} ";
                }
                $query .= "{$where['column']} {$where['operator']} ?";
            }
        }

        $stmt = $this->conn->query($query, $this->bindings);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get the SQL query string without executing it.
     *
     * @return string The generated SQL query string
     */
    public function toSql(): string
    {
        $query = "SELECT ";

        if (empty($this->selects)) {
            $query .= "* ";
        } else {
            $query .= implode(', ', $this->selects) . " ";
        }

        $query .= "FROM {$this->table}";

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $joinType = $join['type'] === 'INNER' ? 'JOIN' : $join['type'] . ' JOIN';
                $query .= " {$joinType} {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        if (!empty($this->wheres)) {
            $query .= " WHERE ";

            foreach ($this->wheres as $index => $where) {
                if ($index > 0) {
                    $query .= " {$where['type']} ";
                }
                $query .= "{$where['column']} {$where['operator']} ?";
            }
        }

        if (!empty($this->orderBy)) {
            $query .= " ORDER BY ";
            $orderClauses = [];
            foreach ($this->orderBy as $order) {
                $orderClauses[] = "{$order['column']} {$order['direction']}";
            }
            $query .= implode(', ', $orderClauses);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT {$this->limit}";
        }

        return $query;
    }
}
