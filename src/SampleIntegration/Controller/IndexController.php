<?php

namespace SampleIntegration\Controller;


class IndexController extends AccessTokenAuthenticatedController
{
    public function index()
    {
        return $this->renderTwig('index.html.twig');
    }

}