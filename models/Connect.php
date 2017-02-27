<?php
namespace app\models;

use mysqli;
use app\_interface\Connection;

class Connect implements Connection
{
    static function get_connect()
    {
        global $dsn, $user_name, $password;
        $connect = new mysqli($dsn['mysql_host'], $user_name, $password, $dsn['db_name']);
        if (mysqli_connect_errno()) {
            throw new \Exception(mysqli_connect_error());
        }
        return $connect;
    }
}

?>