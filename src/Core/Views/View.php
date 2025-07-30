<?php

namespace Core\Views;

class View extends BaseView
{
    public function __construct(string $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function render(): BaseView
    {
        $viewPath = BASE_PATH . "{$this->viewsDirectory}{$this->template}{$this->fileExtension}";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View file not found: {$viewPath}");
        }

        ob_start();
        extract($this->data);
        require $viewPath;
        $this->output = ob_get_clean();

        return $this;
    }
}
