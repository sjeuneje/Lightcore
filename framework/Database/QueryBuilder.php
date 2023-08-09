<?php

namespace Lightcore\Framework\Database;

use Lightcore\Framework\Database\Abstracts\Builder;

class QueryBuilder extends Builder
{
    public function where(string $column, string $operator, $value): self
    {
        //TODO
        return $this;
    }

    public function get(): array
    {
        //TODO
        return [];
    }
}