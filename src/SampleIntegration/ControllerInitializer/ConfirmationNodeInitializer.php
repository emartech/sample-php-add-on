<?php

namespace SampleIntegration\ControllerInitializer;

use SampleIntegration\Controller\ConfirmationNode;
use SampleIntegration\Middleware\EscherAuthenticationMiddleware;
use SampleIntegration\Middleware\SessionMiddleware;

class ConfirmationNodeInitializer extends ControllerInitializer
{

    protected function setupControllers()
    {
        $app = $this->app;

        $app['confirmation_node.options.controller'] = function () use ($app) {
            return new ConfirmationNode\OptionsController($app, $app['twig']);
        };

        $app['confirmation_node.trigger.controller'] = function () use ($app) {
            return new ConfirmationNode\TriggerController();
        };

        $app['confirmation_node.contact.controller'] = function () use ($app) {
            return new ConfirmationNode\ContactController($app, $app['twig']);
        };
    }

    protected function setupRoutes()
    {

        $this->app->get('/confirmation_node/options', 'confirmation_node.options.controller:index')
            ->before(EscherAuthenticationMiddleware::build());


        $this->app->post('/confirmation_node/trigger', 'confirmation_node.trigger.controller:index')
            ->before(EscherAuthenticationMiddleware::build());


        $this->app->get('/confirmation_node/contact_list', 'confirmation_node.contact.controller:listContacts')
            ->before(SessionMiddleware::build())
            ->bind('confirmation/contact_list');

        $this->app->get('/confirmation_node/deny_contact', 'confirmation_node.contact.controller:denyContact')
            ->before(SessionMiddleware::build())
            ->bind('confirmation/deny_contact');

        $this->app->get('/confirmation_node/allow_contact', 'confirmation_node.contact.controller:allowContact')
            ->before(SessionMiddleware::build())
            ->bind('confirmation/allow_contact');
    }
}
