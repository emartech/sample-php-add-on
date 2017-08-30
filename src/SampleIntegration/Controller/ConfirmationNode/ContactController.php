<?php

namespace SampleIntegration\Controller\ConfirmationNode;

use SampleIntegration\AccessToken\TokenContainer;
use SampleIntegration\Controller\AccessTokenAuthenticatedController;
use SampleIntegration\DatabaseFactory;
use SampleIntegration\EmarsysClient;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AccessTokenAuthenticatedController
{

    public function listContacts()
    {
        $pdo = DatabaseFactory::getPDO();
        $statement = $pdo->prepare("SELECT * FROM triggered_contacts WHERE customer_id = :customer_id ORDER BY id");
        $statement->execute(['customer_id' => TokenContainer::getInstance()->getField('customerId')]);
        $contacts = $statement->fetchAll(\PDO::FETCH_CLASS, '\SampleIntegration\Model\Contact');
        return $this->renderTwig('ConfirmationNode/contact_list.html.twig', ['contacts' => $contacts]);
    }

    public function denyContact(Request $request)
    {
        $customerId = TokenContainer::getInstance()->getField('customerId');
        $suiteClient = new EmarsysClient($customerId);
        $suiteClient->callbackAutomationCenter($request->get('trigger_id'));

        $this->deleteTriggeredContact($customerId, $request->get('id'));

        return $this->app->redirect($this->app['url_generator']->generate('confirmation/contact_list', ['token' => TokenContainer::getInstance()->getAsHash()]));
    }

    public function allowContact(Request $request)
    {
        $customerId = TokenContainer::getInstance()->getField('customerId');
        $suiteClient = new EmarsysClient($customerId);
        $suiteClient->callbackAutomationCenter($request->get('trigger_id'), $request->get('contact_id'));

        $this->deleteTriggeredContact($customerId, $request->get('id'));

        return $this->app->redirect($this->app['url_generator']->generate('confirmation/contact_list', ['token' => TokenContainer::getInstance()->getAsHash()]));
    }


    private function deleteTriggeredContact($customerId, $triggeredContactId)
    {
        $pdo = DatabaseFactory::getPDO();
        $statement = $pdo->prepare("DELETE FROM triggered_contacts WHERE customer_id = :customer_id AND id = :id");
        $statement->execute([
            'customer_id' => $customerId,
            'id' => $triggeredContactId
        ]);
    }
}
