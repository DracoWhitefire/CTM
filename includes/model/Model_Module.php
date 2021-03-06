<?php

/**
 * Module object for each application module
 */
Class Model_Module extends Model_Dao
{
    public $id;
    public $minRank;
    public $visible;
    public $menuName;
    public $rank;
    public $position;
    protected static $_tableName = "modules";
        
    /**
     * Gets all modules
     * @return array|object - array of Model_Module (or single instance of Model_Module)
     */
    protected static function _getAll() {
        $query  = "SELECT * ";
        $query .= "FROM `modules` ";
        $query .= "WHERE `visible` = 1 ";
        $query .= "ORDER BY `position` ASC ";
        return self::getByQuery($query);
    }
    
    /**
     * Returns module object by id;
     * @global Controller_Session $session
     * @param int $id - the id if the module
     * @return Model_Module|boolean - Instance of Model_Module or false
     */
    private static function _getById($id) {
        global $session;
        if(!((1 <= $id) && ($id <= self::_getCount()))) { // Prevents GET request abuse - will always connect to valid module
            $id = 1;
        }
        $query  = "SELECT * ";
        $query .= "FROM `modules` ";
        $query .= "WHERE `id` = '" . call_user_func(DB_CLASS . "::queryPrep", $id) . "' ";
        $query .= "LIMIT 1";
        $object = self::getByQuery($query);
        if($object->minRank <= $session->getRank()) {
            return $object;
        } else {
            return FALSE;
        }
    }
    
    
    /**
     * Gets Subject object(s) from query
     * @global Db Object $db
     * @global Session Object $session - current user session
     * @param $selection - defines the query selection
     * @return object or array of objects
     */
    public static function get($selection = "all") {
        global $session;
        if($selection == "all") {
            return self::_getAll();
        } elseif(is_numeric($selection)) {
            if($session->getRank() > 0) {
                return self::_getById($selection);
            } else {
                return self::_getById(LOGIN_MODULE_ID);
            }
        }
    }
    
    /**
     * Returns id of current subject
     * @return int - id of current subject
     */
    public static function getCurrentId() {
        if(Controller_Request::get("id")) {
                (integer) $currentId = Controller_Request::get("id");
        } else {
                (integer) $currentId = 1;
        }
        return $currentId;
    }
}