<?php

use Lightcore\Framework\Database\DB;

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$container = new \Lightcore\Framework\IoC\Container();

$container->bind(PDO::class, function ($container) {
    $config = [
        'host' => $_ENV['DB_HOST'],
        'dbname' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
    ];

    $dsn = 'mysql:' . 'host=' . $config['host'] . ';' . 'dbname=' . $config['dbname'];

    return new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
});

DB::setContainer($container);