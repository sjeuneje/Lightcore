<?php

namespace Lightcore\Framework\View;

use Lightcore\Framework\Contracts\View\ShouldRender;

class RenderingView implements ShouldRender
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function render(): void
    {
        if (!empty($this->view->variables)) {
            foreach ($this->view->variables as $variableName => $variableValue) {
                $$variableName = $variableValue;
            }
        }

        include  $this->view->path;
    }
}