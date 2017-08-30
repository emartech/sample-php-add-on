<?php

namespace SampleIntegration\Controller;

use SampleIntegration\AccessToken\TokenContainer;
use SampleIntegration\EmarsysClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends AccessTokenAuthenticatedController
{
    public function index()
    {
        return $this->renderTwig('profile.html.twig');
    }

    public function getData()
    {
        $customerId = TokenContainer::getInstance()->getField('customerId');

        $suiteClient = new EmarsysClient($customerId);
        return new JsonResponse($suiteClient->getSettings());
    }
}