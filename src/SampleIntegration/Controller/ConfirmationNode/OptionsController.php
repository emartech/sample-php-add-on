<?php

namespace SampleIntegration\Controller\ConfirmationNode;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class OptionsController
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

    public function index(Request $request)
    {
        return $this->twig->render(
            'ConfirmationNode/options.html.twig',
            [
                'confirmationTag' => $this->getConfirmationTag($request)
            ]
        );
    }

    private function getConfirmationTag(Request $request)
    {
        $confirmationTag = $request->get('resource_id') == '0' ? '' : $request->get('resource_id');
        return $confirmationTag;
    }
}