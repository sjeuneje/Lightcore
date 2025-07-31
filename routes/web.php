<?php

use Core\Http\Response;
use Core\Http\Router;
use App\Controllers\UserController;
use Core\Views\View;

Router::get('/', function () {
    class User extends \Core\Database\Model {}

    $user = User::find(2);
    dd($user);

    return Response::view(
        new View('welcome', [
            'frameworkName' => 'Lightcore'
        ])
    );
});

Router::get('users', [UserController::class, 'index']);
Router::post('users', [UserController::class, 'store']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::patch('users', [UserController::class, 'update']);
Router::delete('users', [UserController::class, 'delete']);