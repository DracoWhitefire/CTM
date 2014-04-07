<?php
// New User Form
if($editUser == TRUE) {
    if(isset($_POST["singleEditList"])) {
        $user = Model_User::get($_POST["singleEditList"]);
    } elseif(isset($_POST["userId"])) {
        $user = Model_User::get($_POST["userId"]);
    }
}
if($addUser == TRUE) {
    $user = new Model_User;
}
$userForm = "<div id=\"userForm\"><form id=\"userForm\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$currentId}") . "\">";
if($editUser == TRUE) {
    $userForm .= "<input type=\"hidden\" name=\"userId\" value=\"" . htmlspecialchars($user->id) . "\" />";
}
$userForm .= "<div id=\"userFormPersonal\"><label for=\"userName_" . $user->id . "\">Username</label><input type=\"text\" id=\"userName_" . $user->id . "\" name=\"userName_" . $user->id . "\" ";
if(isset($validator->errors["userName_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["userName_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["userName_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->userName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"forumName_" . $user->id . "\">Forum Name</label><input type=\"text\" id=\"forumName_" . $user->id . "\" name=\"forumName_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["forumName_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["forumName_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["forumName_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->forumName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"firstName_" . $user->id . "\">First Name</label><input type=\"text\" id=\"firstName_" . $user->id . "\" name=\"firstName_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["firstName_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["firstName_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["firstName_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->firstName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"lastName_" . $user->id . "\">Last Name</label><input type=\"text\" id=\"lastName_" . $user->id . "\" name=\"lastName_" . $user->id . "\" autocomplete=\"off\" ";
if(isset($validator->errors["lastName"])) {
    $userForm .= "class=\"error\" ";
}
if($editUser == TRUE) {
    $userForm .= "value=\"";
    if(isset($_POST["lastName_" . $user->id])) {
        $userForm .= htmlspecialchars($_POST["lastName_" . $user->id]);
    } else {
        $userForm .= htmlspecialchars($user->lastName);
    }
    $userForm .= "\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"rankselect_" . $user->id . "\">Rank</label>";
$userForm .= View_Rank::selector($user);
$userForm .= "<br />";
$userForm .= "<label for=\"active_" . $user->id . "\" class=\"check\">Active</label><input type=\"checkbox\" id=\"active_" . $user->id . "\" name=\"active_" . $user->id . "\" ";
if($editUser == TRUE) {
    if(isset($_POST["active_" . $user->id])) {
        if($_POST["active_" . $user->id] == TRUE) {
            $userForm .=  "checked=\"checked\" ";
        }
    }
    if($user->active == TRUE) {
        $userForm .=  "checked=\"checked\" ";
    }
}
$userForm .= "/><br />";
$userForm .= "<label for=\"password_" . $user->id . "\">Password</label><input type=\"password\" id=\"password_" . $user->id . "\" name=\"password_" . $user->id . "\" ";
if(isset($validator->errors["password_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"confPassword_" . $user->id . "\">Confirm Password</label><input type=\"password\" id=\"confPassword_" . $user->id . "\" name=\"confPassword_" . $user->id . "\"  ";
if(isset($validator->errors["confPassword_" . $user->id])) {
    $userForm .= "class=\"error\" ";
}
$userForm .= "/><br />";
$userForm .= "<label for=\"changePw_" . $user->id . "\" class=\"check\">Change password on next login</label><input type=\"checkbox\" id=\"changePw_" . $user->id . "\" name=\"changePw_" . $user->id . "\" /></div><hr />";
$userForm .= "<div id=\"userFormSchedule\"><table id=\"userFormSchedule\">";
$userForm .= "<thead><tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr></thead><tbody>";
for($weekday = 2; $weekday <= 6; $weekday++) {
    if($editUser == TRUE) {
        $userSched = $user->getSchedule($weekday);
        count($userSched) != 1 ? $userSched = new Model_Schedule : NULL;
    }
    $userForm .= "<tr><td>{$weekday}</td><td><input type=\"text\" name=\"{$weekday}BeginTime\" ";
    if(isset($validator->errors["{$weekday}BeginTime"])) {
        $userForm .= "class=\"error\" ";
    }
    if($editUser == TRUE) {
        if(isset($_POST["{$weekday}BeginTime"])) {
            $userForm .= "value=\"" . Controller_Time::format($_POST["{$weekday}BeginTime"], "html") . "\" ";
        }
        $userForm .= "value=\"" . Controller_Time::format($userSched->getStarttime(), "html") . "\" ";
    }
    $userForm .= "/></td><td><input type=\"text\" name=\"{$weekday}EndTime\" ";
    if(isset($validator->errors["{$weekday}EndTime"])) {
        $userForm .= "class=\"error\" ";
    }
    if($editUser == TRUE) {
        if(isset($_POST["{$weekday}EndTime"])) {
            $userForm .= "value=\"" . Controller_Time::format($_POST["{$weekday}EndTime"], "html") . "\" ";
        }
        $userForm .= "value=\"" . Controller_Time::format($userSched->getEndtime(), "html") . "\" ";
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