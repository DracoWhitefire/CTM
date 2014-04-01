<?php

/**
 * Generic Database Access Object class abstract
 */
abstract class Model_Dao
{
    protected $_tableName;
    var $_columns;
    
    /**
     * _get_columns
     * Populates and returns $this->_columns with db column names and converted object var names
     * @global dbObject $db
     * @return array - this object's column names and respective var names
     */
    public function _get_columns() {
        global $db;
        if(is_null($this->_columns)) {
            $columnQuery  = "SELECT `COLUMN_NAME` ";
            $columnQuery .= "FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
            $columnQuery .= "WHERE `TABLE_NAME`='";
            $columnQuery .= $db->query_prep($this->_tableName);
            $columnQuery .= "';";
            $columnResult = $db->query($columnQuery);
            while($row = $db->fetch_assoc($columnResult)) {
                $this->_columns[$row["COLUMN_NAME"]] = $this->_column_to_var($row["COLUMN_NAME"]);
            }
            mysqli_free_result($columnResult);
        }
        return $this->_columns;
    }
    
    /**
     * _column_to_var
     * Turns db column name into object var name;
     * @param type $column - the column name to be converted
     * @return string - the converted attribute name
     */
    private function _column_to_var($column) {
        //SQL table names and columns are separated by underscore.
        //Object attributes are camelCase instead.
        if(!function_exists("rename_attribute")) { 
            function rename_attribute(array $strings) {
                return $strings[1] . ucfirst($strings[2]);
            }
        }
        $convertedAttribute = preg_replace_callback("/^(\w+?)_(\w+?)$/", "rename_attribute", $column);
        if($this->_has_attribute($convertedAttribute)) {
            return $convertedAttribute;
        } elseif($this->_has_attribute("_" . $convertedAttribute)) {
            return "_" . $convertedAttribute;
        }
    }
    
    private function _var_to_column($var) {
        if(!function_exists("rename_attribute")) { 
            function rename_attribute(array $strings) {
                return $strings[1] . "_" . strtolower($strings[2]);
            }
        }
        $convertedAttribute = preg_replace_callback("/^([a-z]+?)([A-Z][a-z]+?)$/", "rename_attribute", $column);
        if($this->_has_attribute($var)) {
            return $convertedAttribute;
        } elseif($this->_has_attribute("_" . $convertedAttribute)) {
            return "_" . $convertedAttribute;
        }
    }
    
    /**
     * _instantiate
     * Instatiates object from query result row
     * @param array $row - Row of a fetch_assoc
     * @return \static object - Instantiated object
     */
    private static function _instantiate(array $row) {
        $object = new static;
        $object->_get_columns();
        foreach($row as $columnName => $value) {
            $varName = $object->_columns[$columnName];
            $object->$varName = $value;
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
        //print_r($vars);
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
            $columnVars = array_flip($this->_columns);
            $query  = "INSERT INTO `" . $this->_tableName . "` (";
            $count = 0;
            foreach($vars as $var->$value) {
                    $count += 1;
                    if(in_array($var, $columnVars)) {
                        $query .= $db->query_prep($var);
                    }
                    if ($count < count($vars)) {
                            $query .= ", ";
                    } else {
                            $query .= ")";
                    }
            }
            $query .= " VALUES (";
            foreach($vars as $var->$value) {
                    $count += 1;
                    if(in_array($var, $columnVars)) {
                        $query .= $db->query_prep($value);
                    }
                    if ($count < count($vars)) {
                            $query .= ", ";
                    } else {
                            $query .= ")";
                    }
            }
            echo $query;
    }
    
    function _update() {
        global $db;
        $vars = get_object_vars($this);
        $columnVars = array_flip($this->_columns);
        $query = "UPDATE `" . $this->_tableName . "` SET ";
        $count = 0;
        foreach($vars as $var => $value) {
            $count += 1;
            if(array_key_exists($var, $columnVars)) {
                if($var != "id") {
                    $query .= "`";
                    $query .= $db->query_prep($columnVars[$var]);
                    $query .= "`='";
                    $query .= $db->query_prep($this->$var);
                    if ($count < count($columnVars)) {
                        $query .= "', ";
                    } else {
                        $query .= "' ";
                    }
                }
            }
        }
        $query .= "WHERE `id` = " . $this->id . " ";
        $query .= "LIMIT 1";
        $query .= ";";
        $db->query($query);
    }
    
    public function save() {
        return isset($this->id) ? $this->_update() : $this->_create();
    }
    public function delete() {
        global $db;
    }
}