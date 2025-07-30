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

// DB Tests
$dbConfig = [
    'DB_NAME' => $_ENV['DB_NAME'],
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_USER' => $_ENV['DB_USER'],
    'DB_PASS' => $_ENV['DB_PASS'],
];

$conn = new \Core\Database\Connection($dbConfig);
$conn->connect();
$stmt = $conn->query("SELECT * FROM users WHERE id = :id", ['id' => 1]);
$user = $stmt->fetch();
dd($user);

// Create kernel and handle request
$kernel = new Kernel();
$request = RequestFactory::createFromGlobals();
$response = $kernel->handle($request);
$response->send();