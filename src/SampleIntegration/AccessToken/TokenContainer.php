<?php

namespace SampleIntegration\AccessToken;


use Symfony\Component\HttpFoundation\Request;


class TokenContainer
{

    /** @var TokenContainer */
    private static $instance;


    /** @var array */
    private $tokenFields;


    public static function createFromTokenizedRequest(Request $request)
    {
        $payload = TokenEncoder::decode(self::findToken($request));
        return self::createFromArray($payload);
    }


    public static function createFromArray(array $fields)
    {
        self::$instance = new self($fields);
        return self::$instance;
    }


    public static function getInstance()
    {
        return self::$instance;
    }


    private static function findToken(Request $request)
    {
        if ($request->getContentType() == 'json')
        {
            $postBody = json_decode($request->getContent(), true);
            return $postBody['token'];
        }
        else
        {
            return $request->get('token');
        }
    }


    private function __construct(array $tokenFields)
    {
        $this->tokenFields = $tokenFields;
    }


    public function getAsHash()
    {
        return TokenEncoder::encode($this->tokenFields);
    }


    public function getFields()
    {
        return $this->tokenFields;
    }

    public function setField($fieldName, $fieldValue)
    {
        $this->tokenFields[$fieldName] = $fieldValue;
    }


    public function getField($fieldName)
    {
        return array_key_exists($fieldName, $this->tokenFields) ? $this->tokenFields[$fieldName] : null;
    }

}