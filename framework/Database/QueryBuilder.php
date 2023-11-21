<?php

namespace Lightcore\Framework\Database;

use PDO;

class QueryBuilder
{
    protected PDO $pdo;
    protected $table;
    protected $fields = '*';
    protected $wheres = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table): static
    {
        $this->table = $table;

        return $this;
    }

    public function select($fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function where($column, $operator, $value): static
    {
        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    public function get(): bool|array
    {
        $query = 'SELECT ' . $this->fields
            . ' FROM ' . $this->table;

        if (!empty($this->wheres)) {
            $query .= ' WHERE ';

            foreach ($this->wheres as $index => $where) {
                if ($index > 0) {
                    $query .= $where['type'] . ' ';
                }
                $query .= $where['column'] . ' '
                    . $where['operator']
                    . ' ?';
            }
        }

        $stmt = $this->pdo->prepare($query);
        $bindedValues = array_column($this->wheres, 'value');
        $stmt->execute($bindedValues);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}