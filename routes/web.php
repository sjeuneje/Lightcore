<?php

use Core\Http\Router;
use app\Controllers\UserController;

Router::get('users', [UserController::class, 'index']);
Router::post('users', [UserController::class, 'store']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::patch('users', [UserController::class, 'update']);
Router::delete('users', [UserController::class, 'delete']);