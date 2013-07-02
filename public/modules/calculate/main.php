
<?php
	$var = "user_name_3";
	$array = preg_split("/([A-Z][a-z]+)|_/", $var, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
	print_r($array);
	echo "<br />";
	$var = "userName_input";
	$array = preg_split("/([[A-Z][a-z]+)|_/", $var, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
	print_r($array);
	echo MYSQL_NUM;
?>
