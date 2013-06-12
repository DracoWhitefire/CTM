<?php
// New Agent Form
	if($editAgent == TRUE) {
		$agent_set = get_agents($_POST["agent_id"]);
		$agent = mysql_fetch_array($agent_set);
	}
	$agentForm = "Agent Form";
	$agentForm .= "<div id=\"agentForm_div\"><form id=\"agentForm_form\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\">";
	$agentForm .= "<label for=\"forumName_input\">Forum Name</label><input type=\"text\" id=\"forumName_input\" name=\"forumName_input\" /><br />";
	$agentForm .= "<label for=\"firstName_input\">First Name</label><input type=\"text\" id=\"firstName_input\" name=\"firstName_input\" /><br />";
	$agentForm .= "<label for=\"lastName_input\">Last Name</label><input type=\"text\" id=\"lastName_input\" name=\"lastName_input\" /><br />";
	$agentForm .= "<label for=\"rank_select\">Rank</label><select id=\"rank_select\" name=\"rank_select\">";
	$agentForm .= "<option value=\"10\">Agent</option>";
	$agentForm .= "<option value=\"50\">Admin</option>";
	$agentForm .= "<option value=\"100\">Superadmin</option>";
	$agentForm .= "</select><br />";
	$agentForm .= "<label for=\"active_input\">Active</label><input type=\"checkbox\" id=\"active_input\" name=\"active_input\" /><br />";
	$agentForm .= "<div id=\"agentFormSchedule_div\"><table id=\"agentFormSchedule_table\">";
	$agentForm .= "<thead><tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr></thead>";
	$agentForm .= "<tbody><tr><td>Monday</td><td><input type=\"text\" name=\"mondayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Tuesday</td><td><input type=\"text\" name=\"tuesdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Wednesday</td><td><input type=\"text\" name=\"wednesdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Thursday</td><td><input type=\"text\" name=\"thursdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Friday</td><td><input type=\"text\" name=\"fridayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "</tbody></table></div>";
	$agentForm .= "<input type=\"submit\" value=\"Add User\" name=\"submitForm\" />";
	$agentForm .= "<input type=\"reset\" value=\"Reset\" />";
	$agentForm .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelSubmitForm\" />";
	$agentForm .= "</form></div>";
?>