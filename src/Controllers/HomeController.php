<?php

namespace App\Controllers;

use Lightcore\Framework\Http\Response;

class HomeController
{
    public function index(): Response
    {
        $content = '<h1>Hello World</h1>';

        return new Response($content);
    }
}