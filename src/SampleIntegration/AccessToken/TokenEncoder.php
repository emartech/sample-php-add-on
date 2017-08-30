<?php

namespace SampleIntegration\AccessToken;


use Firebase\JWT\JWT;

class TokenEncoder
{

    const ENV_TOKEN_SECRET = 'JWT_TOKEN_SECRET';


    public static function encode($payload)
    {
        return JWT::encode($payload, getenv(self::ENV_TOKEN_SECRET), 'HS256');
    }


    public static function decode($token)
    {
        try
        {
            return (array)JWT::decode($token, getenv(self::ENV_TOKEN_SECRET), array('HS256'));
        }
        catch (\UnexpectedValueException $e)
        {
            throw new TokenException('JWT: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

}