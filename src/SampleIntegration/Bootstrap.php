<?php

namespace SampleIntegration;


use Dotenv\Dotenv;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Bootstrap
{

    public function start()
    {
        $this->loadDotEnv();

        $app = $this->createApp();

        $this->setTwigConfig($app);
        $this->setTrustedProxies();
        $this->setRoutes($app);

        $app->run();
    }


    public function loadDotEnv()
    {
        $dotEnvDir = dirname(dirname(__DIR__));
        if (file_exists($dotEnvDir . '/.env'))
        {
            $dotenv = new Dotenv($dotEnvDir);
            $dotenv->load();
        }
    }


    private function createApp()
    {
        $appFactory = new AppFactory();
        return $appFactory->create();
    }


    private function setTrustedProxies()
    {
        Request::setTrustedProxies(array('127.0.0.1'));
    }


    private function setTwigConfig(Application $app)
    {
        $app['twig.path'] = array(__DIR__.'/../../templates');
        if (getenv('TWIG_CACHE_ENABLED'))
        {
            $app['twig.options'] = array('cache' => __DIR__.'/../../var/cache/twig');
        }
    }


    private function setRoutes(Application $app)
    {
        $router = new Router();
        $router->setupRoutes($app);
        $router->setErrorHandlers($app);
    }

}
