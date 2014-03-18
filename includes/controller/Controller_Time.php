<?php

/**
 * Description of Controller_Time
 */
Class Controller_Time
{
    static function format($time_string, $target) {
	if($target == "db") {
            $tempresult = preg_replace("/^([0-9]:[0-9]{2})(:[0-9]{2})?$/", "0\\1\\2", $time_string);
            $result = preg_replace("/^([0-9]{2}:[0-9]{2})$/", "\\1:00", $tempresult);
	} elseif ($target == "html") {
            $result = preg_replace("/^(0)?([1-9]?[0-9]:[0-9]{2})(:[0-9]{2})$/", "\\2", $time_string);			
	}
	return htmlspecialchars($result);
    }
}
?>
