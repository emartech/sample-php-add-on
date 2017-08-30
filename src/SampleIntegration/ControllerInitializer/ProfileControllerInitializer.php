<?php

namespace SampleIntegration\ControllerInitializer;

use SampleIntegration\Controller\ProfileController;
use SampleIntegration\Middleware\SessionMiddleware;

class ProfileControllerInitializer extends ControllerInitializer
{

    protected function setupControllers()
    {
        $app = $this->app;

        $app['profile.controller'] = function () use ($app) {
            return new ProfileController($app, $app['twig']);
        };
    }

    protected function setupRoutes()
    {
        $this->app->get('/profile', 'profile.controller:index')
            ->bind('profile')
            ->before(SessionMiddleware::build());

        $this->app->post('/profile/getData', 'profile.controller:getData')
            ->before(SessionMiddleware::build());
    }
}
