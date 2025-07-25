<?php

namespace Core\Providers;

use Core\Container;

abstract class ServiceProvider implements ServiceProviderInterface
{
    abstract public function register(Container $container): void;

    public function boot(Container $container): void
    {
        //
    }
}
