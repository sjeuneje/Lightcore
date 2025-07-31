<?php

use Core\Autoloader;
use Core\Kernel;
use Core\Http\Factory\RequestFactory;

define('BASE_PATH', dirname(__DIR__) . "/");

// Load core dependencies
require_once BASE_PATH . "/src/helpers.php";
//require_once BASE_PATH . "/src/Core/Autoloader.php";
require_once BASE_PATH . "/vendor/autoload.php";

$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Register autoloader
Autoloader::register();

// Create kernel and handle request
$kernel = new Kernel();
$request = RequestFactory::createFromGlobals();
$response = $kernel->handle($request);
$response->send();