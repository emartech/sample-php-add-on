<?php

namespace SampleIntegration\ControllerInitializer;

use SampleIntegration\Controller\LoginController;
use SampleIntegration\Middleware\EscherAuthenticationMiddleware;

class LoginControllerInitializer extends ControllerInitializer
{

    protected function setupControllers()
    {
        $app = $this->app;

        $app['login.controller'] = function () use ($app) {
            return new LoginController($app);
        };
    }

    protected function setupRoutes()
    {
        $this->app->get('/login', 'login.controller:index')
            ->before(EscherAuthenticationMiddleware::build());
    }
}
