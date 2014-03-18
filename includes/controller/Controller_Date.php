<?php

/**
 * Description of Controller_Date
 */
Class Controller_Date
{
    public static function from_get() {
        if(isset($_GET["d"]) || isset($_GET["m"]) || isset($_GET["y"])) {
            
        }
    }
    
    public static function from_post() {
        
    }
    
    public static function to_get($year = "", $month = "", $day = "") {
        $urlQueries_array = array();
        $urlQueries = explode("&", $_SERVER["QUERY_STRING"]);
        foreach($urlQueries as $urlQuery) {
            $query_array = explode("=", $urlQuery);
            $urlQueries_array[$query_array[0]] = $query_array[1];
        }
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
        $url = $_SERVER["PHP_SELF"] . "?" . http_build_query($urlQueries_array);
        return $url;
        
    }
    
    public static function to_post() {
        
    }
    
    public static function get_selected() {
        $dateArray = array();
        if(!isset($_GET["y"])) {
            $dateArray["y"] = date("Y");
        } else {
            $dateArray["y"] = urldecode($_GET["y"]);
        }
        if(!isset($_GET["m"])) {
            $dateArray["m"] = date("m");
        } else {
            if((1 <= $_GET["m"]) && ($_GET["m"] <= 12)) {
                $dateArray["m"] = urldecode($_GET["m"]);
            } else {
                $dateArray["m"] = date("m");
            }
        }
        if(isset($_GET["d"])) {
            if((1<=$_GET["d"])&&($_GET["d"]<=cal_days_in_month(CAL_GREGORIAN, $dateArray["m"], $dateArray["y"]))) {
                $dateArray["d"] = urldecode($_GET["d"]);
            } else {
                $dateArray["d"] = date("d");
            }
        } else {
            $dateArray["d"] = date("d");				
        }
        return $dateArray;        
    }
}

