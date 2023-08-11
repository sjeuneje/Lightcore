<?php

namespace App\Controllers;

use Lightcore\Framework\Http\Response;

class WelcomeController
{
    public function index()
    {
        $content = dirname(__FILE__) . '../../../resources/views/welcome.php';

        return new Response($content);
    }
}