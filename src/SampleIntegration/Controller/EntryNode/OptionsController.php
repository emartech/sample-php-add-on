<?php

namespace SampleIntegration\Controller\EntryNode;

use Symfony\Component\HttpFoundation\JsonResponse;

class OptionsController
{

    public function index()
    {
        return new JsonResponse([
            ['id' => 'initiated', 'name' => 'Initiated'],
            ['id' => 'picked_up', 'name' => 'Picked up'],
            ['id' => 'in_transit', 'name' => 'In transit'],
            ['id' => 'delivered', 'name' => 'Delivered']
        ]);
    }

}