<?php

namespace Lightcore\Framework\Contracts\Database;

interface HasQueries
{
    public function table(string $table): static;

    public function select(mixed $fields): static;

    public function where(string $column, string $operator, mixed $value): static;

    public function get(): bool|array;
}