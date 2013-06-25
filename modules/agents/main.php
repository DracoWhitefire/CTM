<?php
	$errors = array();
	if(isset($_POST["cancelEditList"])) {
		$_POST = "";
	}
	if(isset($_POST["editList"])) {
		if(count($_POST) > 1) {
			$editList = TRUE;
			$addAgent = FALSE;
			$editAgent = FALSE;
		} else {
			$editList = FALSE;
			$addAgent = FALSE;
			$editAgent = FALSE;
		}
	} elseif(isset($_POST["addList"])) {
		$addAgent = TRUE;
		$editAgent = FALSE;
		$editList = FALSE;
	} elseif(isset($_POST["singleEditList"])) {
		$editAgent = TRUE;
		$addAgent = FALSE;
		$editList = FALSE;
	} else {
		$addAgent = FALSE;
		$editAgent = FALSE;
		$editList = FALSE;
	}
	if((isset($_POST["submitList"])) || (isset($_POST["submitForm"]))) {
		//Form Validation		
		$checkReq_array = array();
		$checkLen_array = array();
		$checkNum_array = array();
		$checkUniq_array = array();
		$checkTime_array = array();
		$checkTimeDiff_array = array();
		foreach($_POST as $valField => $val) {			
			$valFieldString_array = preg_split("/([A-Z][a-z]+)|_/", $valField, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			if(count($valFieldString_array) > 1){
				if(strtolower($valFieldString_array[1]) == "name") {					
					$checkLen_array[$valField] = "1-32";
					$checkReq_array[] = $valField;
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
			}
		}
		form_val_unique($checkUniq_array);
		form_val_required($checkReq_array);
		form_val_length($checkLen_array);
		form_val_num($checkNum_array);
		form_val_time($checkTime_array);
		form_val_timediff($checkTimeDiff_array);
	}
	if(isset($_POST["submitList"])) {
		// This sorts all POST-vars by agent id
		foreach($_POST as $varName => $postValue) {
			$string_array = explode("_", $varName);
			if(count($string_array) > 1){
				if(count($string_array) > 2) {
					$string_id = $string_array[2];
				} else {
					$string_id = $_POST["agentId_input"];
				}				
				if(!isset($query_array_{$string_id})) {
					$query_array_{$string_id} = array();
				}
				$field = $string_array[0] . "_" . $string_array[1];
				$postValue = trim(mysqli_prep($postValue));
				$query_array_{$string_id}[$field] = $postValue;
				$query_array[$string_id] = $query_array_{$string_id};
			}
		}
		// This combines every field in an id (row) into one query and runs the query
		foreach($query_array as $array_id => $agent_array) {
			$i = 1;
			$changefields = "";
			if(!isset($agent_array["active_check"])) {
				$agent_array["active_check"] = "off";
			}
			//print_r($agent_array);
			foreach($agent_array as $field => $postValue) {
				$field = mysqli_prep($field);
				$postValue = mysqli_prep($postValue);
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
			if(empty($errors)) {
				$result = mysqli_query($connection, $query);
				if(!$result) {
					echo "MySQL Query Failed: " . mysqli_error($connection);
				}
			} else {
				$editList = TRUE;
			}
		}
	}
	if(isset($_POST["submitForm"])) {
		if(empty($errors)) {
			
		} else {
			if($_POST["submitForm"] == "Add User") {
				$editAgent = TRUE;
				
			} elseif($_POST["submitForm"] == "Submit User") {
				$editAgent = TRUE;
			}
		}
	}
	// If validation fails:	
	if(isset($_POST["submitList"])) {
		$errorId_array = array();
		foreach($errors as $errorField => $errorName) {
				$errorString_array = explode("_", $errorField);
				$errorId = $errorString_array[2];
				$errorId_array[$errorId] = TRUE;
		}
	}
	$agent_set = get_agents("all");
	if(($addAgent == TRUE) || ($editAgent == TRUE)) {
		include("form.php");
		$output = $agentForm;
	} else {
		include("list.php");
		$output = $agentList;
	}
	echo $output;
?>