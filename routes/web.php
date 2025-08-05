<?php

use Core\Http\Response;
use Core\Http\Router;
use App\Controllers\UserController;
use Core\Views\View;

Router::get('/', function () {
    return Response::view(
        new View('welcome', [
            'frameworkName' => \Core\Config::get('app.name')
        ])
    );
});

Router::get('users', [UserController::class, 'index']);
Router::post('users', [UserController::class, 'store']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::patch('users', [UserController::class, 'update']);
Router::delete('users', [UserController::class, 'delete']);