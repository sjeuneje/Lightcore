<?php

namespace Core\Views;

abstract class BaseView
{
    /**
     * The base directory for view templates.
     *
     * @var string
     */
    protected string $viewsDirectory = "resources/views/";

    /**
     * The view template name/path
     *
     * @var string
     */
    protected string $template;

    /**
     * The data to pass to the view template
     *
     * @var array
     */
    protected array $data = [];

    /**
     * The file extension for view templates.
     *
     * @var string
     */
    protected string $fileExtension = ".php";

    /**
     * The compiled HTML output
     *
     * @var string
     */
    protected string $output = '';

    /**
     * Render the view template
     *
     * @return BaseView
     */
    abstract public function render(): BaseView;

    /**
     * Get the rendered HTML output
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->output;
    }
}
