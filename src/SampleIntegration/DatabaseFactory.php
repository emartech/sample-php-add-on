<?php

namespace SampleIntegration;

class DatabaseFactory
{
    private static $pdo;

    public static function getPDO()
    {
        if (!is_null(self::$pdo)) return self::$pdo;

        $dbopts = parse_url(getenv('DATABASE_URL'));

        $dsn = 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"] . ';port=' . $dbopts["port"];
        $user = $dbopts["user"];
        $pass = $dbopts["pass"];

        self::$pdo = new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

        return self::$pdo;
    }
}