<?php

namespace SampleIntegration;


use SampleIntegration\ControllerInitializer;
use SampleIntegration\AccessToken\TokenContainer;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    public function setupRoutes(Application $app)
    {
        $app->before(function (Request $request) use ($app) {
            $app['twig']->addGlobal('current_page_path', $request->get("_route"));
        });

        $initializers = [
            new ControllerInitializer\LoginControllerInitializer($app),
            new ControllerInitializer\IndexControllerInitializer($app),
            new ControllerInitializer\ProfileControllerInitializer($app),
            new ControllerInitializer\EntryNodeInitializer($app),
            new ControllerInitializer\ConfirmationNodeInitializer($app)
        ];

        foreach ($initializers as $initializer)
        {
            $initializer->build();
        }
    }


    public function setErrorHandlers(Application $app)
    {
        $app->error(function (\Exception $e, Request $request, $code) use ($app) {
            if ($app['debug']) {
                return null;
            }

            // 404.html, or 40x.html, or 4xx.html, or error.html
            $templates = array(
                'errors/' . $code . '.html.twig',
                'errors/' . substr($code, 0, 2) . 'x.html.twig',
                'errors/' . substr($code, 0, 1) . 'xx.html.twig',
                'errors/default.html.twig',
            );

            if ($request->getContentType() == 'json') {
                return new JsonResponse(['errorMessage' => $e->getMessage()], $code);
            } else {
                $tokenContainer = TokenContainer::getInstance();
                return new Response($app['twig']->resolveTemplate($templates)->render([
                    'code' => $code,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'token' => is_null($tokenContainer) ? '' : $tokenContainer->getAsHash()
                ]), $code);
            }
        });
    }

}
