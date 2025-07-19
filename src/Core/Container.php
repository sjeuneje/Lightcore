<?php

namespace Core;

use Closure;
use Exception;

/**
 * A simple service container that manages bindings and resolves dependencies.
 * Implements the PSR-11 Container Interface.
 */
class Container
{
    protected array $bindings = [];

    /**
     * Bind a class or interface to a service (either a closure or an instance).
     *
     * @param string $id The service identifier (class name or interface name)
     * @param mixed $service The service or closure that resolves the service
     * @return Container Returns the container instance for method chaining
     */
    public function bind(string $id, mixed $service): Container
    {
        if (!($service instanceof Closure)) {
            $service = function() use ($service) {
                return $service;
            };
        }
        $this->bindings[$id] = $service;
        return $this;
    }

    /**
     * Bind a class or interface to a singleton service in the container.
     * A singleton service ensures that the same instance is returned each
     * time it is requested.
     *
     * @param string $id The service identifier (class name or interface name)
     * @param Closure $closure A closure that creates the instance of the service
     * @return Container Returns the container instance for method chaining
     */
    public function singleton(string $id, Closure $closure): Container
    {
        $this->bindings[$id] = function() use ($closure) {
            static $instance;

            return $instance ?: $instance = $closure();
        };
        return $this;
    }

    /**
     * Retrieve a service from the container by its identifier.
     * If the service has been bound as a closure, it will be executed
     * to instantiate the service; otherwise, the existing instance will be returned.
     *
     * @param string $id The service identifier (class name or interface name)
     * @return mixed The resolved service instance
     * @throws Exception If the service is not found in the container
     */
    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            $binding = $this->bindings[$id];
            // here we want to call the closure or return the instance.
            return is_callable($binding) ? $binding() : $binding;
        }

        throw new Exception("Container entry not found for: {$id}");
    }

    /**
     * Check if a service identifier exists in the container.
     *
     * @param string $id The service identifier (class name or interface name)
     * @return bool True if the service exists, false otherwise
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->bindings);
    }
}
