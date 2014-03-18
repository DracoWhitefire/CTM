<?php

/**
 * Description of Model_Team
 */
class Model_Team extends Model_Dao
{
	public $id;
	public $project;
	public $name;
	public $count;
	
	public static function get($selection = "all") {
		global $db;
		$query  = "SELECT * ";
		$query .= "FROM `teams` ";
		$result = $db->query($query);
		$numRows = $db->num_rows($result);
		mysqli_free_result($result);		
		if($selection == "all") {
			return self::get_by_query($query);
		} elseif(is_numeric($selection)) {
			if((1 <= $selection) && ($selection <= $numRows)) {
				$selection = $db->query_prep($selection);
			} else {
				$selection = 2;
			}			
			$query .= "WHERE `id` = {$selection} ";
			return self::get_by_query($query);
		}		
	}
}
?>