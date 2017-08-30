<?php
require 'vendor/autoload.php';

$dbopts = parse_url(getenv('DATABASE_URL'));

$dsn = 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"] . ';port=' . $dbopts["port"];
$user = $dbopts["user"];
$pass = $dbopts["pass"];

$pdo = new PDO($dsn, $user, $pass);

$pdo = SampleIntegration\DatabaseFactory::getPDO();

$pdo->exec(file_get_contents('db/schema.sql'));
