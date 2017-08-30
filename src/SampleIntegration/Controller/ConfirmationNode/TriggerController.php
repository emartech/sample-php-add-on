<?php

namespace SampleIntegration\Controller\ConfirmationNode;

use SampleIntegration\DatabaseFactory;
use SampleIntegration\Model\Contact;
use Symfony\Component\HttpFoundation\Request;

class TriggerController
{

    public function index(Request $request)
    {
        $contact = $this->buildContactFrom($request);
        if ($contact->contact_id)
        {
            $contact->save(DatabaseFactory::getPDO());
        }
        $response = ['timeout' => $this->getTimeoutForTrigger()];
        return json_encode($response);
    }



    private function buildContactFrom(Request $request)
    {
        $fields = json_decode($request->getContent(), true);

        $contact = new Contact();
        $contact->customer_id = $fields['customer_id'];
        $contact->contact_id  = $fields['user_id'];
        $contact->resource_id = $fields['resource_id'];
        $contact->trigger_id  = $fields['trigger_id'];
        $contact->program_id  = $fields['program_id'];
        $contact->node_id     = $fields['node_id'];

        return $contact;
    }


    private function getTimeoutForTrigger()
    {
        $UTC = new \DateTimeZone("UTC");
        $date = new \DateTime( 'now', $UTC );
        $date->modify('+1 hour');
        return $date->format('Y-m-d\TH:i:s\Z');
    }
}