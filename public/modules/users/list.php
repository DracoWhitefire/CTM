<?php
	
	//print_r($_POST);
	//print_r($_COOKIE);
	$columnName_array = array("userName", "firstName", "lastName", "forumName", "rank", "active");
	function listFilter($columnName_array) {
		$columnFilter_array = array();
		$expiration = time()+60*60*24*31;
		foreach($columnName_array as $columnName) {
			$columnName;
			$columnFilter_array[$columnName] = FALSE;
			if(isset($_POST["userListFilter_submit"])) {
				if(isset($_POST[$columnName . "Vis_check"])) {
					if($_POST[$columnName . "Vis_check"] == "on") {
						setcookie($columnName, TRUE, $expiration);
						$columnFilter_array[$columnName] = TRUE;
					}
				} else {
					unset($_COOKIE[$columnName]);
				}
			} else {
				if(isset($_COOKIE[$columnName])) {
					if($_COOKIE[$columnName] == TRUE) {
						$columnFilter_array[$columnName] = TRUE;
					}
				}
			}
		}
		return $columnFilter_array;
	}
	$columnFilter_array = listFilter($columnName_array);
	$userList = "<div id=\"userListFilter_div\" ><form id=\"userlistFilter_form\"  action=\"index.php?id=" . urlencode($current_id) . "\" method=\"POST\" >";
	$userList .= "<div id=\"listColumnFilter_div\" >";
	$userList .= "<label for \"userNameVis_check\" >User Name</label><input type=\"checkbox\" id=\"userNameVis_check\" name=\"userNameVis_check\" ";
	if($columnFilter_array["userName"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "<label for \"firstNameVis_check\" >First Name</label><input type=\"checkbox\" id=\"firstNameVis_check\" name=\"firstNameVis_check\" ";
	if($columnFilter_array["firstName"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "<label for \"lastNameVis_check\" >Last Name</label><input type=\"checkbox\" id=\"lastNameVis_check\" name=\"lastNameVis_check\" ";
	if($columnFilter_array["lastName"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "<label for \"forumNameVis_check\" >Forum Name</label><input type=\"checkbox\" id=\"forumNameVis_check\" name=\"forumNameVis_check\" ";
	if($columnFilter_array["forumName"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "<label for \"rankVis_check\" >Rank</label><input type=\"checkbox\" id=\"rankVis_check\" name=\"rankVis_check\" ";
	if($columnFilter_array["rank"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "<label for \"activeVis_check\" >Active</label><input type=\"checkbox\" id=\"activeVis_check\" name=\"activeVis_check\" ";
	if($columnFilter_array["active"]) {
		$userList .= "checked=\"checked\" ";
	}
	$userList .= ">";
	$userList .= "</div>";
	$userList .= "<input type=\"submit\" id=\"userListFilter_submit\" name=\"userListFilter_submit\" value=\"Filter\" >";
	$userList .= "</div>";
	
	$userList .= "<div id=\"userlist_div\"><form id=\"userlist_form\"  action=\"index.php?id=" . urlencode($current_id) . "\" method=\"POST\" >";
	$userList .= "<table id=\"userlist_table\">";
	$userList .= "<thead>";
	$userList .= "<tr><th></th>";
	if($columnFilter_array["userName"]) {
		$userList .= "<th>CTM Username</th>";
	}
	if($columnFilter_array["firstName"]) {
		$userList .= "<th>First Name</th>";
	}
	if($columnFilter_array["lastName"]) {
		$userList .= "<th>Last Name</th>";
	}
	if($columnFilter_array["forumName"]) {
		$userList .= "<th>Forum Name</th>";
	}
	if($columnFilter_array["rank"]) {
		$userList .= "<th>CTM Rank</th>";
	}
	if($columnFilter_array["active"]) {
		$userList .= "<th>Active</th>";
	}
	$userList .= "<th></th></tr>";
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
		if($columnFilter_array["userName"]) {
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
		}
		if($columnFilter_array["firstName"]) {
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
		}
		if($columnFilter_array["lastName"]) {
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
		}
		if($columnFilter_array["forumName"]) {
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
		}
		if($columnFilter_array["rank"]) {
			$userList .= "<td class=\"rank\" >";
			if($editRow == TRUE) {
				$userList .= Rank::selector($user);
			} else {
				$rank = Rank::get($user->rank);
				$userList .= htmlspecialchars($rank->name);
			}
			$userList .= "</td>";
		}
		if($columnFilter_array["active"]) {
			$userList .= "<td><input type=\"checkbox\" name=\"" . htmlspecialchars("active_check_{$id}") . "\" ";
			if($editRow == FALSE) {
				$userList .= "disabled=\"disabled\" ";
			}
			if($user->active == TRUE) {
				$userList .= "checked=\"checked\" ";
			}
			$userList .= "/>";
			$userList .= "</td>";
		}
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