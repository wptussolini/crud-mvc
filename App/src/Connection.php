<?php

namespace App\src;

class Connection
{
    private static $connection;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        $config = parse_ini_file(BASE_PATH.'/App/config.ini');
        if (is_null(self::$connection)) {
            self::$connection = new \PDO('mysql:host='. $config['host'] .';port='. $config['port'] .';dbname='. $config['dbname'], $config['user'], $config['password']);
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection->exec('set names utf8');
        }
        return self::$connection;
    }
}