<?php

$dir = ROOT . "/config";
$catalog = opendir($dir);
while ($filename = readdir($catalog)) {
    if ($filename != '.' and $filename != '..') {
        $filename = $dir . "/" . $filename;
        require_once($filename);
    }
}
closedir($catalog);

$dir = ROOT . "/components";
$catalog = opendir($dir);
while ($filename = readdir($catalog)) {
    if ($filename != '.' and $filename != '..') {
        $filename = $dir . "/" . $filename;
        require_once($filename);
    }
}
closedir($catalog);

$dir = ROOT . "/interface";
$catalog = opendir($dir);
while ($filename = readdir($catalog)) {
    if ($filename != '.' and $filename != '..') {
        $filename = $dir . "/" . $filename;
        require_once($filename);
    }
}
closedir($catalog);

$dir = ROOT . "/models";
$catalog = opendir($dir);
while ($filename = readdir($catalog)) {
    if ($filename != '.' and $filename != '..') {
        $filename = $dir . "/" . $filename;
        require_once($filename);
    }
}
closedir($catalog);

closedir($catalog);


