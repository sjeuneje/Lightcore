<?php

use Core\Http\Router;
use App\Controllers\UserController;

Router::get('/', function () {
   $html = "<h1>Welcome to Lightcore.</h1>";
   return \Core\Http\Response::html($html);
});

Router::get('users', [UserController::class, 'index']);
Router::post('users', [UserController::class, 'store']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::patch('users', [UserController::class, 'update']);
Router::delete('users', [UserController::class, 'delete']);