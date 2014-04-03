<?php
// New User Form
if($editUser == TRUE) {
    if(isset($_POST["singleEditList"])) {
        $user = Model_User::get($_POST["singleEditList"]);
    } elseif(isset($_POST["userId_input"])) {
        $user = Model_User::get($_POST["userId_input"]);
    }
}
if($addUser == TRUE) {
    $user = new Model_User;
}
$userForm = "<div id=\"userForm_div\"><form id=\"userForm_form\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$currentId}") . "\">";
if($editUser == TRUE) {
    $userForm .= "<input type=\"hidden\" name=\"userId_input\" value=\"" . htmlspecialchars($user->id) . "\" />";
}
$userForm .= "<div id=\"userFormPersonal_div\"><label for=\"userName_input_" . $user->id . "\">Username</label><input type=\"text\" id=\"userName_input_" . $user->id . "\" name=\"userName_input_" . $user->id . "\" ";
if(isset($validator->errors["userName_input_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["userName_input_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["userName_input_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->userName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"forumName_input_" . $user->id . "\">Forum Name</label><input type=\"text\" id=\"forumName_input_" . $user->id . "\" name=\"forumName_input_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["forumName_input_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["forumName_input_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["forumName_input_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->forumName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"firstName_input_" . $user->id . "\">First Name</label><input type=\"text\" id=\"firstName_input_" . $user->id . "\" name=\"firstName_input_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["firstName_input_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["firstName_input_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["firstName_input_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->firstName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"lastName_input_" . $user->id . "\">Last Name</label><input type=\"text\" id=\"lastName_input_" . $user->id . "\" name=\"lastName_input_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["lastName_input"])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["lastName_input_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["lastName_input_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->lastName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"rank_select_" . $user->id . "\">Rank</label>";
$userForm .= View_Rank::selector($user);
$userForm .= "<br />";
$userForm .= "<label for=\"active_input_" . $user->id . "\" class=\"check\">Active</label><input type=\"checkbox\" id=\"active_input_" . $user->id . "\" name=\"active_input_" . $user->id . "\" ";
if($editUser == TRUE) {
    if(isset($_POST["active_input_" . $user->id])) {
        if($_POST["active_input_" . $user->id] == TRUE) {
            $userForm .=  "checked=\"checked\" ";
        }
    }
    if($user->active == TRUE) {
        $userForm .=  "checked=\"checked\" ";
    }
}
$userForm .= "/><br />";
$userForm .= "<label for=\"password_input_" . $user->id . "\">Password</label><input type=\"password\" id=\"password_input_" . $user->id . "\" name=\"password_input_" . $user->id . "\" ";
if(isset($validator->errors["password_input_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"confPassword_input_" . $user->id . "\">Confirm Password</label><input type=\"password\" id=\"confPassword_input_" . $user->id . "\" name=\"confPassword_input_" . $user->id . "\"  ";
if(isset($validator->errors["confPassword_input_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"changePw_input_" . $user->id . "\" class=\"check\">Change password on next login</label><input type=\"checkbox\" id=\"changePw_input_" . $user->id . "\" name=\"changePw_input_" . $user->id . "\" /></div><hr />";
$userForm .= "<div id=\"userFormSchedule_div\"><table id=\"userFormSchedule_table\">";
$userForm .= "<thead><tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr></thead><tbody>";
for($weekday = 2; $weekday <= 6; $weekday++) {
    if($editUser == TRUE) {
        $userSched = $user->get_sch($weekday);
        count($userSched) != 1 ? $userSched = new Model_Schedule : NULL;
    }
    $userForm .= "<tr><td>{$weekday}</td><td><input type=\"text\" name=\"{$weekday}Begin_input\" ";
    if(isset($validator->errors["{$weekday}Begin_input"])) {
        $userForm .= "class=\"error\" ";
    }
    if($editUser == TRUE) {
        if(isset($_POST["{$weekday}Begin_input"])) {
            $userForm .= "value=\"" . Controller_Time::format($_POST["{$weekday}Begin_input"], "html") . "\" ";
        }
        $userForm .= "value=\"" . Controller_Time::format($userSched->get_starttime(), "html") . "\" ";
    }
    $userForm .= "/></td><td><input type=\"text\" name=\"{$weekday}End_input\" ";
    if(isset($validator->errors["{$weekday}End_input"])) {
        $userForm .= "class=\"error\" ";
    }
    if($editUser == TRUE) {
        if(isset($_POST["{$weekday}End_input"])) {
            $userForm .= "value=\"" . Controller_Time::format($_POST["{$weekday}End_input"], "html") . "\" ";
        }
        $userForm .= "value=\"" . Controller_Time::format($userSched->get_endtime(), "html") . "\" ";
    }
    $userForm .= "/></td></tr>";
}
$userForm .= "</tbody></table></div><hr />";
if($addUser == TRUE) {
    $userForm .= "<input type=\"submit\" value=\"Add User\" name=\"submitForm\" />";
} elseif($editUser == TRUE) {
    $userForm .= "<input type=\"submit\" value=\"Submit User\" name=\"submitForm\" />";
}
$userForm .= "<input type=\"reset\" value=\"Reset\" />";
$userForm .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelSubmitForm\" />";
$userForm .= "</form></div>";