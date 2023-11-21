<?php

namespace App\Controllers;

use Lightcore\Framework\Database\DB;
use Lightcore\Framework\Http\Response;

class WelcomeController
{
    public function index(): Response
    {
        $content = BASE_PATH . '/resources/views/welcome.php';

        $tasks = DB::table('tasks')->get();

        return new Response($content);
    }
}