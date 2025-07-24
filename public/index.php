<?php

use Core\Autoloader;
use Core\Container;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;
use Core\Http\Response;

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

$container->singleton(Response::class, function() {
    return new Response();
});

/**
 * Retrieve the Request.
 */
$request = $container->get(Request::class);

/**
 * Retrieve the Response
 */
Response::html('<h1>Welcome to My Framework!</h1>')
    ->send();