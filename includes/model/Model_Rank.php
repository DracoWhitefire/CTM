<?php

/**
 * Description of Model_Rank
 */
class Model_Rank extends Model_Dao
{
	public $id;
	public $value;
	public $name;
	
	public static function get($selection = "all") {
		global $db;
		$query  = "SELECT MAX(`value`) ";
		$query .= "FROM `ranks` ";
		$result = $db->query($query);
		$maxValue = $db->fetch_assoc($result)["MAX(`value`)"];
		mysqli_free_result($result);
		$query  = "SELECT * ";
		$query .= "FROM `ranks` ";
		if($selection == "all") {
			return self::get_by_query($query);
		} elseif(is_numeric($selection)) {	
			if((1 <= $selection) && ($selection <= $maxValue)) {
				$selection = $db->query_prep($selection);
			} else {
				$selection = 1;
			}
			$query .= "WHERE `value` = {$selection} ";
			$query .= "LIMIT 1 ";
			return self::get_by_query($query);
		}
	}

	public static function selector($user = "") {
		$output = "<select id=\"" . htmlspecialchars("rank_select");
		if($user!="") {
			$output .= htmlspecialchars("_" . $user->id);
		}
		$output .= "\" name=\"" . htmlspecialchars("rank_select");
		if($user!="") {
			$output .= htmlspecialchars("_" . $user->id);
		}
		$output .= "\">";
		$ranks_array = self::get();
		foreach($ranks_array as $rank) {
			$output .= "<option value=\"" . htmlspecialchars($rank->value) . "\" ";
				if($user!="") {
					if($user->rank == $rank->value) {
						$output .= "selected=\"selected\" ";
					}
				}
			$output .= ">" . htmlspecialchars($rank->name) . "</option>";
		}
		$output .= "</select>";
		return $output;
	}
}
?>