<?php

namespace Lightcore\Framework\View;

/**
 * Handle the display of a view and variables distribution.
 */
class View
{
    public string $path;
    public array $variables;

    public function set(string $view): View
    {
        $this->path = BASE_PATH . "/resources/views/{$view}.php";

        return $this;
    }

    public function attach(array $variables): View
    {
        $this->variables = $variables;

        return $this;
    }
}