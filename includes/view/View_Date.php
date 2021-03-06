<?php

/**
 * View_Date
 * Requires the classes Controller_Date and Controller_Url
 */
Class View_Date
{
    private $_date = array();
    
    public function __construct() {
        $dateArray = Controller_Date::getSelected();
        $this->_date["selectedYear"] = isset($dateArray["y"]) ? $dateArray["y"] : date("Y");
        $this->_date["selectedMonth"] = isset($dateArray["m"]) ? $dateArray["m"] : date("n");
        $this->_date["selectedDay"] = isset($dateArray["d"]) ? $dateArray["d"] : date("j");
    }
    
    /**
     * Returns an HTML-ready string with a calendar;
     * @param string $type - defines the return type for the selector
     * @return string - HTML-ready calendar string
     * 
     * Required CSS:
     * #calendar        { text-align: center; }        
     * #calendar a      { display: block; }
     * #calPrev         { float: left; }
     * #calNext         { float: right; }
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
        $output  = "<div id=\"calendar\">";
        $output .= "<div id=\"monthSelect\">";
        $prevMonthNav = Controller_Date::toGet($prevYear, $prevMonth);
        $navLinks = "<div id=\"calPrev\"><a href=\"" . htmlspecialchars($prevMonthNav) . "\">Prev</a></div>";
        $nextMonthNav = Controller_Date::toGet($nextYear, $nextMonth);
        $navLinks .= "<div id=\"calNext\"><a href=\"" . htmlspecialchars($nextMonthNav) . "\">Next</a></div>";
        $navLinks .= "<div id=\"calCur\">" . htmlspecialchars(date("j F Y",strtotime($this->_date["selectedDay"] . "-" . $this->_date["selectedMonth"] . "-" . $this->_date["selectedYear"]))) . "</div>";
        $output .= $navLinks . "</div>"; 
        $output .= "<div id=\"daySelect\">";
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
                $dateUrl = Controller_Date::toGet($urlYear, $urlMonth, $urlDay);
                $output .= $tdOutput1 . "<a href=\"" . htmlspecialchars($dateUrl) . "\" >" . $tdOutput2 . $tdOutput3;
                $dayNo++;
            }
            $output .= "&thinsp;</div>";
        }
        $output .= "</div>";
        $output .= "</div>";
        if(file_exists("scripts/miniscript.js")) {
            $output .= "<script src=\"scripts/miniscript.js\"></script>";
        } else {
            $output .= "<script src=\"scripts/View_Date.js\"></script>";
        }
        return $output;	
    }
}