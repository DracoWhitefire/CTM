<?php
	if(isset($_POST["cancelEditList"])) {
		$_POST = "";
	}
	if(isset($_POST["editList"])) {
		if(count($_POST) > 1) {
			$editList = TRUE;
			$addUser = FALSE;
			$editUser = FALSE;
		} else {
			$editList = FALSE;
			$addUser = FALSE;
			$editUser = FALSE;
		}
	} elseif(isset($_POST["addList"])) {
		$addUser = TRUE;
		$editUser = FALSE;
		$editList = FALSE;
	} elseif(isset($_POST["singleEditList"])) {
		$editUser = TRUE;
		$addUser = FALSE;
		$editList = FALSE;
	} else {
		$addUser = FALSE;
		$editUser = FALSE;
		$editList = FALSE;
	}
	//Form Validation	
	if((isset($_POST["submitList"])) || (isset($_POST["submitForm"]))) {
		$checkReq_array = array();
		$checkLen_array = array();
		$checkNum_array = array();
		$checkUniq_array = array();
		$checkTime_array = array();
		$checkTimeDiff_array = array();
		$checkSame_array = array();
		$checkPw_array = array();
		foreach($_POST as $valField => $val) {			
			$valFieldString_array = preg_split("/([A-Z][a-z]+)|_/", $valField, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			if(count($valFieldString_array) > 1){
				if(strtolower($valFieldString_array[1]) == "name") {					
					if(strtolower($valFieldString_array[0]) != "forum") {
						$checkReq_array[] = $valField;
						$checkLen_array[$valField] = "1-32";
					}
					if($valFieldString_array[0] == "user") {
						$checkUniq_array[] = $valField;
					}
				}
				if(($valFieldString_array[0] == "rank")) {
					$checkNum_array[] = $valField;
					$checkReq_array[] = $valField;
				}
				if((strtolower($valFieldString_array[1]) == "begin") || (strtolower($valFieldString_array[1]) == "end")) {
					$checkReq_array[] = $valField;
					$checkTime_array[] = $valField;
					if(strtolower($valFieldString_array[1]) == "begin") {
						$checkTimeDiff_array[$valField] = $valFieldString_array[0] . "End_" . $valFieldString_array[2];
					}
				}
				if(strtolower($valFieldString_array[0]) == "password") {
					$checkSame_array[$valField] = "conf" . ucfirst($valFieldString_array[0]) . "_" . $valFieldString_array[1];
					if(!empty($_POST[$valField])) {
						$checkPw_array[] = $valField;
					}
				}
			}
		}
		$validator = new Validator;
		$validator->unique($checkUniq_array);
		$validator->required($checkReq_array);
		$validator->length($checkLen_array);
		$validator->numeric($checkNum_array);
		$validator->time($checkTime_array);
		$validator->timediff($checkTimeDiff_array);
		$validator->password($checkPw_array);
		$validator->compare($checkSame_array);
	}
	//Form Processing
	if(isset($_POST["submitList"])) {
		// This sorts all POST-vars by user id
		foreach($_POST as $varName => $postValue) {
			$string_array = explode("_", $varName);
			if(count($string_array) > 1){
				if(count($string_array) > 2) {
					$string_id = $string_array[2];
				} else {
					$string_id = $_POST["userId_input"];
				}				
				if(!isset($query_array_{$string_id})) {
					$query_array_{$string_id} = array();
				}
				$field = $string_array[0] . "_" . $string_array[1];
				$postValue = trim($db->query_prep($postValue));
				$query_array_{$string_id}[$field] = $postValue;
				$query_array[$string_id] = $query_array_{$string_id};
			}
		}
		// This combines every field in an id (row) into one query and runs the query
		foreach($query_array as $array_id => $user_array) {
			$i = 1;
			$changefields = "";
			if(!isset($user_array["active_check"])) {
				$user_array["active_check"] = "off";
			}
			//print_r($user_array);
			foreach($user_array as $field => $postValue) {
				$field = $db->query_prep($field);
				$postValue = $db->query_prep($postValue);
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
				if($i < count($user_array)) {
					$changefields .= ",";
				}
				$changefields .= " ";
				$i++;
			}
			$query  = "UPDATE `users` SET ";
			$query .= $changefields;
			$query .= "WHERE `id` = '{$array_id}' ";
			$query .= "LIMIT 1 ";
			$query .= "; ";
			if(empty($validator->errors)) {
				$result = $db->query($query);
			} else {
				$editList = TRUE;
			}
		}
	}
	if(isset($_POST["submitForm"])) {
		if(empty($validator->errors)) {
			//Password Hashing
			if(!empty($_POST["password_input"])) {
				$hashPw = User::pw_encrypt($_POST["password_input"]);
			}
			if($_POST["submitForm"] == "Add User") {
				//create user
				if(isset($_POST["active_input"])) {
					$active = 1;
				} else {
					$active = 0;
				}
				$query  = "INSERT INTO `users` ";
				$query .= "(`user_name`, `forum_name`, `first_name`, `last_name`, `rank`, `passwordhash`, `active`) ";
				$query .= "VALUES ('" . 	$db->query_prep($_POST["userName_input"]) . "', '" . 
											$db->query_prep($_POST["forumName_input"]) . "', '" . 
											$db->query_prep($_POST["firstName_input"]) . "', '" . 
											$db->query_prep($_POST["lastName_input"]) . "', '" . 
											$db->query_prep($_POST["rank_select"]) . "', '" .
											$db->query_prep($hashPw) . "', '" . 
											$db->query_prep($active) . "') ";
				$query .= ";";
				$insert_success = $db->query($query);
				
				//create schedule for user
				(int) $createdId = $db->insert_id();
				$query  = "INSERT INTO `schedules` ";
				$query .= "(`id`, `weekday`, `start_time`, `end_time`) ";
				$query .= "VALUES ";
				$weekdays_array = array("monday", "tuesday", "wednesday", "thursday", "friday");
				foreach($weekdays_array as $weekday) {
					$beginFieldname = ucfirst($weekday) . "Begin_input";
					$endFieldname = ucfirst($weekday) . "End_input";
					$query .= 	"(" .	$createdId . ", '" .
										$weekday . "', '" .
										$db->query_prep($_POST[$beginFieldname]) . "', '" .
										$db->query_prep($_POST[$endFieldname]) . "')";
					if($weekday != "friday") {
						$query .= ",";
					}
				}
				$query .= ";";
				$insert_success = $db->query($query);
			} elseif($_POST["submitForm"] == "Submit User") {
				//Update existing user
				if(isset($_POST["active_input"])) {
					$active = 1;
				} else {
					$active = 0;
				}
				$query  = 	"UPDATE `users` SET ";
				$query .= 	"`user_name`='" . 	$db->query_prep($_POST["userName_input"]) . "', " . 
							"`forum_name`='" . 	$db->query_prep($_POST["forumName_input"]) . "', " . 
							"`first_name`='" . 	$db->query_prep($_POST["firstName_input"]) . "', " .  
							"`last_name`='" . 	$db->query_prep($_POST["lastName_input"]) . "', " .  
							"`rank`='" . 		$db->query_prep($_POST["rank_select_" . $_POST["userId_input"]]);
				if(isset($hashPw)) {
					$query .= "', `passwordhash`='" . $db->query_prep($hashPw);
				}
				$query .= "', `active`='" . $db->query_prep($active) . "' ";
				$query .= "WHERE `id`=" . $db->query_prep($_POST["userId_input"]) . " ";
				$query .= "LIMIT 1" ;
				$query .= ";";
				$update_success = $db->query($query);
				
				$weekdays_array = array("monday", "tuesday", "wednesday", "thursday", "friday");
				foreach($weekdays_array as $weekday) {
					$beginFieldname = ucfirst($weekday) . "Begin_input";
					$endFieldname = ucfirst($weekday) . "End_input";
					$query  = "UPDATE `schedules` SET ";
					$query .= "`start_time`='" . $db->query_prep($_POST[$beginFieldname]) . "', `end_time`='" . $db->query_prep($_POST[$endFieldname]) . "' ";
					$query .= "WHERE `id` = '" . $db->query_prep($_POST["userId_input"]) . "' ";
					$query .= "AND `weekday` = '{$weekday}' ";
					$query .= "LIMIT 1 ";
					$query .= ";";
					$insert_success = $db->query($query);
				}
				
			}
		} else {
			if($_POST["submitForm"] == "Add User") {
				$editUser = TRUE;
				$addUser = TRUE;
				
			} elseif($_POST["submitForm"] == "Submit User") {
				$editUser = TRUE;
			}
		}
	}
	// If validation fails:	
	if(isset($_POST["submitList"])) {
		if(isset($validator->errors) && !empty($validator->errors)) {
			$errorId_array = array();
			foreach($validator->errors as $errorField => $errorName) {
					$errorString_array = explode("_", $errorField);
					$errorId = $errorString_array[2];
					$errorId_array[$errorId] = TRUE;
			}
		}
	}
	$users_array = User::get("all");
	if(($addUser == TRUE) || ($editUser == TRUE)) {
		include("form.php");
		$output = $userForm;
	} else {
		include("list.php");
		$output = $userList;
	}
	echo $output;
?>