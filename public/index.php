<?php

use Core\Autoloader;
use Core\Kernel;
use Core\Http\Factory\RequestFactory;

define('BASE_PATH', dirname(__DIR__) . "/");

// Load core dependencies
require_once "../src/helpers.php";
require_once "../src/Core/Autoloader.php";

// Register autoloader
Autoloader::register();

// Create kernel and handle request
$kernel = new Kernel();
$request = RequestFactory::createFromGlobals();
$response = $kernel->handle($request);
$response->send();