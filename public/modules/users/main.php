<?php
if(Controller_Request::post("cancelEditList")) {
    $_POST = "";
}
if(Controller_Request::post("editList")) {
    if(count($_POST) > 1) {
        $editList = TRUE;
        $addUser = FALSE;
        $editUser = FALSE;
    } else {
        $editList = FALSE;
        $addUser = FALSE;
        $editUser = FALSE;
    }
} elseif(Controller_Request::post("addList")) {
    $addUser = TRUE;
    $editUser = FALSE;
    $editList = FALSE;
} elseif(Controller_Request::post("singleEditList")) {
    $editUser = TRUE;
    $addUser = FALSE;
    $editList = FALSE;
} else {
    $addUser = FALSE;
    $editUser = FALSE;
    $editList = FALSE;
}
//Form Validation
if((Controller_Request::post("submitList")) || (Controller_Request::post("submitForm"))) {
    $checkReqArray = array();
    $checkLenArray = array();
    $checkNumArray = array();
    $checkUniqArray = array();
    $checkTimeArray = array();
    $checkTimeDiffArray = array();
    $checkSameArray = array();
    $checkPwArray = array();
    foreach($_POST as $valField => $val) {
        $valFieldStringArray = preg_split("/([A-Z][a-z]+)|_/", $valField, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        if(count($valFieldStringArray) > 1){
            if(strtolower($valFieldStringArray[1]) == "name") {
                if(strtolower($valFieldStringArray[0]) != "forum") {
                    $checkReqArray[] = $valField;
                    $checkLenArray[$valField] = "1-32";
                }
                if($valFieldStringArray[0] == "user") {
                    $checkUniqArray[] = $valField;
                }
            }
            if(($valFieldStringArray[0] == "rank")) {
                $checkNumArray[] = $valField;
                $checkReqArray[] = $valField;
            }
            if((strtolower($valFieldStringArray[1]) == "begin") || (strtolower($valFieldStringArray[1]) == "end")) {
                $checkReqArray[] = $valField;
                $checkTimeArray[] = $valField;
                if(strtolower($valFieldStringArray[1]) == "begin") {
                    $checkTimeDiffArray[$valField] = $valFieldStringArray[0] . "End" . $valFieldStringArray[2];
                }
            }
            if(strtolower($valFieldStringArray[0]) == "password") {
                $checkSameArray[$valField] = "conf" . ucfirst($valField);
                isset($valFieldStringArray[2]) ? $checkSameArray[$valField] .= $valFieldStringArray[2] : NULL;
                if(!empty(Controller_Request::post($valField))) {
                    $checkPwArray[] = $valField;
                }
            }
        }
    }
    $validator = new Controller_Validator;
    $validator->unique($checkUniqArray);
    $validator->required($checkReqArray);
    $validator->length($checkLenArray);
    $validator->numeric($checkNumArray);
    $validator->time($checkTimeArray);
    $validator->timediff($checkTimeDiffArray);
    $validator->password($checkPwArray);
    $validator->compare($checkSameArray);
}
//Form Processing
if(Controller_Request::post("submitList")) {
    // This sorts all POST-vars by user id
    foreach($_POST as $varName => $postValue) {
        $stringArray = explode("_", $varName);
        if(count($stringArray) > 1){
            if(!($stringArray[0] == "filter")) {
                $stringId = $stringArray[1];
            }
            if(isset($stringId)) {
                if(!isset($queryArray_{$stringId})) {
                    $queryArray_{$stringId} = array();
                }
                $field = $stringArray[0];
                $postValue = trim(call_user_func(DB_CLASS . "::queryPrep", $postValue));
                $queryArray_{$stringId}[$field] = $postValue;
                $queryArray[$stringId] = $queryArray_{$stringId};
            }
        }
    }
    // This adds every field in an id (row) into a Model_User object and updates the database
    foreach($queryArray as $array_id => $user_array) {
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
if(Controller_Request::post("submitForm")) {
    if(empty($validator->errors)) {
        //Password Hashing
        if(!empty(Controller_Request::post("password"))) {
            $hashPw = Model_User::pwEncrypt(Controller_Request::post("password"));
        }
        if((Controller_Request::post("submitForm") == "Submit User") || (Controller_Request::post("submitForm") == "Add User")) {
            Controller_Request::post("userId") ? $userId = Controller_Request::post("userId") : $userId = NULL;
            !is_null($userId) ? $user = Model_User::get($userId) : $user = new Model_User;
            if(Controller_Request::post("active_" . $userId)) {
                $active = 1;
            } else {
                $active = 0;
            }
            isset($active)                              ? $user->active = $active : NULL;
            isset($employeeNr)                          ? $user->employeeNr = $employeeNr : NULL;
            Controller_Request::post("userName_" . $userId)  ? $user->userName = Controller_Request::post("userName_" . $userId) : NULL;
            Controller_Request::post("forumName_" . $userId) ? $user->forumName = Controller_Request::post("forumName_" . $userId) : NULL;
            Controller_Request::post("firstName_" . $userId) ? $user->firstName = Controller_Request::post("firstName_" . $userId) : NULL;
            Controller_Request::post("lastName_" . $userId)  ? $user->lastName = Controller_Request::post("lastName_" . $userId) : NULL;
            Controller_Request::post("rankSelect_" . $userId)      ? $user->rank = Controller_Request::post("rankSelect_" . $userId) : NULL;
            isset($hashPw)                              ? $user->passwordhash = $hashPw : NULL;
            $update_success = $user->save();
            for($weekday = 0; $weekday <= 6; $weekday++) {
                $schedule = $user->getSchedule($weekday);
                if(!is_object($schedule)) {
                    $schedule = new Model_Schedule;
                    $schedule->userId = $user->id;
                    $schedule->weekdayId = $weekday;
                }
                Controller_Request::post("{$weekday}BeginTime")  ? $schedule->setStarttime(call_user_func(DB_CLASS . "::queryPrep", Controller_Request::post("{$weekday}BeginTime"))) : NULL;
                Controller_Request::post("{$weekday}EndTime")    ? $schedule->setEndtime(call_user_func(DB_CLASS . "::queryPrep", Controller_Request::post("{$weekday}EndTime"))) : NULL;
                is_null($schedule->id) ? NULL : $insert_success = $schedule->save();
            }
        }
    } else {
        if(Controller_Request::post("submitForm") == "Add User") {
            $editUser = TRUE;
            $addUser = TRUE;
        } elseif(Controller_Request::post("submitForm") == "Submit User") {
            $editUser = TRUE;
        }
    }
}
// If validation fails:
if(Controller_Request::post("submitList")) {
    if(isset($validator->errors) && !empty($validator->errors)) {
        $errorIdArray = array();
        foreach($validator->errors as $errorField => $errorName) {
            $errorStringArray = explode("_", $errorField);
            $errorId = $errorStringArray[2];
            $errorIdArray[$errorId] = TRUE;
        }
    }
}
$usersArray = Model_User::get("all");
if(($addUser == TRUE) || ($editUser == TRUE)) {
    include("form.php");
    $output = $userForm;
} else {
    include("list.php");
    $output = $userList;
}
echo $output;