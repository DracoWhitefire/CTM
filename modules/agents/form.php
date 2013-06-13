<?php
// New Agent Form
	if($editAgent == TRUE) {
		$agent_set = get_agents($_POST["singleEditList"]);
		$agent_array = mysql_fetch_array($agent_set);
	}
	
	$agentForm = "Agent Form";
	$agentForm .= "<div id=\"agentForm_div\"><form id=\"agentForm_form\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\">";
	if($editAgent == TRUE) {
		$agentForm .= "<input type=\"hidden\" name=\"agentId_input\" value=\"{$agent_array['id']}\" />";
	}
	$agentForm .= "<label for=\"userName_input\">CTM Username</label><input type=\"text\" id=\"userName_input\" name=\"userName_input\" ";
	if($editAgent == TRUE) {
		$agentForm .=  "value=\"{$agent_array['user_name']}\" ";
	}
	$agentForm .= "/><br />";
	$agentForm .= "<label for=\"forumName_input\">Forum Name</label><input type=\"text\" id=\"forumName_input\" name=\"forumName_input\" ";
	if($editAgent == TRUE) {
		$agentForm .=  "value=\"{$agent_array['forum_name']}\" ";
	}
	$agentForm .= "/><br />";
	$agentForm .= "<label for=\"firstName_input\">First Name</label><input type=\"text\" id=\"firstName_input\" name=\"firstName_input\" ";
	if($editAgent == TRUE) {
		$agentForm .=  "value=\"{$agent_array['first_name']}\" ";
	}
	$agentForm .= "/><br />";
	$agentForm .= "<label for=\"lastName_input\">Last Name</label><input type=\"text\" id=\"lastName_input\" name=\"lastName_input\" ";
	if($editAgent == TRUE) {
		$agentForm .=  "value=\"{$agent_array['last_name']}\" ";
	}
	$agentForm .= "/><br />";
	$agentForm .= "<label for=\"rank_select\">Rank</label><select id=\"rank_select\" name=\"rank_select\">";
	$agentForm .= "<option value=\"1\" ";
	if($editAgent == TRUE) {
		if($agent_array["rank"] == 1) {
			$agentForm .= "selected=\"selected\" ";
		}
	}
	$agentForm .= ">Guest</option>";
	$agentForm .= "<option value=\"10\" ";
	if($editAgent == TRUE) {
		if($agent_array["rank"] == 10) {
			$agentForm .= "selected=\"selected\" ";
		}
	}
	$agentForm .= ">Agent</option>";
	$agentForm .= "<option value=\"50\" ";
	if($editAgent == TRUE) {
		if($agent_array["rank"] == 50) {
			$agentForm .= "selected=\"selected\" ";
		}
	}
	$agentForm .= ">Admin</option>";
	$agentForm .= "<option value=\"100\" ";
	if($editAgent == TRUE) {
		if($agent_array["rank"] == 100) {
			$agentForm .= "selected=\"selected\" ";
		}
	}
	$agentForm .= ">Superadmin</option>";
	$agentForm .= "</select><br />";
	$agentForm .= "<label for=\"active_input\">Active</label><input type=\"checkbox\" id=\"active_input\" name=\"active_input\" ";
	if($editAgent == TRUE) {
		if($agent_array["active"] == TRUE) {
			$agentForm .=  "checked=\"checked\" ";	
		}
	}
	$agentForm .= "/><br />";
	$agentForm .= "<label for=\"password_input\">Password</label><input type=\"password\" id=\"password_input\" name=\"password_input\" /><br />";
	$agentForm .= "<label for=\"confPassword_input\">Confirm Password</label><input type=\"password\" id=\"confPassword_input\" name=\"confPassword_input\" /><br />";
	$agentForm .= "<label for=\"changePw_input\">Change password on next login</label><input type=\"checkbox\" id=\"changePw_input\" name=\"changePw_input\" />";
	$weekdays_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
	$agentForm .= "<div id=\"agentFormSchedule_div\"><table id=\"agentFormSchedule_table\">";
	$agentForm .= "<thead><tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr></thead><tbody>";
	foreach($weekdays_array as $weekday) {
		if($editAgent == TRUE) {
			$agentSched_array = get_sch_for_agent($agent_array["id"], strtolower($weekday));
		}
		$agentForm .= "<tr><td>{$weekday}</td><td><input type=\"text\" name=\"{$weekday}Begin_input\" ";
		if($editAgent == TRUE) {
			$agentForm .= "value=\"{$agentSched_array['start_time']}\" ";
		}
		$agentForm .= "/></td><td><input type=\"text\" name=\"{$weekday}End_input\" ";
		if($editAgent == TRUE) {
			$agentForm .= "value=\"{$agentSched_array['end_time']}\" ";
		}
		$agentForm .= "/></td></tr>";
	}
	$agentForm .= "</tbody></table></div>";
	if($addAgent == TRUE) {
		$agentForm .= "<input type=\"submit\" value=\"Add User\" name=\"submitForm\" />";
	} elseif($editAgent == TRUE) {
		$agentForm .= "<input type=\"submit\" value=\"Submit User\" name=\"submitForm\" />";
	}	
	$agentForm .= "<input type=\"reset\" value=\"Reset\" />";
	$agentForm .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelSubmitForm\" />";
	$agentForm .= "</form></div>";
?>