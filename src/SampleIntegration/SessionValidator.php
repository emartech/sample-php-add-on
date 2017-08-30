<?php

namespace SampleIntegration;

use GuzzleHttp\Exception;

class SessionValidator
{
    /**
     * @var SessionValidatorClient
     */
    private $client;

    /**
     * @param $client SessionValidatorClient
     */
    public function __construct(SessionValidatorClient $client)
    {
        $this->client = $client;
    }

    public function validate($msid)
    {
        try
        {
            $this->client->getStatusCode($msid);
        }
        catch (Exception\ConnectException $e) {}
        catch (Exception\ServerException $e) {}
        catch (\Exception $e)
        {
            throw new SessionValidatorException('Session validation error');
        }
    }
}