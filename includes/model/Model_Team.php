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
         * Returns instance of self;
         * @param int $id - the id
         * @return Model_Team - instance of self
         */
        protected static function _getById($id) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT * ";
            $query .= "FROM `teams` ";
            if((1 <= $id) && ($id <= self::_getCount())) {
                $id = call_user_func(DB_CLASS . "::queryPrep", $id);
            } else {
                $id = 2;
            }			
            $query .= "WHERE `id` = {$id} ";
            return self::getByQuery($query);
        }
        
        /**
         * Returns the currently selected team;
         * @return int - the currently selected team
         */
        public static function getSelected() {
            if(Controller_Request::post("teamSelect")) {
                return (int) Controller_Request::post("teamSelect");
            } elseif(Controller_Request::get("teamSelect")) {
                return (int) Controller_Request::get("teamSelect");
            } elseif (Controller_Request::session("teamSelect")) {    
                return (int) Controller_Request::session("teamSelect");
            } else {
                return 1;
            }
        }
}