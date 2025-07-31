<?php

namespace Core;

use Core\Exceptions\HttpException;
use Core\Providers\CoreServiceProvider;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Dispatcher;
use Core\Providers\DatabaseServiceProvider;

/**
 * Application kernel responsible for handling HTTP requests
 *
 * Manages the complete request-response lifecycle including service providers
 * registration, request processing, and response generation.
 */
class Kernel
{
    /**
     * The application's service container
     *
     * @var Container
     */
    private Container $container;

    /**
     * List of service provider class names to register
     *
     * @var array<string>
     */
    private array $providers = [];

    /**
     * Create a new kernel instance
     *
     * Initializes the container and registers all service providers.
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->registerProviders();
        $this->boot();
    }

    /**
     * Register all service providers
     *
     * @return void
     */
    private function registerProviders(): void
    {
        $this->providers = [
            CoreServiceProvider::class,
            DatabaseServiceProvider::class
        ];
    }

    /**
     * Boot the application
     *
     * @return void
     */
    private function boot(): void
    {
        // Register all services
        foreach ($this->providers as $providerClass) {
            $provider = new $providerClass();
            $provider->register($this->container);
        }

        // Boot all providers
        foreach ($this->providers as $providerClass) {
            $provider = new $providerClass();
            $provider->boot($this->container);
        }
    }

    /**
     * Handle an incoming HTTP request
     *
     * Processes the request through the application pipeline and returns
     * the corresponding HTTP response.
     *
     * @param Request|null $request The incoming HTTP request
     * @return Response The HTTP response
     * @throws HttpException
     */
    public function handle(Request $request = null): Response
    {
        $request = $request ?? $this->container->get(Request::class);

        // Load routes
        require_once __DIR__ . '/../../routes/web.php';

        // Dispatch request and get response
        return Dispatcher::dispatch($request);
    }

    /**
     * Get the service container instance
     *
     * @return Container The application's service container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
