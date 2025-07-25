<?php

namespace Core\Providers;

use Core\Container;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;

class CoreServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $container->singleton(Request::class, function() {
            return RequestFactory::createFromGlobals();
        });

        $container->singleton(Response::class, function() {
            return new Response();
        });

        $container->singleton(Router::class, function () {
            return new Router();
        });
    }

    public function boot(Container $container): void
    {
        //
    }
}
