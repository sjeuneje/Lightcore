<?php

use App\Controllers\WelcomeController;

return [
    ['GET', '/Lightcore/public/', [WelcomeController::class, 'index']],
];