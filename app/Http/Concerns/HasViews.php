<?php

namespace App\Http\Concerns;

use App\Exceptions\NotFoundViewException;
use Symfony\Component\HttpFoundation\Response;

trait HasViews
{
    public string $view_path;
    public string $component_path;

    public function bootHasViews()
    {
        $this->view_path = dirname(__FILE__, 4) . '/views';
        $this->component_path = "$this->view_path/components";
    }

    protected function view($view, ...$params): Response
    {
        $this->bootHasViews();
        $view_file = $this->view_path . '/' . str_replace('.', '/', $view) . '.php';

        if (file_exists($view_file)) {
            ob_start();
            extract($params);
            include($view_file);
            $this->includeVueComponents();
        } else {
            throw new NotFoundViewException();
        }

        return (new Response())->setContent(ob_get_clean());
    }

    protected function includeVueComponents()
    {
        foreach (scandir($this->component_path) ?: [] as $component) {
            if (str_ends_with($component, '.html')) {
                include("$this->component_path/$component");
            }
        }
    }
}