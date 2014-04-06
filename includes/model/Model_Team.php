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
	
        /**
         * get
         * Returns (array of) instance(s) of self
         * @param string $selection
         * @return object|array - (array of) instance(s) of self
         */
	public static function get($selection = "all") {
            if($selection == "all") {
                return self::_getAll();
            } elseif(is_numeric($selection)) {
                return self::_getById($selection);
            }		
	}
        
        /**
         * _getById
         * Returns instance of self;
         * @param int $id - the id
         * @return Model_Team - instance of self
         */
        protected static function _getById($id) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT * ";
            $query .= "FROM `teams` ";
            if((1 <= $id) && ($id <= self::_getCount())) {
                $id = call_user_func(DB_CLASS . "::query_prep", $id);
            } else {
                $id = 2;
            }			
            $query .= "WHERE `id` = {$id} ";
            return self::get_by_query($query);
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