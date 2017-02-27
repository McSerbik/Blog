<?php
namespace app\models;

use app\_interface\Change;

class Changes implements Change
{
    static function set_change_flag($did = 1)
    {
        switch ($did) {
            case 1:
                $connect = Connect::get_connect();
                $select = "UPDATE changes SET did = 1;";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;

            case 0:
                $connect = Connect::get_connect();
                $select = "UPDATE changes SET did = 0;";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;
        }

        return null;
    }

    static function get_change_flag()
    {
        $connect = Connect::get_connect();
        $select = "SELECT changes.did FROM changes";
        if (!$connect->multi_query($select)) {
            $trace = debug_backtrace();
            trigger_error(
                "bad select : " . mysqli_error($connect) .
                ' in file ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return null;
        }
        $result = $connect->use_result();
        $result = $result->fetch_assoc();
        $did = $result['did'];
        return $did;
    }

}

?>