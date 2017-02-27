<?php
namespace app\_interface;

interface Change
{
    static function set_change_flag($did = 1);

    static function get_change_flag();
    
}

?>