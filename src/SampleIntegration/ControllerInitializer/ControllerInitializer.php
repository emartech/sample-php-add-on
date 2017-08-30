<?php

namespace SampleIntegration\ControllerInitializer;

abstract class ControllerInitializer
{
    /**
     * @var \Silex\Application
     */
    protected $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }


    public function build()
    {
        $this->setupControllers();
        $this->setupRoutes();
    }

    protected abstract function setupControllers();

    protected abstract function setupRoutes();
}