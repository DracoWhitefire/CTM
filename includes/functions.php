<?php
require_once("config.php");

function __autoload($className) {
    $classArray = explode("_", $className);
    $classPath = $classArray[0] . "/" . $className . ".php";
    include($classPath);
}

?>