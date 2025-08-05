<?php

namespace Core\Database;

/**
 * Base Model class providing ORM functionality.
 *
 * This class serves as a blueprint for creating models that interact
 * with the database. It encapsulates common methods for data manipulation
 * such as creation, updating, deletion, and querying, along with
 * functionalities for attribute management and table relations.
 *
 * @package Core\Database
 */
abstract class Model
{
    /**
     * Target table name for the model instance.
     *
     * @var string
     */
    protected string $table = "";

    /**
     * Primary key column name for the model.
     *
     * @var string
     */
    protected string $primaryKey = "id";

    /**
     * Array of fillable attributes for mass assignment.
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * Array holding model attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Flag indicating whether the model instance exists in the database.
     *
     * @var bool
     */
    protected bool $exists = false;

    /**
     * Initialize the model with given attributes.
     *
     * @param array $attributes Array of initial attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!empty($attributes)) {
            $this->fill($attributes);
        }
    }

    /**
     * Fill the model's attributes with values from the given array.
     *
     * @param array $attributes Array of attributes to fill
     * @return self Current instance for method chaining
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Create a new instance of the model with given attributes.
     *
     * @param array $attributes Array of attributes for the new model
     * @return static New model instance
     */
    public static function make(array $attributes): static
    {
        return new static($attributes);
    }

    /**
     * Create and save a new instance of the model in the database.
     *
     * @param array $attributes Array of attributes for the new model
     * @return static New model instance with inserted data
     * @throws \RuntimeException If insertion fails
     */
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

    /**
     * Get the table name associated with the model.
     *
     * @return string Table name
     */
    protected function getTable(): string
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            return strtolower($className) . 's'; // Plurialized table name
        }
        return $this->table;
    }

    /**
     * Save the current model instance to the database.
     *
     * @return bool True if the save operation is successful, false otherwise
     */
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

    /**
     * Retrieve all records from the model's table.
     *
     * @return array Array of all model instances
     */
    public static function all(): array
    {
        $instance = new static();
        return DB::table($instance->getTable())->get();
    }

    /**
     * Find a model instance by its primary key.
     *
     * @param string $pk Primary key value
     * @return static|null Model instance if found, null otherwise
     */
    public static function find(string $pk): ?static
    {
        $instance = new static();
        $result = static::newQuery()
            ->where($instance->getPrimaryKey(), '=', $pk)
            ->first();

        return $result ? new static($result) : null;
    }

    /**
     * Create a new ModelQuery instance for the model's table.
     *
     * @return ModelQuery ModelQuery instance
     */
    protected static function newQuery(): ModelQuery
    {
        $instance = new static;
        $qb = new QueryBuilder(DB::getConnection(), $instance->getTable());
        return new ModelQuery($qb, static::class);
    }

    /**
     * Handle dynamic static method calls.
     *
     * @param string $method Method name
     * @param array $arguments Method arguments
     * @return mixed Result of the called method
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return static::newQuery()->$method(...$arguments);
    }

    /**
     * Get the name of the primary key column.
     *
     * @return string Primary key column name
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Update the current model instance's attributes in the database.
     *
     * @param array $attributes Array of attributes to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(array $attributes = []): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        return DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->update($attributes);
    }

    /**
     * Delete the current model instance from the database.
     *
     * @return bool True if delete was successful, false otherwise
     */
    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        return DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->delete();
    }

    /**
     * Reload the current model instance with fresh data from the database.
     *
     * @return static|null New model instance with fresh data, or null if not found
     */
    public function fresh(): ?static
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return null;
        }

        $freshData = DB::table($this->getTable())
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->first();

        if ($freshData) {
            return new static($freshData);
        }

        return null;
    }

    /**
     * Dynamically access model attributes.
     *
     * @param string $key Attribute name
     * @return mixed Attribute value
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Dynamically set model attributes.
     *
     * @param string $key Attribute name
     * @param string $value Attribute value
     */
    public function __set(string $key, string $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get an attribute by its key.
     *
     * @param string $key Attribute name
     * @return mixed Attribute value
     */
    public function getAttributes(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set an attribute by its key.
     *
     * @param string $key Attribute name
     * @param string $value Attribute value
     */
    public function setAttribute(string $key, string $value): void
    {
        $this->attributes[$key] = $value;
    }
}
