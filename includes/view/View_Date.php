<?php

/**
 * View_Date
 * Requires the classes Controller_Date and Controller_Url
 */
Class View_Date
{
    private $_date = array();
    
    public function __construct() {
        $dateArray = Controller_Date::get_selected();
        $this->_date["selectedYear"] = isset($dateArray["y"]) ? $dateArray["y"] : date("Y");
        $this->_date["selectedMonth"] = isset($dateArray["m"]) ? $dateArray["m"] : date("n");
        $this->_date["selectedDay"] = isset($dateArray["d"]) ? $dateArray["d"] : date("j");
    }
    
    /**
     * selector
     * Returns an HTML-ready string with a calendar;
     * @param string $type - defines the return type for the selector
     * @return string - HTML-ready calendar string
     * 
     * Required CSS:
     * #calendar_div        { text-align: center; }        
     * #calendar_div a      { display: block; }
     * #calPrev_div         { float: left; }
     * #calNext_div         { float: right; }
     * .day                 { float: left; width: 2em; }
     * .weekrow             { clear: both; }
     */
    public function selector($type = "GET"){
        if($this->_date["selectedMonth"] == 1) {
                $prevMonth = 12;
                $prevYear = $this->_date["selectedYear"] - 1;
        } else {
            $prevMonth = $this->_date["selectedMonth"] - 1;
            $prevYear = $this->_date["selectedYear"];
        }
        if($this->_date["selectedMonth"] == 12) {
                $nextMonth = 1;
                $nextYear = $this->_date["selectedYear"] + 1;
        } else {
            $nextMonth = $this->_date["selectedMonth"] + 1;
            $nextYear = $this->_date["selectedYear"];
        }
        $firstDay = jddayofweek(cal_to_jd(CAL_GREGORIAN, $this->_date["selectedMonth"], 1, $this->_date["selectedYear"]));
        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $this->_date["selectedMonth"], $this->_date["selectedYear"]);
        $prevNumberOfDays = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
        $numberOfWeeks = ceil(($numberOfDays + $firstDay)/7);
        $daysLastMonthFirstWeek = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear)-$firstDay+1;
        $remainingLastMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear)-$daysLastMonthFirstWeek;
        $daysNextMonthLastWeek = cal_days_in_month(CAL_GREGORIAN, $nextMonth, $this->_date["selectedYear"]);
        $output = "<div id=\"calendar_div\"><div id=\"month_select\">";
        $prevMonthNav = Controller_Date::to_get($prevYear, $prevMonth);
        $navLinks = "<div id=\"calPrev_div\"><a href=\"" . htmlspecialchars($prevMonthNav) . "\">Prev</a></div>";
        $nextMonthNav = Controller_Date::to_get($nextYear, $nextMonth);
        $navLinks .= "<div id=\"calNext_div\"><a href=\"" . htmlspecialchars($nextMonthNav) . "\">Next</a></div>";
        $navLinks .= "<div id=\"calCur_div\">" . htmlspecialchars(date("j F Y",strtotime($this->_date["selectedDay"] . "-" . $this->_date["selectedMonth"] . "-" . $this->_date["selectedYear"]))) . "</div>";
        $output .= $navLinks . "</div>"; 
        $dayNo = $daysLastMonthFirstWeek;
        for($weekNo = 1; $weekNo <= $numberOfWeeks; $weekNo++) {
            $output .= "<div class=\"weekrow\" >";
            for($wDayNo = 1; $wDayNo <= 7; $wDayNo++) {
                $tdOutput1 = "<div class=\"day ";
                if(1 < $wDayNo && $wDayNo < 7) {
                    $tdOutput1 .= "weekDay";
                }
                if($dayNo <= $prevNumberOfDays) {
                    $urlMonth = $prevMonth;
                    $urlYear = $prevYear;
                    $tdOutput1 .= " prevMonth\">";
                    $urlDay = $tdOutput2 = $dayNo;
                } elseif (($dayNo - $prevNumberOfDays) <= $numberOfDays) {
                    $urlMonth = $this->_date["selectedMonth"];
                    $urlYear = $this->_date["selectedYear"];
                    if ($dayNo - $prevNumberOfDays == $this->_date["selectedDay"]) {
                        $tdOutput1 .= " selectedDay";
                    }
                    $tdOutput1 .= "\">";
                    $urlDay = $tdOutput2 = ($dayNo - $prevNumberOfDays);
                } else {
                    $urlMonth = $nextMonth;
                    $urlYear = $nextYear;
                    $tdOutput1 .= " nextMonth\">";
                    $urlDay = $tdOutput2 = ($dayNo - $prevNumberOfDays - $numberOfDays);
                }
                $tdOutput3 = "</a></div>";
                if($type == "GET") {
                    $dateUrl = Controller_Date::to_get($urlYear, $urlMonth, $urlDay);
                } elseif($type == "SESSION") {
                    $url = new Controller_Url;
                    $dateUrl = $url->get_string();
                }
                $output .= $tdOutput1 . "<a href=\"" . htmlspecialchars($dateUrl) . "\" >" . $tdOutput2 . $tdOutput3;
                $dayNo++;
            }
            $output .= "</div>";
        }
        $output .= "</div>";
        return $output;	
    }
}