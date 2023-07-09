<?php

use App\Controllers\HomeController;
use App\Controllers\PostController;

return [
    ['GET', '/Lightcore/public/', [HomeController::class, 'index']],
    ['GET', '/Lightcore/public/posts/{id:\d+}', [PostController::class, 'show']]
];