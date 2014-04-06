<?php

/**
 * Generic Database Access Object class abstract
 */
abstract class Model_Dao
{
    protected static $_tableName;
    protected static $_count;
    protected $_columns;
    
    /**
     * _get_columns
     * Populates and returns $this->_columns with db column names and converted object var names
     * @global dbObject $db
     * @return array - this object's column names and respective var names
     */
    protected function _getColumns() {
        if(!isset($this->_columns)) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            if(is_null($this->_columns)) {
                $columnQuery  = "SELECT `COLUMN_NAME` ";
                $columnQuery .= "FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
                $columnQuery .= "WHERE `TABLE_NAME`='";
                $columnQuery .= call_user_func(DB_CLASS . "::query_prep", static::$_tableName);
                $columnQuery .= "';";
                $columnResult = $db->query($columnQuery);
                while($row = $db->fetch_assoc($columnResult)) {
                    $this->_columns[$row["COLUMN_NAME"]] = $this->_columnToVar($row["COLUMN_NAME"]);
                }
                mysqli_free_result($columnResult);
            }
        }
        return $this->_columns;
    }
    
    /**
     * _getCount
     * Returns the amount of rows;
     * @return int self::$_count - The amount of rows
     */
    protected static function _getCount() {
        if(!isset(self::$_count)) {
            $db = call_user_func(DB_CLASS . "::getInstance");
            $query  = "SELECT COUNT(*) ";
            $query .= "AS `rowCount`";
            $query .= "FROM `" . static::$_tableName . "` ";
            $set = $db->query($query);
            self::$_count = $db->fetch_assoc($set)["rowCount"];
            mysqli_free_result($set);
        }
        return self::$_count;
    }
    
    /**
     * _column_to_var
     * Turns db column name into object var name;
     * @param type $column - the column name to be converted
     * @return string - the converted attribute name
     */
    private function _columnToVar($column) {
        //SQL table names and columns are separated by underscore.
        //Object attributes are camelCase instead.
        if(!function_exists("rename_attribute")) { 
            function rename_attribute(array $strings) {
                return $strings[1] . ucfirst($strings[2]);
            }
        }
        $convertedAttribute = preg_replace_callback("/^(\w+?)_(\w+?)$/", "rename_attribute", $column);
        if($this->_hasAttribute($convertedAttribute)) {
            return $convertedAttribute;
        } elseif($this->_hasAttribute("_" . $convertedAttribute)) {
            return "_" . $convertedAttribute;
        }
    }
    
    /**
     * _var_to_column
     * Turns object var name into db column name;
     * @param string $var - the var name to be converted
     * @return string - the converted column name
     */
    private function _varToColumn($var) {
        if(!function_exists("rename_attribute")) { 
            function rename_attribute(array $strings) {
                return $strings[1] . "_" . strtolower($strings[2]);
            }
        }
        $convertedAttribute = preg_replace_callback("/^([a-z]+?)([A-Z][a-z]+?)$/", "rename_attribute", $column);
        if($this->_hasAttribute($var)) {
            return $convertedAttribute;
        } elseif($this->_hasAttribute("_" . $convertedAttribute)) {
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
        $object->_getColumns();
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
    private function _hasAttribute($attribute) {
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
        $db = call_user_func(DB_CLASS . "::getInstance");
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
     * _getAll
     * Returns all instances of self;
     * @return object|array - returns instance of self or array of instances
     */
    protected static function _getAll() {
        $query  = "SELECT * ";
        $query .= "FROM " . static::$_tableName . " ";
        $query .= "ORDER BY `id` ASC";
        return self::get_by_query($query);
    }
    
    /**
     * create
     * Creates new db entry from Object
     * @global dbObject $db
     */
    private function _create() {
        $db = call_user_func(DB_CLASS . "::getInstance");
        $vars = get_object_vars($this);
        $columnVars = array_flip($this->_getColumns());
        $query  = "INSERT INTO `" . $this->_tableName . "` (";
        $valueCount = 0;
        $count = 0;
        foreach($vars as $var => $value) {
            isset($value) ? $valueCount++ : NULL;
        }
        foreach($vars as $var => $value) {
            $count += 1;
            if(array_key_exists($var, $columnVars) && !is_null($value)) {
                $query .= "`" . call_user_func(DB_CLASS . "::query_prep", $columnVars[$var]) . "`";
                if ($count < $valueCount) {
                        $query .= ", ";
                }
            }
        }
        $query .= ") VALUES (";
        $count = 0;
        foreach($vars as $var => $value) {
            $count += 1;
            if(array_key_exists($var, $columnVars) && !is_null($value)) {
                $query .= "'" . call_user_func(DB_CLASS . "::query_prep", $value) . "'";
                if ($count < $valueCount) {
                        $query .= ", ";
                }
            }
        }
        $query .= ") ";
        $query .= ";";
        $db->query($query);
    }
    
    /**
     * _update
     * Updates existing db record with current object;
     * @global dbObject $db
     */
    private function _update() {
        $db = call_user_func(DB_CLASS . "::getInstance");
        $vars = get_object_vars($this);
        $columnVars = array_flip($this->_columns);
        $query = "UPDATE `" . static::$_tableName . "` SET ";
        $count = 0;
        foreach($vars as $var => $value) {
            $count += 1;
            if(array_key_exists($var, $columnVars)) {
                if($var != "id") {
                    $query .= "`";
                    $query .= call_user_func(DB_CLASS . "::query_prep", $columnVars[$var]);
                    $query .= "`='";
                    $query .= call_user_func(DB_CLASS . "::query_prep", $this->$var);
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
    
    /**
     * save
     * Creates or updates db entry based on current object;
     * @return bool - Did the operation succeed?
     */
    public function save() {
        return !is_null($this->id) ? $this->_update() : $this->_create();
    }
    
    /**
     * delete
     * Deletes current object from memory and entry from db;
     * @global dbObject $db
     */
    public function delete() {
        $db = call_user_func(DB_CLASS . "::getInstance");
    }
}