<?php

/**
 * Controller_Url
 */
class Controller_Url {
    private $_string;
    private $_array;
    
    /**
     * Constructor;
     * @param string $type - The type of URL; defaults to url of requesting page
     * @return NULL
     */
    public function __construct($type = "request") {
        if($type == "request") {
            $this->_string = $_SERVER["REQUEST_URI"];
            $urlQueries = explode("&", $_SERVER["QUERY_STRING"]);
            foreach($urlQueries as $urlQuery) {
                $query_array = explode("=", $urlQuery);
                $this->_array[$query_array[0]] = $query_array[1];
            }
        }
    }
    
    /**
     * String conversion, will return content of string property
     * @return string $string
     */
    public function __toString() {
        return $this->_string;
    }
    
    /**
     * @return string $this->_string - the _string property
     */
    public function getString() {
        return $this->_string;
    }
    
    /**
     * @return array $this->_array - array of GET attributes
     */
    public function getArray() {
        return $this->_array;
    }
    
    /**
     * adds GET parameters to url
     * @param array $params
     * @return NULL
     */
    public function add(array $params) {
        $this->_array = array_merge($this->_array, $params);
        $this->_string = $_SERVER["PHP_SELF"] . "?" . http_build_query($this->_array);
    }
}
