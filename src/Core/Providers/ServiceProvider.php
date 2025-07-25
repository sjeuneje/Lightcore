<?php

namespace Core\Providers;

use Core\Container;

/**
 * Abstract base class for all service providers.
 *
 * Service providers are the central place to configure and register
 * application services into the dependency injection container.
 * They follow a two-phase lifecycle: register() then boot().
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Register services into the container.
     *
     * This method is called during the registration phase.
     * Use it to bind services, interfaces, and dependencies
     * into the service container.
     *
     * @param Container $container The service container
     * @return void
     */
    abstract public function register(Container $container): void;

    /**
     * Bootstrap services after all providers are registered.
     *
     * This method is called after all service providers have been
     * registered. Override this method to perform bootstrapping
     * logic that requires other services to be available.
     *
     * @param Container $container The service container
     * @return void
     */
    public function boot(Container $container): void
    {
        // Default implementation does nothing
        // Override in concrete providers as needed
    }
}
