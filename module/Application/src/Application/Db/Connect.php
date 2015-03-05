<?php

namespace Application\Db;

class Connect
{
    private static  $connect;

    public static function defaultConnection()
    {
        if(is_null(self::$connect)) {
            $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
            $dr = DIRECTORY_SEPARATOR;
            $pdo = new \PDO("sqlite:{$root}.{$dr}data{$dr}db{$dr}mkr.db");
            self::$connect = $pdo;
        }
        return self::$connect;
    }

}