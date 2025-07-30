<?php

namespace Core\Providers;

use Core\Container;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;

/**
 * Core HTTP services provider.
 *
 * Registers essential HTTP components required for request/response handling
 * and routing functionality. This provider is automatically loaded by the
 * kernel and provides the foundation for web request processing.
 *
 * Registered services:
 * - HTTP Request (singleton)
 * - HTTP Response (singleton)
 * - Router (singleton)
 *
 * @package Core\Providers
 * @since 1.0.0
 *
 * @see Request For HTTP request handling
 * @see Response For HTTP response generation
 * @see Router For application routing
 */
class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register core HTTP services into the container.
     *
     * Binds the essential HTTP components as singletons:
     * - Request: Created from PHP globals ($_GET, $_POST, etc.)
     * - Response: Empty response instance ready for content
     * - Router: Application router for handling routes
     *
     * @param Container $container The service container
     * @return void
     *
     * @throws \Exception If service binding fails
     */
    public function register(Container $container): void
    {
        // Register empty HTTP Response as singleton
        $container->singleton(Response::class, function() {
            return new Response();
        });

        // Register application Router as singleton
        $container->singleton(Router::class, function () {
            return new Router();
        });
    }

    /**
     * Bootstrap core HTTP services.
     *
     * Currently, no bootstrapping logic is required for core services.
     * This method is available for future enhancements such as:
     * - Route middleware registration
     * - Global response headers
     * - Request validation rules
     *
     * @param Container $container The service container
     * @return void
     */
    public function boot(Container $container): void
    {
        //
    }
}
