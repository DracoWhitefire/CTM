<?php

/**
 * Description of Model_Rank
 */
class Model_Rank extends Model_Dao
{
    private static $_max;
    public $id;
    public $value;
    public $name;
    protected $_tableName = "ranks";

    /**
     * get
     * Instantiates rank object(s);
     * @global object $db
     * @param string $selection
     * @return object|array - instance of Model_Rank or array thereof
     */
    public static function get($selection = "all") {
        $db = call_user_func(DB_CLASS . "::getInstance");
        $query  = "SELECT * ";
        $query .= "FROM `ranks` ";
        if($selection == "all") {
            return self::_getAll();
        } elseif(is_numeric($selection)) {	
            if((1 <= $selection) && ($selection <= self::_getMax())) {
                $selection = $db->query_prep($selection);
            } else {
                $selection = 1;
            }
            $query .= "WHERE `value` = {$selection} ";
            $query .= "LIMIT 1 ";
            return self::get_by_query($query);
        }
    }
    
    /**
     * _getMax
     * Returns the highest value in the rank table;
     * @return int - the highest rank
     */
    private static function _getMax() {
        if(!isset(self::$_max)) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT MAX(`value`) ";
            $query .= "FROM `ranks` ";
            $result = $db->query($query);
            (int) self::$_max = $db->fetch_assoc($result)["MAX(`value`)"];
            mysqli_free_result($result);
        }        
        return self::$_max;
    }
    
    /**
     * _getAll
     * Returns all instances of self;
     * @return Model_Rank|array - returns instance of self or array of instances
     */
    private static function _getAll() {
        $query  = "SELECT * ";
        $query .= "FROM `ranks` ";
        return self::get_by_query($query);
    }
}