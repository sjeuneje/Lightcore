<?php

use App\Controllers\UserController;
use Core\Autoloader;
use Core\Container;
use Core\Http\Dispatcher;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;

require_once "../src/helpers.php";
require_once "../src/Core/Autoloader.php";
require_once "../app/Controllers/UserController.php";

Autoloader::register();

$container = new Container();

$container->singleton(Request::class, function() {
    return RequestFactory::createFromGlobals();
});

$container->singleton(Response::class, function() {
    return new Response();
});

$container->singleton(Router::class, function () {
    return new Router();
});

$request = $container->get(Request::class);

require_once '../routes/web.php';

Dispatcher::dispatch($request);