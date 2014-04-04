<?php

/**
 * Implementation of Model_Db for MySQL database
 */
final class Model_MySqlDb implements Model_DbInterface
{
    private $_connection;
    private $_magicQuotesActive;
    private $_mysqliRealEscapeStringExists;
    private static $_singleInstance;
    public $last_query;

    private function __construct() {
        $this->_connect();
        $this->_magicQuotesActive = get_magic_quotes_gpc();
        $this->_mysqliRealEscapeStringExists = function_exists("mysqli_real_escape_string"); //PHP5
    }
    
    public static function getInstance() {
        !isset(self::$_singleInstance) ? self::$_singleInstance = new self : NULL;
        return self::$_singleInstance;
    }
    
    /**
     * _connect
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
     * disconnect
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
     * query_prep
     * Prepares a query for database operation;
     * @param string $value - raw query
     * @return string $value - safe query
     */
    public function query_prep($value) {
        if($this->_mysqliRealEscapeStringExists) {
            if($this->_magicQuotesActive) {
                $value = $this->_connection->real_escape_string(stripslashes($value));
            }
        } else {
            if(!$this->_magicQuotesActive) {
                $value = $this->_connection->real_escape_string($value);
            }
        }
        return trim($value);
    }

    /**
     * fetch_assoc
     * Fetches associative array from query result;
     * @param result $result - result set from db query
     * @return array - associative array from result set
     */
    public function fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }

    /**
     * num_rows
     * Returns number of rows in result set;
     * @param result $result - result set from db query
     * @return int $count - number of rows in result set
     */
    public function num_rows($result) {
        return mysqli_num_rows($result);
    }

    /**
     * insert_id
     * Returns id of last inserted row;
     * @return int - id of last insert
     */
    public function insert_id() {
        return mysqli_insert_id($this->_connection);
    }

    /**
     * affected_rows
     * Returns amount of rows affected by last query;
     * @return int - amount of affected rows
     */
    public function affected_rows() {
        return mysqli_affected_rows($this->_connection);
    }

    /**
     * _mysqli_confirm
     * Confirms success of query - kills on failure;
     * @global bool $debug - Is debugging turned on?
     * @param result $result - Result set of query
     */
    private function _mysqli_confirm($result) {
        global $debug;
        if(!$result) {
            if($debug == TRUE) {
                $message = "Database query failed: " . $this->_connection->error;
                $message .= "<br />" . $this->last_query;
            } else {
                $message = "There was an error. Please contact the system administrator.";
            }
            die($message);
        }
    }

    /**
     * query
     * Processes MySQL query
     * @param string $query - The query to be processed
     * @return result $result - Result set from query
     */
    public function query($query) {
        $this->last_query = $query;
        $result = mysqli_query($this->_connection, $query);
        $this->_mysqli_confirm($result);
        return $result;
    }
}