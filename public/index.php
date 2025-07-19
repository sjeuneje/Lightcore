<?php

use Core\Autoloader;
use Core\Container;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;

require_once "../src/helpers.php";
require_once "../src/Core/Autoloader.php";

/**
 * Register the Autoloader of the framework.
 */
Autoloader::register();

/**
 * Initialize the service container.
 */
$container = new Container();

/**
 * Bind Request service to the container.
 */
$container->singleton(Request::class, function() {
    return RequestFactory::createFromGlobals();
});

/**
 * Retrieve the Request.
 */
$request = $container->get(Request::class);
