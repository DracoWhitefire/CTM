<?php
/**
 * Controller_Request
 * Handles and filters all GET, POST, and SESSION requests
 */
class Controller_Request
{
    private static $_getStore = [];
    private static $_postStore = [];
    private static $_sessionStore = [];
    
    /**
     * _cleanup
     * prepares string for output/database
     * @param string $string
     * @return string
     */
    private static function _cleanup($string) {
        return call_user_func(DB_CLASS . "::query_prep", $string);
    }
    
    /**
     * filter
     * Handler method to filter requests;
     * @param string $requestType - GET, POST or SESSION
     * @param string $keyName - the specific request key
     * @return string|bool - the filtered value for request key or bool false if not available
     */
    public static function filter($requestType, $keyName) {
        $attribName = "_" . strtolower($requestType) . "Store";
        $requestName = "_" . $requestType;
//        $foo = "bar";
//        $requestName = "foo";
        print_r($_GET);                 // this works
        print_r(${$requestName});       // this does not
        echo ${$requestName};
        if(!isset(self::${$attribName})) {
            foreach(${$requestName} as $key => $value) {
                static::${$attribName}[$key] = self::_cleanup($value);
            }
        }
        return isset(self::${$attribName}[$keyName]) ? self::${$attribName}[$keyName] : false;
    }
}