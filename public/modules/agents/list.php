<?php
	$userList = "<div id=\"userlist_div\"><form id=\"userlist_form\"  action=\"index.php?id=" . urlencode($current_id) . "\" method=\"POST\" >";
	$userList .= "<table id=\"userlist_table\">";
	$userList .= "<thead>";
	$userList .= "<tr><th></th><th>CTM Username</th><th>First Name</th><th>Last Name</th><th>Forum Name</th><th>CTM Rank</th><th>Active</th><th></th></tr>";
	$userList .= "</thead>";
	$userList .= "<tbody>";
	while($user_row = mysqli_fetch_array($user_set)) {
		$id = $user_row["id"];
		$fieldname = "edit_{$id}";
		$editRow = FALSE;
		if((isset($_POST["$fieldname"]))) {
			if($_POST["$fieldname"] == "on") {
				$editRow = TRUE;
			}
		} elseif((!empty($validator->errors)) && ((isset($errorId_array[$id])) || isset($_POST["user_name_{$id}"]))) {
			$editRow = TRUE;
		}
		$userList .= "<tr><td class=\"check\" >";
		if($editList == FALSE) {
			$userList .= "<input type=\"checkbox\" name=\"" . htmlspecialchars("edit_{$id}") . "\" />";
		}
		$userList .= "</td>";
		$userList .= "<td class=\"name";
		if(isset($validator->errors["user_name_{$id}"])) {
			$userList .= " error\"";
		} else {
			$userList .= "\"";
		}
		$userList .= " >";
		if($editRow == TRUE) {
			$userList .= "<input type=\"text\" name=\"" . htmlspecialchars("user_name_{$id}") . "\" value=\"";
		}
		if(isset($_POST["user_name_{$id}"])) {
			$userList .= htmlspecialchars($_POST["user_name_{$id}"]);
		} else {
			$userList .= htmlspecialchars($user_row["user_name"]);
		}		
		if($editRow == TRUE) {
			$userList .= "\" />";
		}
		$userList .= "</td>";
		$userList .= "<td class=\"name";
		if(isset($validator->errors["first_name_{$id}"])) {
			$userList .= " error\"";
		} else {
			$userList .= "\"";
		}
		$userList .= " >";
		if($editRow == TRUE) {
			$userList .= "<input type=\"text\" name=\"" . htmlspecialchars("first_name_{$id}") . "\" value=\"";
		}
		if(isset($_POST["first_name_{$id}"])) {
			$userList .= htmlspecialchars($_POST["first_name_{$id}"]);
		} else {
			$userList .= htmlspecialchars($user_row["first_name"]);
		}		
		if($editRow == TRUE) {
			$userList .= "\" />";
		}
		$userList .= "</td>"; 
		$userList .= "<td class=\"name";
		if(isset($validator->errors["last_name_{$id}"])) {
			$userList .= " error\"";
		} else {
			$userList .= "\"";
		}
		$userList .= " >";
		if($editRow == TRUE) {
			$userList .= "<input type=\"text\" name=\"" . htmlspecialchars("last_name_{$id}") . "\" value=\"";
		}
		if(isset($_POST["last_name_{$id}"])) {
			$userList .= htmlspecialchars($_POST["last_name_{$id}"]);
		} else {
			$userList .= htmlspecialchars($user_row["last_name"]);
		}
		if($editRow == TRUE) {
			$userList .= "\" />";
		}
		$userList .= "</td>"; 
		$userList .= "<td class=\"name";
		if(isset($validator->errors["forum_name_{$id}"])) {
			$userList .= " error\"";
		} else {
			$userList .= "\"";
		}
		$userList .= ">";
		if($editRow == TRUE) {
			$userList .= "<input type=\"text\" name=\"" . htmlspecialchars("forum_name_{$id}") . "\" value=\"";
		}
		if(isset($_POST["forum_name_{$id}"])) {
			$userList .= htmlspecialchars($_POST["forum_name_{$id}"]);
		} else {
			$userList .= htmlspecialchars($user_row["forum_name"]);
		}
		if($editRow == TRUE) {
			$userList .= "\" />";
		}
		$userList .= "<td class=\"rank\" >";
		if($editRow == TRUE) {
			$userList .= "<select id=\"" . htmlspecialchars("rank_select_{$id}") . "\" name=\"" . htmlspecialchars("rank_select_{$id}") . "\">";
			$userList .= "<option value=\"1\" ";
			if($user_row["rank"] == 1) {
				$userList .= "selected=\"selected\" ";
			}
			$userList .= ">Guest</option>";
			$userList .= "<option value=\"10\" ";
			if($user_row["rank"] == 10) {
				$userList .= "selected=\"selected\" ";
			}
			$userList .= ">User</option>";
			$userList .= "<option value=\"50\" ";
			if($user_row["rank"] == 50) {
				$userList .= "selected=\"selected\" ";
			}
			$userList .= ">Admin</option>";
			$userList .= "<option value=\"100\" ";
			if($user_row["rank"] == 100) {
				$userList .= "selected=\"selected\" ";
			}
			$userList .= ">Superadmin</option>";
			$userList .= "</select>";
		} else {
			$userList .= htmlspecialchars(convert_rank($user_row["rank"]));
		}
		$userList .= "</td>"; 
		$userList .= "<td><input type=\"checkbox\" name=\"" . htmlspecialchars("active_check_{$id}") . "\" ";
		if($editRow == FALSE) {
			$userList .= "disabled=\"disabled\" ";
		}
		if($user_row["active"] == TRUE) {
			$userList .= "checked=\"checked\" ";
		}
		$userList .= "/>";
		$userList .= "</td>";
		$userList .= "<td>";
		if($editList == FALSE) {
			$userList .= "<button type=\"submit\" name=\"singleEditList\" formmethod=\"post\" value=\"" . htmlspecialchars($id) . "\"  >Edit</button>";
		}
		$userList .= "</td></tr>";
	}

	$userList .= "</tbody>";
	$userList .= "</table>";
	if($editList == TRUE) {
		$userList .= "<input type=\"submit\" value=\"Submit\" name=\"submitList\" />";
		$userList .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelEditList\" />";
	} else {
		$userList .= "<input type=\"submit\" value=\"Bulk Edit\" name=\"editList\" />";
		$userList .= "<input type=\"submit\" value=\"Add\" name=\"addList\" />";
	}
	$userList .= "</form></div>";
	
	mysqli_free_result($user_set);
?>