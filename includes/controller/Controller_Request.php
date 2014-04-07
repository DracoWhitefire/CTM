<?php
/**
 * Handles and filters all GET, POST, and SESSION requests
 */
class Controller_Request
{
    private static $_getStore;
    private static $_postStore;
    private static $_cookieStore;
    private static $_sessionStore;
    
    /**
     * prepares string for output/database
     * @param string $string
     * @return string
     */
    private static function _cleanup($string) {
        return call_user_func(DB_CLASS . "::queryPrep", $string);
    }
    
    /**
     * Handler to filter GET requests;
     * @param string $keyName - the _GET key
     * @return string - the filtered value for $keyName
     */
    public static function get($keyName) {
        if(!isset(static::$_getStore)) {
            foreach($_GET as $key => $value) {
                static::$_getStore[$key] = self::_cleanup($value);
            }
        }
        return isset(static::$_getStore[$keyName]) ? static::$_getStore[$keyName] : false;
    }
    
    /**
     * Handler to filter POST requests;
     * @param string $keyName - the _POST key
     * @return string - the filtered value for $keyName
     */
    public static function post($keyName) {
        if(!isset(static::$_postStore)) {
            foreach($_POST as $key => $value) {
                static::$_postStore[$key] = self::_cleanup($value);
            }
        }
        return isset(static::$_postStore[$keyName]) ? static::$_postStore[$keyName] : false;
    }
    
    /**
     * Handler to filter COOKIE requests;
     * @param string $keyName - the _COOKIE key
     * @return string - the filtered value for $keyName
     */
    public static function cookie($keyName) {
        if(!isset(static::$_cookieStore)) {
            foreach($_COOKIE as $key => $value) {
                static::$_cookieStore[$key] = self::_cleanup($value);
            }
        }
        return isset(static::$_cookieStore[$keyName]) ? static::$_cookieStore[$keyName] : false;
    }
    
    /**
     * Handler to filter SESSION requests;
     * @param string $keyName - the _SESSION key
     * @return string|array - the filtered value(s) for $keyName
     */
    public static function session($keyName) {
        if(!isset(static::$_sessionStore)) {
            foreach($_SESSION as $key => $value) {
                if(is_string($value)) {
                    static::$_sessionStore[$key] = self::_cleanup($value);
                } elseif(is_array($value)) {
                    foreach($value as $valueKey => $valueValue) {
                        $value[$valueKey] = self::_cleanup($valueValue);
                    }
                }
                
            }
        }
        return isset(static::$_sessionStore[$keyName]) ? static::$_sessionStore[$keyName] : false;
    }
   
}