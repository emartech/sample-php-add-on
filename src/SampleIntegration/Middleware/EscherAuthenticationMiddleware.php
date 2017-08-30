<?php

namespace SampleIntegration\Middleware;

use ArrayObject;
use SampleIntegration\EscherFactory;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EscherAuthenticationMiddleware
{
    public static function build()
    {
        return function (Request $request, Application $app) {
            try
            {
                $keyId = getenv('ESCHER_KEY_ID');
                $secret = getenv('ESCHER_SECRET');

                $keyDB = new ArrayObject(array(
                    $keyId => $secret
                ));

                $escher = EscherFactory::createForSuite();
                $escher->authenticate($keyDB);

                return null;
            }
            catch (\Escher\Exception $ex)
            {
                $app->abort(Response::HTTP_UNAUTHORIZED, $ex->getMessage());
            }
        };
    }
}