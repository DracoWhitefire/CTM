<?php

/**
 * Description of Model_Rank
 */
class Model_Rank extends Model_Dao
{
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
}