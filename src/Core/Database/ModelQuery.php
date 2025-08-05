<?php

namespace Core\Database;

class ModelQuery
{
    private QueryBuilder $qb;
    private string $modelClass;
    private array $with = [];

    public function __construct(QueryBuilder $qb, string $modelClass)
    {
        $this->qb = $qb;
        $this->modelClass = $modelClass;
    }

    public function __call(string $method, array $args): self
    {
        $this->qb->$method(...$args);
        return $this;
    }
}