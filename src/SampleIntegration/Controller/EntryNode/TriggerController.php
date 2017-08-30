<?php

namespace SampleIntegration\Controller\EntryNode;

use SampleIntegration\AccessToken\TokenContainer;
use SampleIntegration\EmarsysClient;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TriggerController
{

    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function index(Request $request)
    {
        $suiteClient = new EmarsysClient(TokenContainer::getInstance()->getField('customerId'));
        $suiteClient->startAutomationCenterPrograms($request->get('resource_id'), $request->get('contact_id'));

        return $this->app->redirect($this->app['url_generator']->generate('entry/contact_list', ['token' => TokenContainer::getInstance()->getAsHash()]));
    }
}