<?php

namespace App\Controllers;

use Lightcore\Framework\Database\DB;
use Lightcore\Framework\Http\Controller;
use Lightcore\Framework\Http\Response;
use Lightcore\Framework\Utils\GeneratePHPDoc;
use Lightcore\Framework\View\View;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        $tasks = DB::table('tasks')->get();

        return new Response($this->view
            ->set('welcome')
            ->attach([
                'tasks' => $tasks
            ]));
    }
}