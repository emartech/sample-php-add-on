<?php

namespace SampleIntegration\Middleware;

use SampleIntegration\AccessToken\TokenContainer;
use SampleIntegration\AccessToken\TokenException;
use SampleIntegration\SessionValidator;
use SampleIntegration\SessionValidatorClient;
use SampleIntegration\SessionValidatorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionMiddleware
{

    public static function build()
    {
        return function (Request $request, Application $app) {
            try
            {
                $token = TokenContainer::createFromTokenizedRequest($request);

                if (self::shouldValidateSession($token))
                {
                    $sessionValidator = new SessionValidator(new SessionValidatorClient());
                    $sessionValidator->validate($token->getFields()['msid']);

                    $token->setField('msid_validated_at', time());
                }

                return null;
            }
            catch (TokenException $ex)
            {
                $app->abort(Response::HTTP_UNAUTHORIZED, $ex->getMessage());
            }
            catch (SessionValidatorException $ex)
            {
                $app->abort(Response::HTTP_UNAUTHORIZED, $ex->getMessage());
            }
        };
    }

    private static function shouldValidateSession(TokenContainer $token)
    {
        return time() - $token->getField('msid_validated_at') > 120;
    }

}