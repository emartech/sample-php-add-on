<?php

namespace SampleIntegration\Controller\EntryNode;

use SampleIntegration\AccessToken\TokenContainer;
use SampleIntegration\Controller\AccessTokenAuthenticatedController;
use SampleIntegration\EmarsysClient;

class ContactController extends AccessTokenAuthenticatedController
{

    public function listContacts()
    {
        $client = new EmarsysClient(TokenContainer::getInstance()->getField('customerId'));
        $contacts = $client->listContacts();
        return $this->renderTwig('EntryNode/contact_list.html.twig', ['contacts' => $contacts]);
    }

}
