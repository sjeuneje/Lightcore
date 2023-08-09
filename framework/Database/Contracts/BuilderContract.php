<?php

namespace Lightcore\Framework\Database\Contracts;

/**
 * Interface defining a contract for query builders.
 */
interface BuilderContract
{
    public function where(string $column, string $operator, mixed $value): self;
    public function get(): array;
}