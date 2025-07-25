<?php

namespace Core\Providers;

use Core\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;

    public function boot(Container $container);
}