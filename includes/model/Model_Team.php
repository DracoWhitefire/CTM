<?php

/**
 * Model_Team
 */
class Model_Team extends Model_Dao
{
	public $id;
	public $project;
	public $name;
        protected static $_tableName = "teams";
	
	public static function get($selection = "all") {
		$db = call_user_func(DB_CLASS . "::getInstance");
		$query  = "SELECT * ";
		$query .= "FROM `teams` ";		
		if($selection == "all") {
			return self::_getAll();
		} elseif(is_numeric($selection)) {
			if((1 <= $selection) && ($selection <= self::_getCount())) {
				$selection = call_user_func(DB_CLASS . "::query_prep", $selection);
			} else {
				$selection = 2;
			}			
			$query .= "WHERE `id` = {$selection} ";
			return self::get_by_query($query);
		}		
	}
        
        /**
         * get_selected
         * Returns the currently selected team;
         * @return int - the currently selected team
         */
        public static function get_selected() {
            if(isset($_POST["teamSelect"])) {
                return (int) $_POST["teamSelect"];
            } elseif(isset($_GET["teamSelect"])) {
                return (int) $_GET["teamSelect"];
            } elseif (isset ($_SESSION["teamSelect"])) {    
                return (int) $_SESSION["teamSelect"];
            } else {
                return 1;
            }
        }
}