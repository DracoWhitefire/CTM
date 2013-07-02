<?php
// New User Form
	if($editUser == TRUE) {
		if(isset($_POST["singleEditList"])) {
			$user_set = get_users($_POST["singleEditList"]);
		} elseif(isset($_POST["userId_input"])) {
			$user_set = get_users($_POST["userId_input"]);
		}
		$user_array = mysqli_fetch_array($user_set);
	}
	
	$userForm = "<div id=\"userForm_div\"><form id=\"userForm_form\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\">";
	if($editUser == TRUE) {
		$userForm .= "<input type=\"hidden\" name=\"userId_input\" value=\"" . htmlspecialchars($user_array['id']) . "\" />";
	}
	$userForm .= "<div id=\"userFormPersonal_div\"><label for=\"userName_input\">CTM Username</label><input type=\"text\" id=\"userName_input\" name=\"userName_input\" ";
	if(isset($validator->errors["userName_input"])) {
		$userForm .= "class=\"error\" ";
	}
	if($editUser == TRUE) {
		$userForm .= "value=\"";
		if(isset($_POST["userName_input"])) {
			$userForm .= htmlspecialchars($_POST["userName_input"]);
		} else {
			$userForm .=  htmlspecialchars($user_array['user_name']);	
		}
		$userForm .= "\" ";
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"forumName_input\">Forum Name</label><input type=\"text\" id=\"forumName_input\" name=\"forumName_input\" autocomplete=\"off\" ";
	if(isset($validator->errors["forumName_input"])) {
		$userForm .= "class=\"error\" ";
	}
	if($editUser == TRUE) {
		$userForm .= "value=\"";
		if(isset($_POST["forumName_input"])) {
			$userForm .= htmlspecialchars($_POST["forumName_input"]);
		} else {
			$userForm .=  htmlspecialchars($user_array['forum_name']);	
		}
		$userForm .= "\" ";
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"firstName_input\">First Name</label><input type=\"text\" id=\"firstName_input\" name=\"firstName_input\" autocomplete=\"off\" ";
	if(isset($validator->errors["firstName_input"])) {
		$userForm .= "class=\"error\" ";
	}
	if($editUser == TRUE) {
		$userForm .= "value=\"";
		if(isset($_POST["firstName_input"])) {
			$userForm .= htmlspecialchars($_POST["firstName_input"]);
		} else {
			$userForm .=  htmlspecialchars($user_array['first_name']);	
		}
		$userForm .= "\" ";
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"lastName_input\">Last Name</label><input type=\"text\" id=\"lastName_input\" name=\"lastName_input\" autocomplete=\"off\" ";
	if(isset($validator->errors["lastName_input"])) {
		$userForm .= "class=\"error\" ";
	}
	if($editUser == TRUE) {
		$userForm .= "value=\"";
		if(isset($_POST["lastName_input"])) {
			$userForm .= htmlspecialchars($_POST["lastName_input"]);
		} else {
			$userForm .=  htmlspecialchars($user_array["last_name"]);	
		}
		$userForm .= "\" ";
	}
	$userForm .= "/><br />";
	$rank_array = array("Guest" => 1, "User" => 10, "Admin" => 50, "Superadmin" => 100);
	$userForm .= "<label for=\"rank_select\">Rank</label><select id=\"rank_select\" name=\"rank_select\">";
	foreach($rank_array as $rankName => $rankValue) {
		$userForm .= "<option value=\"" . htmlspecialchars($rankValue) . "\" ";
		if($editUser == TRUE) {
			if(isset($_POST["rank_select"])) {
				if($_POST["rank_select"] == $rankValue) {
					$userForm .= "selected=\"selected\" ";
				}
			} else {
				if($user_array["rank"] == $rankValue) {
					$userForm .= "selected=\"selected\" ";
				}
			}
		}
		$userForm .= ">" . htmlspecialchars($rankName) . "</option>";
	}
	$userForm .= "</select><br />";
	$userForm .= "<label for=\"active_input\" class=\"check\">Active</label><input type=\"checkbox\" id=\"active_input\" name=\"active_input\" ";
	if($editUser == TRUE) {
		if(isset($_POST["active_input"])) {
			if($_POST["active_input"] == TRUE) {
				$userForm .=  "checked=\"checked\" ";	
			}
		}
		if($user_array["active"] == TRUE) {
			$userForm .=  "checked=\"checked\" ";	
		}
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"password_input\">Password</label><input type=\"password\" id=\"password_input\" name=\"password_input\" ";
	if(isset($validator->errors["password_input"])) {
		$userForm .= "class=\"error\" ";
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"confPassword_input\">Confirm Password</label><input type=\"password\" id=\"confPassword_input\" name=\"confPassword_input\"  ";
	if(isset($validator->errors["confPassword_input"])) {
		$userForm .= "class=\"error\" ";
	}
	$userForm .= "/><br />";
	$userForm .= "<label for=\"changePw_input\" class=\"check\">Change password on next login</label><input type=\"checkbox\" id=\"changePw_input\" name=\"changePw_input\" /></div><hr />";
	$weekdays_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
	$userForm .= "<div id=\"userFormSchedule_div\"><table id=\"userFormSchedule_table\">";
	$userForm .= "<thead><tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr></thead><tbody>";
	foreach($weekdays_array as $weekday) {
		if($editUser == TRUE) {
			$userSched_array = get_sch_for_user($user_array["id"], strtolower($weekday));
		}
		$userForm .= "<tr><td>{$weekday}</td><td><input type=\"text\" name=\"{$weekday}Begin_input\" ";
		if(isset($validator->errors["{$weekday}Begin_input"])) {
			$userForm .= "class=\"error\" ";
		}
		if($editUser == TRUE) {
			if(isset($_POST["{$weekday}Begin_input"])) {
				$userForm .= "value=\"" . format_time($_POST["{$weekday}Begin_input"], "html") . "\" ";
			}
			$userForm .= "value=\"" . format_time($userSched_array["start_time"], "html") . "\" ";
		}
		$userForm .= "/></td><td><input type=\"text\" name=\"{$weekday}End_input\" ";
		if(isset($validator->errors["{$weekday}End_input"])) {
			$userForm .= "class=\"error\" ";
		}
		if($editUser == TRUE) {
			if(isset($_POST["{$weekday}End_input"])) {
				$userForm .= "value=\"" . format_time($_POST["{$weekday}End_input"], "html") . "\" ";
			}
			$userForm .= "value=\"" . format_time($userSched_array["end_time"], "html") . "\" ";
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
	
	mysqli_free_result($user_set);
?>