<?php

namespace Core\Database;

use Core\Database\QueryBuilder;
use Core\Support\Collection;

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
}
