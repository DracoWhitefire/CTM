<?php
	$errors = array();
	if(isset($_POST["edit"])) {
		if(count($_POST) > 1) {
			$editing = TRUE;
			$addAgent = FALSE;
		} else {
			$editing = FALSE;
			$addAgent = FALSE;
		}
	} else {
		$editing = FALSE;
		if(isset($_POST["add"])) {
			$addAgent = TRUE;
		} else {
			$addAgent = FALSE;
		}
	}
	if(isset($_POST["submit"])) {
		
		//Form Validation
		
		$checkReq_array = array();
		$checkLen_array = array();
		$checkNum_array = array();
		foreach($_POST as $valField => $val) {			
			$valFieldString_array = explode("_", $valField);
			if(count($valFieldString_array) > 1){
				if($valFieldString_array[1] == "name") {
					$checkLen_array[$valField] = "1-32";
					$checkReq_array[] = $valField;
				}
				if(($valFieldString_array[0] == "rank")) {
					$checkNum_array[] = $valField;
					$checkReq_array[] = $valField;
				}
			}
		}
		form_val_required($checkReq_array);
		form_val_length($checkLen_array);
		form_val_num($checkNum_array);
		
		// This sorts all POST-vars by agent id
		foreach($_POST as $varName => $postValue) {
			$string_array = explode("_", $varName);
			if(count($string_array) > 1){
				$string_id = $string_array[2];
				if(!isset($query_array_{$string_id})) {
					$query_array_{$string_id} = array();
				}
				$field = $string_array[0] . "_" . $string_array[1];
				$postValue = trim(mysql_prep($postValue));
				$query_array_{$string_id}[$field] = $postValue;
				$query_array[$string_id] = $query_array_{$string_id};
			}
		}		
		//print_r($query_array);
		
		// This combines every field in an id (row) into one query and runs the query
		foreach($query_array as $array_id => $agent_array) {
			$i = 1;
			$changefields = "";
			if(!isset($agent_array["active_check"])) {
				$agent_array["active_check"] = "off";
			}
			//print_r($agent_array);
			foreach($agent_array as $field => $postValue) {
				if($field == "active_check") {
					$field = "active";
					if($postValue == "on") {
						$postValue = 1;
					} elseif($postValue == "off") {
						$postValue = 0;
					}
				}
				if($field == "rank_select") {
					$field = "rank";
				}
				$changefields .= "`{$field}` = '{$postValue}'";
				if($i < count($agent_array)) {
					$changefields .= ",";
				}
				$changefields .= " ";
				$i++;
			}
			$query  = "UPDATE `agents` SET ";
			$query .= $changefields;
			$query .= "WHERE `id` = '{$array_id}' ";
			$query .= "LIMIT 1 ";
			$query .= "; ";
			//echo $query;
			
			if(empty($errors)) {
				$result = mysql_query($query);
				
				if(!$result) {
					echo "MySQL Query Failed: " . mysql_error();
				}
			} else {
				$editing = TRUE;
			}
		}
	}
	
	// If validation fails:	
	$errorId_array = array();
	foreach($errors as $errorField => $errorName) {
			$errorString_array = explode("_", $errorField);
			$errorId = $errorString_array[2];
			$errorId_array[$errorId] = TRUE;
	}
	//print_r($errorId_array);
	//print_r($errors);
	
	
	$agent_set = get_agents("all");
	$agentList = "<div id=\"agentlist_div\"><form id=\"agentlist_form\"  action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\" method=\"POST\" >";
	$agentList .= "<table id=\"agentlist_table\">";
	$agentList .= "<thead>";
	$agentList .= "<tr><th></th><th>CTM Username</th><th>First Name</th><th>Last Name</th><th>Forum Name</th><th>CTM Rank</th><th>Active</th></tr>";
	$agentList .= "</thead>";
	$agentList .= "<tbody>";
	while($agent_row = mysql_fetch_array($agent_set)) {
		$id = $agent_row["id"];
		$fieldname = "edit_{$id}";
		$editRow = FALSE;
		if((isset($_POST["$fieldname"]))) {
			if($_POST["$fieldname"] == "on") {
				$editRow = TRUE;
			}
		} elseif(isset($errorId_array[$id])) {
			$editRow = TRUE;
		}
		$agentList .= "<tr><td class=\"check\" >";
		if($editing == FALSE) {
			$agentList .= "<input type=\"checkbox\" name=\"edit_{$id}\" />";
		}
		$agentList .= "</td>";
		$agentList .= "<td class=\"name";
		if(isset($errors["user_name_{$id}"])) {
			$agentList .= " error\"";
		} else {
			$agentList .= "\"";
		}
		$agentList .= " >";
		if($editRow == TRUE) {
			$agentList .= "<input type=\"text\" name=\"user_name_{$id}\" value=\"";
		}
		if(isset($_POST["user_name_{$id}"])) {
			$agentList .= $_POST["user_name_{$id}"];
		} else {
			$agentList .= $agent_row["user_name"];
		}		
		if($editRow == TRUE) {
			$agentList .= "\" />";
		}
		$agentList .= "</td>";
		$agentList .= "<td class=\"name";
		if(isset($errors["first_name_{$id}"])) {
			$agentList .= " error\"";
		} else {
			$agentList .= "\"";
		}
		$agentList .= " >";
		if($editRow == TRUE) {
			$agentList .= "<input type=\"text\" name=\"first_name_{$id}\" value=\"";
		}
		if(isset($_POST["first_name_{$id}"])) {
			$agentList .= $_POST["first_name_{$id}"];
		} else {
			$agentList .= $agent_row["first_name"];
		}		
		if($editRow == TRUE) {
			$agentList .= "\" />";
		}
		$agentList .= "</td>"; 
		$agentList .= "<td class=\"name";
		if(isset($errors["last_name_{$id}"])) {
			$agentList .= " error\"";
		} else {
			$agentList .= "\"";
		}
		$agentList .= " >";
		if($editRow == TRUE) {
			$agentList .= "<input type=\"text\" name=\"last_name_{$id}\" value=\"";
		}
		if(isset($_POST["last_name_{$id}"])) {
			$agentList .= $_POST["last_name_{$id}"];
		} else {
			$agentList .= $agent_row["last_name"];
		}
		if($editRow == TRUE) {
			$agentList .= "\" />";
		}
		$agentList .= "</td>"; 
		$agentList .= "<td class=\"name";
		if(isset($errors["forum_name_{$id}"])) {
			$agentList .= " error\"";
		} else {
			$agentList .= "\"";
		}
		$agentList .= ">";
		if($editRow == TRUE) {
			$agentList .= "<input type=\"text\" name=\"forum_name_{$id}\" value=\"";
		}
		if(isset($_POST["forum_name_{$id}"])) {
			$agentList .= $_POST["forum_name_{$id}"];
		} else {
			$agentList .= $agent_row["forum_name"];
		}		
		if($editRow == TRUE) {
			$agentList .= "\" />";
		}
		$agentList .= "<td class=\"rank\" >";
		if($editRow == TRUE) {
			$agentList .= "<select id=\"rank_select_{$id}\" name=\"rank_select_{$id}\">";
			$agentList .= "<option value=\"1\" ";
			if($agent_row["rank"] == 1) {
				$agentList .= "selected=\"selected\" ";
			}
			$agentList .= ">Guest</option>";
			$agentList .= "<option value=\"10\" ";
			if($agent_row["rank"] == 10) {
				$agentList .= "selected=\"selected\" ";
			}
			$agentList .= ">Agent</option>";
			$agentList .= "<option value=\"50\" ";
			if($agent_row["rank"] == 50) {
				$agentList .= "selected=\"selected\" ";
			}
			$agentList .= ">Admin</option>";
			$agentList .= "<option value=\"100\" ";
			if($agent_row["rank"] == 100) {
				$agentList .= "selected=\"selected\" ";
			}
			$agentList .= ">Superadmin</option>";
			$agentList .= "</select>";
		} else {
			$agentList .= convert_rank($agent_row["rank"]);
		}
		$agentList .= "</td>"; 
		$agentList .= "<td><input type=\"checkbox\" name=\"active_check_{$id}\" ";
		if($editRow == FALSE) {
			$agentList .= "disabled=\"disabled\" ";
		}
		if($agent_row["active"] == TRUE) {
			$agentList .= "checked=\"checked\" ";
		}
		$agentList .= "/>";
		$agentList .= "</td></tr>";
	}

	$agentList .= "</tbody>";
	$agentList .= "</table>";
	
	if($editing == TRUE) {
		$agentList .= "<input type=\"submit\" value=\"Submit\" name=\"submit\" />";
		$agentList .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelEdit\" />";
	} else {
		$agentList .= "<input type=\"submit\" value=\"Edit\" name=\"edit\" />";
		$agentList .= "<input type=\"submit\" value=\"Add\" name=\"add\" />";
	}
	$agentList .= "</form></div>";
	
