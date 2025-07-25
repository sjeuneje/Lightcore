<?php

use Core\Autoloader;
use Core\Kernel;
use Core\Http\Factory\RequestFactory;

// Load core dependencies
require_once "../src/helpers.php";
require_once "../src/Core/Autoloader.php";

// Register autoloader
Autoloader::register();

// Create kernel and handle request
$kernel = new Kernel();
$request = RequestFactory::createFromGlobals();
$response = $kernel->handle($request);

// Send response
$response->send();
