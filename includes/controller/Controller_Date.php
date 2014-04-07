<?php

/**
 * Controller_Date
 */
Class Controller_Date
{
    /**
     * Gets selected date from GET request
     * @return array $getDateArray - array with selected date
     */
    private static function _fromGet() {
        $getDateArray = array();
        if(Controller_Request::get("y")) {
            $getDateArray["y"] = date("Y");
        } else {
            $getDateArray["y"] = urldecode(Controller_Request::get("y"));
        }
        if(Controller_Request::get("m")) {
            $getDateArray["m"] = date("m");
        } else {
            if((1 <= Controller_Request::get("m")) && (Controller_Request::get("m") <= 12)) {
                $getDateArray["m"] = urldecode(Controller_Request::get("m"));
            } else {
                $getDateArray["m"] = date("m");
            }
        }
        if(Controller_Request::get("d")) {
            if((1<=Controller_Request::get("d"))&&(Controller_Request::get("d")<=cal_days_in_month(CAL_GREGORIAN, $getDateArray["m"], $getDateArray["y"]))) {
                $getDateArray["d"] = urldecode(Controller_Request::get("d"));
            } else {
                $getDateArray["d"] = date("d");
            }
        } else {
            $getDateArray["d"] = date("d");				
        }
        return (array) $getDateArray;
    }

    /**
     * Gets selected date from POST request 
     * @return array $postDateArray - array with selected date
     */
    private static function _fromPost() {
        $postDateArray["d"] = Controller_Request::post("d");
        $postDateArray["m"] = Controller_Request::post("m");
        $postDateArray["y"] = Controller_Request::post("y");
        return (array) $postDateArray;
    }
    
    /**
     * Gets selected date from SESSION 
     * @return array $sessionDateArray - array with selected date
     */
    private static function _fromSession() {
        $sessionDateArray = Controller_Request::session("selectedDate");
        return (array) $sessionDateArray;
    }
    
    /**
     * Creates a url for GET request anchors
     * @param string $year
     * @param string $month
     * @param string $day
     * @return string $url - URL for GET request
     */
    public static function toGet($year = "", $month = "", $day = "") {
        $urlQueriesArray = array();
        $urlQueriesArray["y"] =    !empty($year) 
                                    ? urlencode($year) 
                                    :  (isset($urlQueriesArray["y"]) 
                                       ? $urlQueriesArray["y"]
                                       : date("Y"));
        $urlQueriesArray["m"] =    !empty($month) 
                                    ? urlencode($month) 
                                    :  (isset($urlQueriesArray["m"]) 
                                       ? $urlQueriesArray["m"]
                                       : date("n"));
        $urlQueriesArray["d"] =    !empty($day) 
                                    ? urlencode($day) 
                                    :  (isset($urlQueriesArray["d"]) 
                                       ? $urlQueriesArray["d"]
                                       : date("j"));
        $url = new Controller_Url();
        $url->add($urlQueriesArray);
        return (string) $url;
    }
    
    /**
     * Gets selected date
     * @return array $dateArray - array with selected date
     */
    public static function getSelected() {
        $dateArray = array();
        if(Controller_Request::post("d") || Controller_Request::post("m") || Controller_Request::post("y")) {
            $dateArray = self::_fromPost();
        } elseif(Controller_Request::session("selectedDate")) {
            $dateArray = self::_fromSession();
        } elseif(Controller_Request::get("d") || Controller_Request::get("m") || Controller_Request::get("y")) {
            $dateArray = self::_fromGet();
        } else {
            $dateArray["y"] = date("Y");
            $dateArray["m"] = date("n");
            $dateArray["d"] = date("j");
        }
        return (array) $dateArray;        
    }
}