<?php

/**
 * Implementation of Model_Db for MySQL database
 */
final class Model_MySqlDb implements Model_DbInterface
{
    private $_connection;
    private $_lastQuery;
    private static $_singleInstance;
    private static $_magicQuotesActive;
    private static $_mysqliRealEscapeStringExists;
    

    private function __construct() {
        $this->_connect();
    }
    
    /**
     * Singleton getter for self;
     * @return Model_MySqlDb - instance of self
     */
    public static function getInstance() {
        !isset(self::$_singleInstance) ? self::$_singleInstance = new self : NULL;
        return self::$_singleInstance;
    }
    
    /**
     * Getter for lazy instantiation of self::$_magicQuotesActive;
     * @return bool - is Magic Quotes active?
     */
    private static function _getMagicQuotesActive() {
        !isset(self::$_magicQuotesActive) ? self::$_magicQuotesActive = get_magic_quotes_gpc() : NULL;
        return self::$_magicQuotesActive;
    }
    
    /**
     * Getter for lazy instantiation of self::$_mysqliRealEscapeStringExists;
     * @return bool - does mysqli_real_escape_string() exist (so it's PHP5)?
     */
    private static function _getMysqliRealEscapeStringExists() {
        !isset(self::$_mysqliRealEscapeStringExists) ? self::$_mysqliRealEscapeStringExists = function_exists("mysqli_real_escape_string") : NULL;
        return self::$_mysqliRealEscapeStringExists;
    }
    
    /**
     * Establish connection with DB;
     * @return void
     */
    private function _connect() {
        global $debug;
        $this->_connection = mysqli_connect(DB_SERVER , DB_USER, DB_PW, DB_NAME);
        if($this->_connection->connect_errno) {
            if($debug == TRUE) {
                $message  = "MySQL connection failed: " . $this->_connection->connect_error;
                $message .= "<br />" . $this->_connection->connect_errno;
            } else {
                $message = "There was an error. Please contact the system administrator.";
            }
            die($message);
        }
    }

    /**
     * Unsets Connection;
     * @return void
     */
    public static function disconnect() {
        if(isset(self::$_singleInstance)) {
            if(isset(self::$_singleInstance->_connection)) {
                mysqli_close(self::$_singleInstance->_connection);
                unset(self::$_singleInstance->_connection);
            }
        }
    }
    
    /**
     * Returns the last executed query if it exists, or null;
     * @return string|null - Either last query or null
     */
    private function _getLastQuery() {
        return isset($this->_lastQuery) ? $this->_lastQuery : NULL;
    }
    
    /**
     * Saves the last query to _lastQuery;
     * @param string $query - the query string to be saved
     */
    private function _setLastQuery($query) {
        $this->_lastQuery = $query;
    }
    
    /**
     * Prepares a query for database operation;
     * @param string $value - raw query
     * @return string $value - safe query
     */
    public static function queryPrep($value) {
        if(self::_getMysqliRealEscapeStringExists()) {
            if(self::_getMagicQuotesActive()) {
                $value = mysqli::real_escape_string(stripslashes($value));
            }
        } else {
            if(!$this->_magicQuotesActive) {
                $value = mysqli::real_escape_string($value);
            }
        }
        return trim($value);
    }

    /**
     * Fetches associative array from query result;
     * @param result $result - result set from db query
     * @return array - associative array from result set
     */
    public function fetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }

    /**
     * Returns number of rows in result set;
     * @param result $result - result set from db query
     * @return int $count - number of rows in result set
     */
    public function numRows($result) {
        return mysqli_num_rows($result);
    }

    /**
     * Returns id of last inserted row;
     * @return int - id of last insert
     */
    public function getInsertId() {
        return mysqli_insert_id($this->_connection);
    }

    /**
     * Returns amount of rows affected by last query;
     * @return int - amount of affected rows
     */
    public function affectedRows() {
        return mysqli_affected_rows($this->_connection);
    }

    /**
     * Confirms success of query - kills on failure;
     * @global bool $debug - Is debugging turned on?
     * @param result $result - Result set of query
     */
    private function _queryConfirm($result) {
        global $debug;
        if(!$result) {
            if($debug == TRUE) {
                $message = "Database query failed: " . $this->_connection->error;
                $message .= "<br />" . $this->_getLastQuery();
            } else {
                $message = "There was an error. Please contact the system administrator.";
            }
            die($message);
        }
    }

    /**
     * Processes MySQL query;
     * @param string $query - The query to be processed
     * @return result $result - Result set from query
     */
    public function query($query) {
        $this->_setLastQuery($query);
        $result = mysqli_query($this->_connection, $query);
        $this->_queryConfirm($result);
        return $result;
    }
}