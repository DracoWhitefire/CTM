<?php

/**
 * Generic Database Access Object class abstract
 */
abstract class Model_Dao
{
    /**
     * _instantiate
     * Instatiates object from query result row
     * @param array $row - Row of a fetch_assoc
     * @return \static object - Instantiated object
     */
    private static function _instantiate(array $row) {
        $object = new static;
        foreach($row as $attribute => $value) {
            //SQL table names and columns are separated by underscore.
            //Object attributes are camelCase instead.
            $converted_attribute = preg_replace("/^(\w+?)_(\w+?)$/e", "\"$1\" . ucfirst(\"$2\")", $attribute);
            if($object->_has_attribute($converted_attribute)) {
                $object->$converted_attribute = $value;
            }
        }
        return $object;
    }
    
    /**
     * _has_attribute
     * Checks whether object $this has attribute $attribute
     * @param type $attribute
     * @return bool
     */
    private function _has_attribute($attribute) {
        $vars = get_object_vars($this);
        return array_key_exists($attribute, $vars);
    }
    
    /**
     * get_by_query
     * Fetches object or array of objects from query $query
     * @global dbObject $db
     * @param string $query
     * @return array $objectArray - if query results in multiple objects
     * @return object - if query results in single object
     */
    public static function get_by_query($query) {
        global $db;
        $objectArray = array();
        $resultSet = $db->query($query);
        while($row = $db->fetch_assoc($resultSet)) {
            $objectArray[] = static::_instantiate($row);
        }
        mysqli_free_result($resultSet);
        if(count($objectArray) == 1) {
            return $objectArray[0];
        } else {
            return $objectArray;
        }
    }
    
    /**
     * create
     * Creates new db entry from Object
     * @global dbObject $db
     * @return NULL 
     */
    protected function _create() {
            global $db;
            $vars = get_object_vars($this);
            $query  = "INSERT INTO `" . $table . "` (";
            $count = 0;
            foreach($vars as $var->$value) {
                    $count += 1;
                    $query .= $db->query_prep($var);
                    if ($count < count($vars)) {
                            $query .= ", ";
                    } else {
                            $query .= ")";
                    }
            }
            $query .= " VALUES (";
            foreach($vars as $var->$value) {
                    $count += 1;
                    $query .= $db->query_prep($value);
                    if ($count < count($vars)) {
                            $query .= ", ";
                    } else {
                            $query .= ")";
                    }
            }
            echo $query;
    }
    protected function _update() {
            global $db;
    }
    public function save() {
            return isset($this->id) ? $this->_update() : $this->_create();
    }
    public function delete() {
            global $db;
    }
}