<?php

use App\Controllers\WelcomeController;

return [
    ['GET', '/', [WelcomeController::class, 'index']],
];