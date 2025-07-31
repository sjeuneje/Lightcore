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
    /**
     * The table associated with the model
     */
    protected string $table = "";

    /**
     * The primary key for the model
     */
    protected string $primaryKey = "id";

    /**
     * The attributes that are mass assignable
     */
    protected array $fillable = [];

    /**
     * The model's attributes
     */
    protected array $attributes = [];

    /**
     * Indicates if the model exists in database
     */
    protected bool $exists = false;

    /**
     * Create a new model instance
     *
     * @param array $attributes Initial attributes to fill
     */
    public function __construct(array $attributes = [])
    {
        if (!empty($attributes)) {
            $this->fill($attributes);
        }
    }

    /**
     * Fill the model with an array of attributes
     *
     * @param array $attributes Attributes to fill
     * @return self
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
     * Create a new model instance without saving
     *
     * @param array $attributes
     * @return static
     */
    public static function make(array $attributes): static
    {
        return new static($attributes);
    }

    /**
     * Create a new instance in the database.
     *
     * @param array $attributes
     * @return static
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
     * Get the table name for the model
     *
     * @return string
     */
    protected function getTable(): string
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            return strtolower($className) . 's';
        }
        return $this->table;
    }

    /**
     * Save the model to the database
     *
     * @return bool
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
}
