<?php

namespace App\Controllers;

use Lightcore\Framework\Http\Response;

class PostController
{
    public function show(int $id): Response
    {
        $content = "This is post " . $id;

        return new Response($content);
    }
}