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
        if($selection == "all") {
            return self::_getAll();
        } elseif(is_numeric($selection)) {	
            return self::_getByValue($selection);
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
    
    /**
     * _getByValue
     * Returns single Model_Rank based on $value
     * @param int $value - the value for the rank
     * @return Model_Rank - instance of Model_Rank
     */
    private static function _getByValue($value) {
        $db = call_user_func(DB_CLASS . "::getInstance");
        if((1 <= $value) && ($value <= self::_getMax())) {
            $value = $db->query_prep($value);
        } else {
            $value = 1;
        }
        $query  = "SELECT * ";
        $query .= "FROM `ranks` ";
        $query .= "WHERE `value` = {$value} ";
        $query .= "LIMIT 1 ";
        return self::get_by_query($query);
    }
}