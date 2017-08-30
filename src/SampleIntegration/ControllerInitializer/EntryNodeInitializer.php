<?php

namespace SampleIntegration\ControllerInitializer;


use SampleIntegration\Controller\EntryNode;
use SampleIntegration\Middleware\EscherAuthenticationMiddleware;
use SampleIntegration\Middleware\SessionMiddleware;

class EntryNodeInitializer extends ControllerInitializer
{

    protected function setupControllers()
    {
        $app = $this->app;

        $this->app['entry_node.options.controller'] = function () use ($app) {
            return new EntryNode\OptionsController();
        };

        $this->app['entry_node.contact.controller'] = function () use ($app) {
            return new EntryNode\ContactController($app, $app['twig']);
        };

        $this->app['entry_node.trigger.controller'] = function () use ($app) {
            return new EntryNode\TriggerController($app);
        };
    }

    protected function setupRoutes()
    {
        $this->app->get('/entry_node/options', 'entry_node.options.controller:index')
            ->before(EscherAuthenticationMiddleware::build());

        $this->app->get('/entry_node/contact_list', 'entry_node.contact.controller:listContacts')
            ->before(SessionMiddleware::build())
            ->bind('entry/contact_list');

        $this->app->get('/entry_node/trigger/{resource_id}', 'entry_node.trigger.controller:index')
            ->before(SessionMiddleware::build())
            ->bind('entry/trigger');
    }
}
