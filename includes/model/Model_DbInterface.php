<?php

/**
 *  interface for db classes
 *  for technology independence
 */
interface Model_DbInterface
{
    /**
     * Singleton method to instantiate single instance;
     * @return Model_DbInterface - instance of self
     */
    public static function getInstance();
    
    /**
     * Disconnects from database;
     */
    public static function disconnect();

    /**
     * Prepares a query for database operation;
     * @param string $value - raw query
     * @return string $value - safe query
     */
    public static function queryPrep($value);

    /**
     * Fetches associative array from query result;
     * @param result $result - result set from db query
     * @return array - associative array from result set
     */
    public function fetchAssoc($result);

    /**
     * Returns number of rows in result set;
     * @param result $result - result set from db query
     * @return int $count - number of rows in result set
     */
    public function numRows($result);

    /**
     * Returns id of last inserted row;
     * @return int - id of last insert
     */
    public function getInsertId();

    /**
     * Returns amount of rows affected by last query;
     * @return int - amount of affected rows
     */
    public function affectedRows();

    /**
     * Processes MySQL query
     * @param string $query - The query to be processed
     * @return result $result - Result set from query
     */
    public function query($query);
}