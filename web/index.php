<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$bootstrap = new \SampleIntegration\Bootstrap();
$bootstrap->start();
