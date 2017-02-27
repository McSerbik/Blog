<?php

define('ROOT', dirname(__FILE__));
require_once(ROOT . "/autoload.php");

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

$router = new Router();

echo $router->run();

?>
