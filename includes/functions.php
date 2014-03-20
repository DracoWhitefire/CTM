<?php
require_once("config.php");
include("FirePHPCore/FirePHP.class.php");

function __autoload($className) {
    $classArray = explode("_", $className);
    $classPath = strtolower($classArray[0]) . "/" . $className . ".php";
    include($classPath);
}
if(!defined("DEBUGMODE") || (DEBUGMODE == TRUE)) {
    $debug = TRUE;
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
$debug = TRUE;
