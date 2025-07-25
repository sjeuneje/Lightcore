<?php

namespace Core\Providers;

use Core\Container;

/**
 * Contract for service provider implementations.
 *
 * Defines the standard interface that all service providers must implement
 * to integrate with the application's service container and bootstrap process.
 *
 * @package Core\Providers
 * @since 1.0.0
 */
interface ServiceProviderInterface
{
    /**
     * Register services into the container.
     *
     * @param Container $container The service container
     * @return void
     */
    public function register(Container $container): void;

    /**
     * Bootstrap services after registration.
     *
     * @param Container $container The service container
     * @return mixed
     */
    public function boot(Container $container);
}
