<?php

namespace SampleIntegration\Controller;

use SampleIntegration\AccessToken\TokenContainer;
use Silex\Application;
use Twig_Environment;

abstract class AccessTokenAuthenticatedController
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct(Application $app, Twig_Environment $twig)
    {
        $this->app = $app;
        $this->twig = $twig;
    }

    public function renderTwig($name, array $context = [])
    {
        $context['token'] = TokenContainer::getInstance()->getAsHash();
        return $this->twig->render($name, $context);
    }
}