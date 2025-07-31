<?php

namespace Core\Database;

use Core\Database\QueryBuilder;
use Core\Database\DB;

/**
 * Base Model class providing ORM functionality
 *
 * @package Core\Database
 */
abstract class Model
{
    protected string $table = "";
    protected string $primaryKey = "id";
    protected array $fillable = [];
    protected array $attributes = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        if (!empty($attributes)) {
            $this->fill($attributes);
        }
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public static function make(array $attributes): static
    {
        return new static($attributes);
    }

    public static function create(array $attributes): static
    {
        $instance = new static();
        $instance->fill($attributes);

        $insertedId = DB::table($instance->getTable())->insert($instance->attributes);
        if (!$insertedId) {
            throw new \RuntimeException("Error while adding a new row in the {$instance->getTable()} table.");
        }

        $instance->attributes[$instance->primaryKey] = $insertedId;
        $instance->exists = true;
        return $instance;
    }

    protected function getTable(): string
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            return strtolower($className) . 's';
        }
        return $this->table;
    }

    public function save(): bool
    {
        if ($this->exists) {
            $id = $this->attributes[$this->primaryKey];
            $result = DB::table($this->getTable())
                ->where($this->primaryKey, '=', $id)
                ->update($this->attributes);
            return $result > 0;
        } else {
            $insertedId = DB::table($this->getTable())->insert($this->attributes);
            if ($insertedId) {
                $this->attributes[$this->primaryKey] = $insertedId;
                $this->exists = true;
                return true;
            }
            return false;
        }
    }

    public static function all(): array
    {
        $instance = new static();
        return DB::table($instance->getTable())->get();
    }

    public static function find(string $pk): ?static
    {
        $instance = new static();
        $result = static::newQuery()
            ->where($instance->getPrimaryKey(), '=', $pk)
            ->first();

        return $result ? new static($result) : null;
    }

    protected static function newQuery(): QueryBuilder
    {
        $instance = new static;
        return new QueryBuilder(DB::getConnection(), $instance->getTable());
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return static::newQuery()->$method(...$arguments);
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function update(array $attributes = []): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        return DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->update($attributes);
    }

    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        return DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->delete();
    }

    public function fresh(): ?static
    {
        if (empty($this->attributes[$this->primaryKey]))
            return null;

        $freshData = DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->first();

        if ($freshData) {
            return new static($freshData);
        }

        return null;
    }

    public function __get(string $key)
    {
        return $this->attributes[$key];
    }

    public function __set(string $key, string $value)
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(string $key): mixed
    {
        return $this->attributes[$key];
    }

    public function setAttribute(string $key, string $value): void
    {
        $this->attributes[$key] = $value;
    }
}