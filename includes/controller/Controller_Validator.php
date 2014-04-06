<?php
/**
 * Controller_Validator
 */
class Controller_Validator
{
    public $errors;
    
    public function required(array $val_req_array) {
        foreach($val_req_array as $fieldName) {
            if(!isset($_POST[$fieldName]) || ((empty($_POST[$fieldName])) && !(is_numeric($_POST[$fieldName])))) {
                $this->errors[$fieldName] = "error_req";
            }
        }
    }
    
    public function length(array $val_len_array) {
        foreach($val_len_array as $fieldName => $minmax) {
            $string_array = explode("-", $minmax);
            $min = $string_array["0"];
            $max = $string_array["1"];
            if((strlen(trim($_POST[$fieldName])) < $min) || (strlen(trim($_POST[$fieldName])) > $max)) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_len";
                }
            }
        }
    }
    
    public function numeric(array $checkNum_array) {
        foreach($checkNum_array as $fieldName) {
            if(!is_numeric($_POST[$fieldName])) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_num";
                }
            }
        }
    }
    
    public function unique(array $val_uniq_array) {
        $db = call_user_func(DB_CLASS . "::getInstance");
        $user_query  = "SELECT `user_name`, `id` ";
        $user_query .= "FROM `users` ";
        $user_set = $db->query($user_query);
        while($user_row = $db->fetchAssoc($user_set)) {
            $id = (int) $user_row["id"];
            $user_array[$id] = $user_row["user_name"];
        }
        mysqli_free_result($user_set);
        foreach($val_uniq_array as $fieldName) {
            $fieldName_array = preg_split("/([A-Z][a-z]+)|_/", $fieldName, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
            foreach($user_array as $id => $name) {
                if($name == $_POST[$fieldName]) {
                    if(is_numeric($fieldName_array[2])) {
                        if($fieldName_array[2] != $id) {
                            if(!isset($this->errors[$fieldName])) {
                                $this->errors[$fieldName] = "error_unique";
                            }
                            break 1;
                        }
                    } elseif($fieldName_array[2] == "input") {
                        if(isset($_POST["userId_input"])) {
                            if($_POST["userId_input"] != $id) {
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
    
    public function time(array $val_time_array) {
        foreach($val_time_array as $fieldName) {
            if(preg_match("/^(0(?=\d)|1(?=\d)|2(?=[0-3]))?\d:[0-5]\d(:[0-5]\d)?$/", $_POST[$fieldName]) == 0) {
                if(!isset($this->errors[$fieldName])) {
                    $this->errors[$fieldName] = "error_time";
                }
            }
        }
    }
    
    public function timediff(array $val_timediff_array) {
        //requires the function Controller_Time::format()
        foreach($val_timediff_array as $startTime_fieldname => $endTime_fieldname) {
            $startTime = (float) strtotime(Controller_Time::format($_POST[$startTime_fieldname], "db"));
            $endTime = (float) strtotime(Controller_Time::format($_POST[$endTime_fieldname], "db"));
            if(($endTime - $startTime) < 0) {
                if(!isset($this->errors[$startTime_fieldname]) && (!isset($this->errors[$endTime_fieldname]))) {
                    $this->errors[$startTime_fieldname] = "error_timediff";
                    $this->errors[$endTime_fieldname] = "error_timediff";
                }
            }
        }
    }
    
    public function password(array $val_pw_array) {
        foreach($val_pw_array as $pw_field) {
            $success = preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s]).{8,20}$/", trim($_POST[$pw_field]));
            if(!$success) {
                if(!isset($this->errors[$pw_field])) {
                    $this->errors[$pw_field] = "error_pw";
                }
            }
        }
    }
    
    public function compare(array $val_compare_array) {
        foreach($val_compare_array as $firstField => $secondField) {
            if($_POST[$firstField] !== $_POST[$secondField]) {
                if(!isset($this->errors[$firstField]) && !isset($this->errors[$secondField])) {
                    $this->errors[$firstField] = "error_compare";
                    $this->errors[$secondField] = "error_compare";
                }
            }
        }
    }
}