<?php

namespace SampleIntegration;


use GuzzleHttp\Client;

class SessionValidatorClient
{

    private $baseUrl;
    private $escherKeyId;
    private $escherSecret;
    private $escher;
    private $restClient;


    public function __construct()
    {
        $this->baseUrl = getenv('SESSION_VALIDATOR_URL');

        $this->escherKeyId = getenv('SESSION_VALIDATOR_KEY_ID');
        $this->escherSecret = getenv('SESSION_VALIDATOR_SECRET');

        $this->escher = EscherFactory::createForSessionValidator();

        $this->restClient = new Client(['base_uri' => $this->baseUrl]);
    }


    public function getStatusCode($msid)
    {
        $uri = "/sessions/{$msid}";

        $headers = $this->escher->signRequest($this->escherKeyId, $this->escherSecret, 'GET', $this->baseUrl . $uri, '');
        $response = $this->restClient->request('GET', $uri, ['headers' => $headers, 'timeout' => 0.150]);

        return $response->getStatusCode();
    }

}