<?php

namespace Lightcore\Framework\Http;

use Lightcore\Framework\View\RenderingView;
use Lightcore\Framework\View\View;

class Response
{
    public function __construct(
        private View $view,
        private int $status = 200,
        private array $headers = []
    )
    {}

    public function send(): void
    {
        (new RenderingView($this->view))->render();
    }
}