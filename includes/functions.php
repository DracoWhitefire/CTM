<?php
require_once("config.php");
include("FirePHPCore/FirePHP.class.php");

function __autoload($className) {
    $classArray = explode("_", $className);
    $classPath = strtolower($classArray[0]) . "/" . $className . ".php";
    include($classPath);
}

$debug = TRUE;
?>