<?php

namespace Lightcore\Framework\View;

use Lightcore\Framework\Contracts\View\ShouldRender;

class RenderingView implements ShouldRender
{
    private View $view;
    private const HEADER_PATH = BASE_PATH . '/bootstrap/views/header.php';
    private const FOOTER_PATH = BASE_PATH . '/bootstrap/views/footer.php';

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

        include self::HEADER_PATH;
        include  $this->view->path;
        include self::FOOTER_PATH;
    }
}