<?php
	$userList = "<div id=\"userlist_div\"><form id=\"userlist_form\"  action=\"index.php?id=" . urlencode($current_id) . "\" method=\"POST\" >";
	$userList .= "<table id=\"userlist_table\">";
	$userList .= "<thead>";
	$userList .= "<tr><th></th><th>CTM Username</th><th>First Name</th><th>Last Name</th><th>Forum Name</th><th>CTM Rank</th><th>Active</th><th></th></tr>";
	$userList .= "</thead>";
	$userList .= "<tbody>";
	foreach($users_array as $user) {
		$id = $user->id;
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
			$userList .= htmlspecialchars($user->userName);
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
			$userList .= htmlspecialchars($user->firstName);
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
			$userList .= htmlspecialchars($user->lastName);
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
			$userList .= htmlspecialchars($user->forumName);
		}
		if($editRow == TRUE) {
			$userList .= "\" />";
		}
		$userList .= "</td>";
		$userList .= "<td class=\"rank\" >";
		if($editRow == TRUE) {
			$userList .= Rank::selector($user);
		} else {
			$rank = Rank::get($user->rank);
			$userList .= htmlspecialchars($rank->name);
		}
		$userList .= "</td>"; 
		$userList .= "<td><input type=\"checkbox\" name=\"" . htmlspecialchars("active_check_{$id}") . "\" ";
		if($editRow == FALSE) {
			$userList .= "disabled=\"disabled\" ";
		}
		if($user->active == TRUE) {
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
?>