<?php

/**
 * Description of Controller_Date
 */
Class Controller_Date
{
    /**
     * _from_get
     * Gets selected date from GET request
     * @return array $getDateArray - array with selected date
     */
    private static function _from_get() {
        $getDateArray = array();
        if(!isset($_GET["y"])) {
            $getDateArray["y"] = date("Y");
        } else {
            $getDateArray["y"] = urldecode($_GET["y"]);
        }
        if(!isset($_GET["m"])) {
            $getDateArray["m"] = date("m");
        } else {
            if((1 <= $_GET["m"]) && ($_GET["m"] <= 12)) {
                $getDateArray["m"] = urldecode($_GET["m"]);
            } else {
                $getDateArray["m"] = date("m");
            }
        }
        if(isset($_GET["d"])) {
            if((1<=$_GET["d"])&&($_GET["d"]<=cal_days_in_month(CAL_GREGORIAN, $getDateArray["m"], $getDateArray["y"]))) {
                $getDateArray["d"] = urldecode($_GET["d"]);
            } else {
                $getDateArray["d"] = date("d");
            }
        } else {
            $getDateArray["d"] = date("d");				
        }
        return (array) $getDateArray;
    }

    /**
     * _from_post
     * Gets selected date from POST request 
     * @return array $postDateArray - array with selected date
     */
    private static function _from_post() {
        $postDateArray = $_POST["date"];
        return (array) $postDateArray;
    }
    
    /**
     * _from_session
     * Gets selected date from SESSION 
     * @return array $sessionDateArray - array with selected date
     */
    private static function _from_session() {
        $sessionDateArray = $_SESSION["date"];
        return (array) $sessionDateArray;
    }
    
    /**
     * to_get
     * Creates a url for GET request anchors
     * @param string $year
     * @param string $month
     * @param string $day
     * @return string $url - URL for GET request
     */
    public static function to_get($year = "", $month = "", $day = "") {
        $urlQueries_array = array();
//        $urlQueries = explode("&", $_SERVER["QUERY_STRING"]);
//        foreach($urlQueries as $urlQuery) {
//            $query_array = explode("=", $urlQuery);
//            $urlQueries_array[$query_array[0]] = $query_array[1];
//        }
        $urlQueries_array["y"] =    !empty($year) 
                                    ? urlencode($year) 
                                    :  (isset($urlQueries_array["y"]) 
                                       ? $urlQueries_array["y"]
                                       : date("Y"));
        $urlQueries_array["m"] =    !empty($month) 
                                    ? urlencode($month) 
                                    :  (isset($urlQueries_array["m"]) 
                                       ? $urlQueries_array["m"]
                                       : date("n"));
        $urlQueries_array["d"] =    !empty($day) 
                                    ? urlencode($day) 
                                    :  (isset($urlQueries_array["d"]) 
                                       ? $urlQueries_array["d"]
                                       : date("j"));
//        $url = $_SERVER["PHP_SELF"] . "?" . http_build_query($urlQueries_array);
        $url = new Controller_Url();
        $url->add($urlQueries_array);
        //print_r($url->get_array());
        return (string) $url;
    }
    
    public static function to_post() {
        
    }
    
    /**
     * get_selected
     * Gets selected date
     * @return array $dateArray - array with selected date
     */
    public static function get_selected() {
        $dateArray = array();
        if(isset($_POST["date"])) {
            $dateArray = self::_from_post();
        } elseif(isset($_SESSION["date"])) {
            $dateArray = self::_from_session();
        } elseif(isset($_GET["d"]) || isset($_GET["m"]) || isset($_GET["y"])) {
            $dateArray = self::_from_get();
        }
        return (array) $dateArray;        
    }
}

