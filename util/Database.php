<?php

require_once 'config/Constants.php';

class Database
{
    public static function getConnection()
    {
        $connect = new PDO('mysql:host='.Constants::DB['HOSTNAME'].';port='.Constants::DB['PORT'].';dbname='.Constants::DB['DATABASE'], Constants::DB['USERNAME'], Constants::DB['PASSWORD']);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connect->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);

        return $connect;
    }
}
