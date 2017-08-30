<?php

use SampleIntegration\DatabaseFactory;

require_once __DIR__.'/../vendor/autoload.php';

$bootstrap = new \SampleIntegration\Bootstrap();
$bootstrap->loadDotEnv();

$pdo = DatabaseFactory::getPDO();
$pdo->query("DELETE FROM triggered_contacts WHERE time < (CURRENT_TIMESTAMP - INTERVAL '90 minutes')");

echo 'DONE', PHP_EOL;
