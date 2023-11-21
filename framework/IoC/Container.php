<?php

namespace Lightcore\Framework\IoC;

use Exception;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * @throws Exception
     */
    public function resolve(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];

            if (is_callable($concrete)) {
                return $concrete($this);
            }

            return new $concrete();
        }

        throw new Exception("Binding not found for abstract class: {$abstract}");
    }
}