<?php

/**
 * Description of Model_Rank
 */
class Model_Rank extends Model_Dao
{
    private static $_max;
    private static $_min;
    public $id;
    public $value;
    public $name;
    protected static $_tableName = "ranks";

    /**
     * Instantiates rank object(s);
     * @global object $db
     * @param string $selection
     * @return Model_Rank|array - instance of Model_Rank or array thereof
     */
    public static function get($selection = "all") {
        if($selection == "all") {
            return self::_getAll();
        } elseif(is_numeric($selection)) {	
            return self::_getByValue($selection);
        }
    }
    
    /**
     * Returns the highest value in the rank table;
     * @return int - the highest rank
     */
    private static function _getMax() {
        if(!isset(self::$_max)) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT MAX(`value`) ";
            $query .= "FROM `ranks` ";
            $result = $db->query($query);
            (int) self::$_max = $db->fetchAssoc($result)["MAX(`value`)"];
            mysqli_free_result($result);
        }        
        return self::$_max;
    }
    
    /**
     * Returns the lowest value in the rank table;
     * @return int - the lowest rank
     */
    private static function _getMin() {
        if(!isset(self::$_min)) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT MIN(`value`) ";
            $query .= "FROM `ranks` ";
            $result = $db->query($query);
            (int) self::$_min = $db->fetchAssoc($result)["MIN(`value`)"];
            mysqli_free_result($result);
        }        
        return self::$_min;
    }
    
    /**
     * Returns single Model_Rank based on $value
     * @param int $value - the value for the rank
     * @return Model_Rank - instance of Model_Rank
     */
    private static function _getByValue($value) {
        if((self::_getMin() <= $value) && ($value <= self::_getMax())) {
            $value = call_user_func(DB_CLASS . "::queryPrep", $value);
        } else {
            $value = self::_getMin();
        }
        $query  = "SELECT * ";
        $query .= "FROM `ranks` ";
        $query .= "WHERE `value` = {$value} ";
        $query .= "LIMIT 1 ";
        return self::getByQuery($query);
    }
}