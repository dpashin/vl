<?php

namespace Kernel;


class Controller
{
    protected function render($viewName, $args = [])
    {
        $content = $this->includeTemplate("Main/${viewName}", $args);
        echo $this->includeTemplate("Layout/main", array_merge($args, ['content' => $content]));
    }

    protected function includeTemplate($template, $args = [])
    {
        extract($args);
        ob_start();
        require __DIR__ . "/../View/" . $template . ".php";
        return ob_get_clean();
    }
}