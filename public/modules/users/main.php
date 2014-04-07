<?php
if(isset($_POST["cancelEditList"])) {
    $_POST = "";
}
if(isset($_POST["editList"])) {
    if(count($_POST) > 1) {
        $editList = TRUE;
        $addUser = FALSE;
        $editUser = FALSE;
    } else {
        $editList = FALSE;
        $addUser = FALSE;
        $editUser = FALSE;
    }
} elseif(isset($_POST["addList"])) {
    $addUser = TRUE;
    $editUser = FALSE;
    $editList = FALSE;
} elseif(isset($_POST["singleEditList"])) {
    $editUser = TRUE;
    $addUser = FALSE;
    $editList = FALSE;
} else {
    $addUser = FALSE;
    $editUser = FALSE;
    $editList = FALSE;
}
//Form Validation
if((isset($_POST["submitList"])) || (isset($_POST["submitForm"]))) {
    $checkReq_array = array();
    $checkLen_array = array();
    $checkNum_array = array();
    $checkUniq_array = array();
    $checkTime_array = array();
    $checkTimeDiff_array = array();
    $checkSame_array = array();
    $checkPw_array = array();
    foreach($_POST as $valField => $val) {
        $valFieldString_array = preg_split("/([A-Z][a-z]+)|_/", $valField, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        if(count($valFieldString_array) > 1){
            if(strtolower($valFieldString_array[1]) == "name") {
                if(strtolower($valFieldString_array[0]) != "forum") {
                    $checkReq_array[] = $valField;
                    $checkLen_array[$valField] = "1-32";
                }
                if($valFieldString_array[0] == "user") {
                    $checkUniq_array[] = $valField;
                }
            }
            if(($valFieldString_array[0] == "rank")) {
                $checkNum_array[] = $valField;
                $checkReq_array[] = $valField;
            }
            if((strtolower($valFieldString_array[1]) == "begin") || (strtolower($valFieldString_array[1]) == "end")) {
                $checkReq_array[] = $valField;
                $checkTime_array[] = $valField;
                if(strtolower($valFieldString_array[1]) == "begin") {
                    $checkTimeDiff_array[$valField] = $valFieldString_array[0] . "End" . $valFieldString_array[2];
                }
            }
            if(strtolower($valFieldString_array[0]) == "password") {
                $checkSame_array[$valField] = "conf" . ucfirst($valField);
                isset($valFieldString_array[2]) ? $checkSame_array[$valField] .= $valFieldString_array[2] : NULL;
                if(!empty($_POST[$valField])) {
                    $checkPw_array[] = $valField;
                }
            }
        }
    }
    $validator = new Controller_Validator;
    $validator->unique($checkUniq_array);
    $validator->required($checkReq_array);
    $validator->length($checkLen_array);
    $validator->numeric($checkNum_array);
    $validator->time($checkTime_array);
    $validator->timediff($checkTimeDiff_array);
    $validator->password($checkPw_array);
    $validator->compare($checkSame_array);
}
//Form Processing
if(isset($_POST["submitList"])) {
    // This sorts all POST-vars by user id
    foreach($_POST as $varName => $postValue) {
        $string_array = explode("_", $varName);
        if(count($string_array) > 1){
            if(!($string_array[0] == "filter")) {
                $string_id = $string_array[1];
            }
            if(isset($string_id)) {
                if(!isset($query_array_{$string_id})) {
                    $query_array_{$string_id} = array();
                }
                $field = $string_array[0];
                $postValue = trim(call_user_func(DB_CLASS . "::queryPrep", $postValue));
                $query_array_{$string_id}[$field] = $postValue;
                $query_array[$string_id] = $query_array_{$string_id};
            }
        }
    }
    // This adds every field in an id (row) into a Model_User object and updates the database
    foreach($query_array as $array_id => $user_array) {
        $user = Model_User::get($array_id);
        $i = 1;
        $changefields = "";
        if(!isset($user_array["activeCheck"])) {
            $user_array["activeCheck"] = "off";
        }
        foreach($user_array as $field => $postValue) {
            $field = call_user_func(DB_CLASS . "::queryPrep", $field);
            $postValue = call_user_func(DB_CLASS . "::queryPrep", $postValue);
            if($field == "activeCheck") {
                $field = "active";
                if($postValue == "on") {
                    $postValue = 1;
                } elseif($postValue == "off") {
                    $postValue = 0;
                }
            }
            if($field == "rankSelect") {
                $field = "rank";
            }
            $user->$field = $postValue;
        }
        if(empty($validator->errors)) {
            $result = $user->save();
        } else {
            $editList = TRUE;
        }
    }
}
if(isset($_POST["submitForm"])) {
    if(empty($validator->errors)) {
        //Password Hashing
        if(!empty($_POST["password"])) {
            $hashPw = Model_User::pwEncrypt($_POST["password"]);
        }
        if(($_POST["submitForm"] == "Submit User") || ($_POST["submitForm"] == "Add User")) {
            isset($_POST["userId"]) ? $userId = $_POST["userId"] : $userId = NULL;
            !is_null($userId) ? $user = Model_User::get($userId) : $user = new Model_User;
            if(isset($_POST["active_" . $userId])) {
                $active = 1;
            } else {
                $active = 0;
            }
            isset($active)                              ? $user->active = $active : NULL;
            isset($employeeNr)                          ? $user->employeeNr = $employeeNr : NULL;
            isset($_POST["userName_" . $userId])  ? $user->userName = $_POST["userName_" . $userId] : NULL;
            isset($_POST["forumName_" . $userId]) ? $user->forumName = $_POST["forumName_" . $userId] : NULL;
            isset($_POST["firstName_" . $userId]) ? $user->firstName = $_POST["firstName_" . $userId] : NULL;
            isset($_POST["lastName_" . $userId])  ? $user->lastName = $_POST["lastName_" . $userId] : NULL;
            isset($_POST["rankSelect_" . $userId])      ? $user->rank = $_POST["rankSelect_" . $userId] : NULL;
            isset($hashPw)                              ? $user->passwordhash = $hashPw : NULL;
            $update_success = $user->save();
            for($weekday = 0; $weekday <= 6; $weekday++) {
                $schedule = $user->getSchedule($weekday);
                if(!is_object($schedule)) {
                    $schedule = new Model_Schedule;
                    $schedule->userId = $user->id;
                    $schedule->weekdayId = $weekday;
                }
                isset($_POST["{$weekday}BeginTime"])  ? $schedule->setStarttime(call_user_func(DB_CLASS . "::queryPrep", $_POST["{$weekday}BeginTime"])) : NULL;
                isset($_POST["{$weekday}EndTime"])    ? $schedule->setEndtime(call_user_func(DB_CLASS . "::queryPrep", $_POST["{$weekday}EndTime"])) : NULL;
                is_null($schedule->id) ? NULL : $insert_success = $schedule->save();
            }
        }
    } else {
        if($_POST["submitForm"] == "Add User") {
            $editUser = TRUE;
            $addUser = TRUE;
        } elseif($_POST["submitForm"] == "Submit User") {
            $editUser = TRUE;
        }
    }
}
// If validation fails:
if(isset($_POST["submitList"])) {
    if(isset($validator->errors) && !empty($validator->errors)) {
        $errorId_array = array();
        foreach($validator->errors as $errorField => $errorName) {
            $errorString_array = explode("_", $errorField);
            $errorId = $errorString_array[2];
            $errorId_array[$errorId] = TRUE;
        }
    }
}
$users_array = Model_User::get("all");
if(($addUser == TRUE) || ($editUser == TRUE)) {
    include("form.php");
    $output = $userForm;
} else {
    include("list.php");
    $output = $userList;
}
echo $output;