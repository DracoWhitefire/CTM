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
    
    /**
     * get
     * Gets Subject object(s) from query
     * @global Db Object $db
     * @global Session Object $session - current user session
     * @param $selection - defines the query selection
     * @return object or array of objects
     */
    public static function get($selection = "all") {
        global $db;
        global $session;
        $loginId = LOGIN_MODULE_ID;
        if($selection == "all") {
            $query  = "SELECT * ";
            $query .= "FROM `modules` ";
            $query .= "WHERE `visible` = 1 ";
            $query .= "ORDER BY `position` ASC ";
            return self::get_by_query($query);
        } elseif(is_numeric($selection)) {
            //Check whether user session exists
            if(isset($session->rank)) {
                $query  = "SELECT * ";
                $query .= "FROM `modules` ";
                $set = $db->query($query);
                $moduleTotal = $db->num_rows($set);
                mysqli_free_result($set);
                // Prevents GET request abuse - will always connect to valid module
                if(!((1 <= $selection) && ($selection <= $moduleTotal))) {
                    $selection = 1;
                }
                $query  = "SELECT * ";
                $query .= "FROM `modules` ";
                $query .= "WHERE `id` = '" . $db->query_prep($selection) . "' ";
                $query .= "LIMIT 1";
                $object = self::get_by_query($query);
                if($object->minRank <= $session->rank) {
                    return $object;
                } else {
                    return FALSE;
                }
            // If no user session exists redirect to login module
            } else {
                $query  = "SELECT * ";
                $query .= "FROM `modules` ";
                $query .= "WHERE `id` = '{$loginId}' ";
                $query .= "LIMIT 1";
                $object = self::get_by_query($query);
                return $object;
            }
        }
    }
    
    /**
     * get_current_id
     * Returns id of current subject
     * @return int - id of current subject
     */
    public static function get_current_id() {
        if(isset($_GET["id"])) {
                (integer) $currentId = $_GET["id"];
        } else {
                (integer) $currentId = 1;
        }
        return $currentId;
    }
}
?>