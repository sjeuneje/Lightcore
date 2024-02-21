<?php

namespace Lightcore\Framework\Http;

use Lightcore\Framework\View\View;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }
}