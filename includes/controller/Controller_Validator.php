<?php
/**
 * Controller_Validator
 */
class Controller_Validator
{
    public $errors;
    
    public function required(array $valReqArray) {
        foreach($valReqArray as $fieldName) {
            if(!Controller_Request::post($fieldName) || ((empty(Controller_Request::post($fieldName))) && !(is_numeric(Controller_Request::post($fieldName))))) {
                $this->errors[$fieldName] = "error_req";
            }
        }
    }
    
    public function length(array $valLenArray) {
        foreach($valLenArray as $fieldName => $minmax) {
            $stringArray = explode("-", $minmax);
            $min = $stringArray["0"];
            $max = $stringArray["1"];
            if((strlen(Controller_Request::post($fieldName)) < $min) || (strlen(Controller_Request::post($fieldName)) > $max)) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_len";
                }
            }
        }
    }
    
    public function numeric(array $checkNumArray) {
        foreach($checkNumArray as $fieldName) {
            if(!is_numeric(Controller_Request::post($fieldName))) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_num";
                }
            }
        }
    }
    
    public function unique(array $valUniqArray) {
        $db = call_user_func(DB_CLASS . "::getInstance");
        $userQuery  = "SELECT `user_name`, `id` ";
        $userQuery .= "FROM `users` ";
        $userSet = $db->query($userQuery);
        while($userRow = $db->fetchAssoc($userSet)) {
            $id = (int) $userRow["id"];
            $userArray[$id] = $userRow["user_name"];
        }
        mysqli_free_result($userSet);
        foreach($valUniqArray as $fieldName) {
            $fieldNameArray = preg_split("/([A-Z][a-z]+)|_/", $fieldName, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
            foreach($userArray as $id => $name) {
                if($name == Controller_Request::post($fieldName)) {
                    if(is_numeric($fieldNameArray[2])) {
                        if($fieldNameArray[2] != $id) {
                            if(!isset($this->errors[$fieldName])) {
                                $this->errors[$fieldName] = "error_unique";
                            }
                            break 1;
                        }
                    } elseif($fieldNameArray[2] == "input") {
                        if(Controller_Request::post("userId")) {
                            if(Controller_Request::post("userId") != $id) {
                                if(!isset($this->errors[$fieldName])) {
                                    $this->errors[$fieldName] = "error_unique";
                                }
                                break 1;
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function time(array $valTimeArray) {
        foreach($valTimeArray as $fieldName) {
            if(preg_match("/^(0(?=\d)|1(?=\d)|2(?=[0-3]))?\d:[0-5]\d(:[0-5]\d)?$/", Controller_Request::post($fieldName)) == 0) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_time";
                }
            }
        }
    }
    
    public function timediff(array $valTimediffArray) {
        //requires the function Controller_Time::format()
        foreach($valTimediffArray as $startTimeFieldname => $endTimeFieldname) {
            $startTime = (float) strtotime(Controller_Time::format(Controller_Request::post($startTimeFieldname), "db"));
            $endTime = (float) strtotime(Controller_Time::format(Controller_Request::post($endTimeFieldname), "db"));
            if(($endTime - $startTime) < 0) {
                if(!isset($this->errors[$startTimeFieldname]) && (!isset($this->errors[$endTimeFieldname]))) {
                    $this->errors[$startTimeFieldname] = "error_timediff";
                    $this->errors[$endTimeFieldname] = "error_timediff";
                }
            }
        }
    }
    
    public function password(array $valPwArray) {
        foreach($valPwArray as $pwField) {
            $success = preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s]).{8,20}$/", Controller_Request::post($pwField));
            if(!$success) {
                if(!isset($this->errors[$pwField])) {
                    $this->errors[$pwField] = "error_pw";
                }
            }
        }
    }
    
    public function compare(array $valCompareArray) {
        foreach($valCompareArray as $firstField => $secondField) {
            if(Controller_Request::post($firstField) !== Controller_Request::post($secondField)) {
                if(!isset($this->errors[$firstField]) && !isset($this->errors[$secondField])) {
                    $this->errors[$firstField] = "error_compare";
                    $this->errors[$secondField] = "error_compare";
                }
            }
        }
    }
}