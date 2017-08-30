<?php

namespace SampleIntegration\Controller;


use SampleIntegration\AccessToken\TokenContainer;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class LoginController
{

    private $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function index(Request $request)
    {
        $token = TokenContainer::createFromArray([
            'msid'        => $request->get('msid'),
            'customerId'  => $request->get('customer_id'),
            'adminId'     => $request->get('admin_id'),
            'sampleParam' => uniqid()
        ]);

        return $this->app->redirect($this->app['url_generator']->generate('index', ['token' => $token->getAsHash()]));
    }

}