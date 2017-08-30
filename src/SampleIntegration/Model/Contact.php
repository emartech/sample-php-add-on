<?php

namespace SampleIntegration\Model;

use PDO;

class Contact
{
    public $id;
    public $contact_id;
    public $customer_id;
    public $resource_id;
    public $trigger_id;
    public $program_id;
    public $node_id;
    public $time;

    public function save(PDO $pdo)
    {
        $sql = "
        INSERT INTO triggered_contacts 
        (
            contact_id, 
            customer_id, 
            resource_id, 
            trigger_id, 
            program_id, 
            node_id,
            time
        )
        VALUES (
            :contact_id,
            :customer_id,
            :resource_id,
            :trigger_id,
            :program_id,
            :node_id,
            CURRENT_TIMESTAMP
        )
        ";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'contact_id'  => (int) $this->contact_id,
            'customer_id' => (int) $this->customer_id,
            'resource_id' => $this->resource_id,
            'trigger_id'  => $this->trigger_id,
            'program_id'  => (int) $this->program_id,
            'node_id'     => (int) $this->node_id
        ]);

        $this->id = $pdo->lastInsertId();
    }
}