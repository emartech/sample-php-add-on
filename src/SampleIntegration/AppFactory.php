<?php

namespace SampleIntegration;


use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

class AppFactory
{

    public function create()
    {
        $app = new Application();
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new AssetServiceProvider());
        $app->register(new TwigServiceProvider());
        $app->register(new HttpFragmentServiceProvider());

        return $app;
    }

}
