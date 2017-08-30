<?php

namespace SampleIntegration;


use GuzzleHttp\Client;

class EmarsysClient
{

    private $customerId;
    private $baseUrl;
    private $escherKeyId;
    private $escherSecret;
    private $escher;
    private $restClient;


    public function __construct($customerId)
    {
        $this->customerId = $customerId;
        $this->baseUrl = getenv('SUITE_URL');
        $this->escherKeyId = getenv('ESCHER_KEY_ID');
        $this->escherSecret = getenv('ESCHER_SECRET');
        $this->escher = EscherFactory::createForSuite();
        $this->restClient = new Client(['base_uri' => $this->baseUrl]);
    }


    public function getSettings()
    {
        $uri = "/api/v2/internal/{$this->customerId}/settings";

        $headers = $this->escher->signRequest($this->escherKeyId, $this->escherSecret, 'GET', $this->baseUrl . $uri, '');
        $response = $this->restClient->request('GET', $uri, ['headers' => $headers]);

        return json_decode($response->getBody(), true);
    }


    public function callbackAutomationCenter($triggerId, $contactId = 0, $contactListId = 0)
    {
        $uri = "/api/v2/internal/{$this->customerId}/ac/programs/callbacks/{$triggerId}";
        $postParams = [
            'user_id' => $contactId,
            'list_id' => $contactListId
        ];

        $headers = $this->escher->signRequest($this->escherKeyId, $this->escherSecret, 'POST', $this->baseUrl . $uri, json_encode($postParams));
        $this->restClient->request('POST', $uri, [
            'headers'     => $headers,
            'body' => json_encode($postParams)
        ]);
    }


    public function listContacts()
    {
        $uri = "/api/v2/internal/{$this->customerId}/contact/query/?return=3&limit=100";

        $headers = $this->escher->signRequest($this->escherKeyId, $this->escherSecret, 'GET', $this->baseUrl . $uri, '');
        $response = $this->restClient->request('GET', $uri, ['headers' => $headers]);

        $parsedResponse = json_decode($response->getBody(), true);
        return array_map(function ($contact) {
            return ['id' => $contact['id'], 'email' => $contact['3']];
        }, $parsedResponse['data']['result']);
    }


    public function startAutomationCenterPrograms($resourceId, $contactId)
    {
        $uri = "/api/v2/internal/{$this->customerId}/ac/programs/entrypoints/sample-entry/resources/{$resourceId}/runs";

        $postParams = [
            'contact_id' => $contactId
        ];

        $headers = $this->escher->signRequest($this->escherKeyId, $this->escherSecret, 'POST', $this->baseUrl . $uri, json_encode($postParams));
        $this->restClient->request('POST', $uri, [
            'headers'     => $headers,
            'body' => json_encode($postParams)
        ]);
    }

}