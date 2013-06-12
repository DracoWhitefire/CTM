<?php
	$agentList = "<div id=\"agentlist_div\"><form id=\"agentlist_form\"  action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\" method=\"POST\" >";
	$agentList .= "<table id=\"agentlist_table\">";
	$agentList .= "<thead>";
	$agentList .= "<tr><th></th><th>CTM Username</th><th>First Name</th><th>Last Name</th><th>Forum Name</th><th>CTM Rank</th><th>Active</th><th></th></tr>";
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
		} elseif((!empty($errors)) && ((isset($errorId_array[$id])) || isset($_POST["user_name_{$id}"]))) {
			$editRow = TRUE;
		}
		$agentList .= "<tr><td class=\"check\" >";
		if($editList == FALSE) {
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
			$agentList .= htmlentities($_POST["user_name_{$id}"]);
		} else {
			$agentList .= htmlentities($agent_row["user_name"]);
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
			$agentList .= htmlentities($_POST["first_name_{$id}"]);
		} else {
			$agentList .= htmlentities($agent_row["first_name"]);
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
			$agentList .= htmlentities($_POST["last_name_{$id}"]);
		} else {
			$agentList .= htmlentities($agent_row["last_name"]);
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
			$agentList .= htmlentities($_POST["forum_name_{$id}"]);
		} else {
			$agentList .= htmlentities($agent_row["forum_name"]);
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
			$agentList .= htmlentities(convert_rank($agent_row["rank"]));
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
		$agentList .= "</td>";
		$agentList .= "<td>";
		if($editList == FALSE) {
			$agentList .= "<form id=\"row_{$id}\" ><input type=\"hidden\" value=\"{$id}\" name=\"agent_id\" ><input type=\"submit\" value=\"Edit\" name=\"singleEditList\" formmethod=\"post\" form=\"row_{$id}\"  /></form>";
		}
		$agentList .= "</td></tr>";
	}

	$agentList .= "</tbody>";
	$agentList .= "</table>";
	if($editList == TRUE) {
		$agentList .= "<input type=\"submit\" value=\"Submit\" name=\"submitList\" />";
		$agentList .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelEditList\" />";
	} else {
		$agentList .= "<input type=\"submit\" value=\"Bulk Edit\" name=\"editList\" />";
		$agentList .= "<input type=\"submit\" value=\"Add\" name=\"addList\" />";
	}
	$agentList .= "</form></div>";
?>