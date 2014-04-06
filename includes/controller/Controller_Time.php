<?php

/**
 * Controller_Time
 */
Class Controller_Time
{
    /**
     * Formats a time string for html output or db entries;
     * @param string $timeString - the string to be converted
     * @param string $target - the target of the string
     * @return string $result - the formatted string
     */
    static function format($timeString, $target) {
	if($target == "db") {
            $tempResult = preg_replace("/^([0-9]:[0-9]{2})(:[0-9]{2})?$/", "0\\1\\2", $timeString);
            $result = preg_replace("/^([0-9]{2}:[0-9]{2})$/", "\\1:00", $tempResult);
	} elseif ($target == "html") {
            $result = preg_replace("/^(0)?([1-9]?[0-9]:[0-9]{2})(:[0-9]{2})$/", "\\2", $timeString);			
	}
	return htmlspecialchars($result);
    }
}