// New Agent Form
	$agentForm = "New Agent Form";
	$agentForm .= "<div id=\"newAgent_div\"><form id=\"newAgent_form\" method=\"POST\" action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\">";
	$agentForm .= "<label for=\"forumName_input\">Forum Name</label><input type=\"text\" id=\"forumName_input\" name=\"forumName_input\" /><br />";
	$agentForm .= "<label for=\"firstName_input\">First Name</label><input type=\"text\" id=\"firstName_input\" name=\"firstName_input\" /><br />";
	$agentForm .= "<label for=\"lastName_input\">Last Name</label><input type=\"text\" id=\"lastName_input\" name=\"lastName_input\" /><br />";
	$agentForm .= "<label for=\"rank_select\">Rank</label><select id=\"rank_select\" name=\"rank_select\">";
	$agentForm .= "<option value=\"10\">Agent</option>";
	$agentForm .= "<option value=\"50\">Admin</option>";
	$agentForm .= "<option value=\"100\">Superadmin</option>";
	$agentForm .= "</select><br />";
	$agentForm .= "<label for=\"active_input\">Active</label><input type=\"checkbox\" id=\"active_input\" name=\"active_input\" /><br />";
	$agentForm .= "<div id=\"newAgentSchedule_div\"><table id=\"newAgentSchedule_table\">";
	$agentForm .= "<tr><th>Weekday</th><th>Begin Shift</th><th>End Shift</th></tr>";
	$agentForm .= "<tr><td>Monday</td><td><input type=\"text\" name=\"mondayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Tuesday</td><td><input type=\"text\" name=\"tuesdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Wednesday</td><td><input type=\"text\" name=\"wednesdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Thursday</td><td><input type=\"text\" name=\"thursdayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "<tr><td>Friday</td><td><input type=\"text\" name=\"fridayBegin_input\" /></td><td><input type=\"text\" name=\"mondayEnd_input\" /></td></tr>";
	$agentForm .= "</table></div>";
	$agentForm .= "<input type=\"submit\" value=\"Add User\" name=\"addUser\" />";
	$agentForm .= "<input type=\"reset\" value=\"Reset\" />";
	$agentForm .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelAddUser\" />";
	$agentForm .= "</form></div>";
	if($addAgent == FALSE) {
		$output = $agentList;
	} else {
		$output = $agentForm;
	}
	echo $output;
?>