<?php

namespace SampleIntegration\ControllerInitializer;

use SampleIntegration\Controller\IndexController;
use SampleIntegration\Middleware\SessionMiddleware;

class IndexControllerInitializer extends ControllerInitializer
{

    protected function setupControllers()
    {
        $app = $this->app;

        $app['index.controller'] = function () use ($app) {
            return new IndexController($app, $app['twig']);
        };
    }

    protected function setupRoutes()
    {
        $this->app->get('/', 'index.controller:index')
            ->bind('index')
            ->before(SessionMiddleware::build());
    }
}
