<?php
	$errors = array();
	if(isset($_POST["cancelEditList"])) {
		$_POST = "";
	}
	if(isset($_POST["editList"])) {
		if(count($_POST) > 1) {
			$editList = TRUE;
			$addAgent = FALSE;
		} else {
			$editList = FALSE;
			$addAgent = FALSE;
		}
	} else {
		$editList = FALSE;
		if(isset($_POST["addList"])) {
			$addAgent = TRUE;
		} else {
			$addAgent = FALSE;
		}
	}
	if(isset($_POST["submitList"])) {
		
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
				$field = mysql_prep($field);
				$postValue = mysql_prep($postValue);
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
				$editList = TRUE;
			}
		}
	}
	
	if(isset($_POST["submitForm"])) {
		
		//Form Validation
		$checkReq_array = array();
		$checkLen_array = array();
		$checkNum_array = array();
		foreach($_POST as $valField => $val) {
			$checkReq_array[] = $valField;
		}
		form_val_required($checkReq_array);
		print_r($errors);
		
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
	//print_r($errorId_array);
	//print_r($errors);
	
	
	$agent_set = get_agents("all");

	if($addAgent == FALSE) {
		include("list.php");
		$output = $agentList;
	} else {
		include("form.php");
		$output = $agentForm;
	}
	echo $output;
?>