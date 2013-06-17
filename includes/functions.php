<?php
	require_once("constants.php");
	function db_connect() {
		global $connection;
		$connection = mysql_connect(DB_SERVER , DB_USER, DB_PW);
		if(!$connection) {
			die("MySQL connection failed: " . mysql_error());
		}
		$db = mysql_select_db(DB_NAME, $connection);
		if(!$db) {
			die("Database selection failed: " . mysql_error());
		}
	}
	function get_all_subjects() {
		global $connection;
		$query  = "SELECT * ";
		$query .= "FROM subjects ";
		$query .= "WHERE visible = 1 ";
		$query .= "ORDER BY position ASC ";
		$nav_set = mysql_query($query, $connection);
		if(!$nav_set) {
			die("Query failed: " . mysql_error());
		}
		return $nav_set;
	}
	function get_selected_id() {
		global $current_id;
		if(isset($_GET["id"])) {
			settype($_GET["id"], "int");
			$current_id = $_GET["id"];
			
		} else {
			$current_id = 1;
		}
	}
	function get_subject_by_id($id) {
		global $connection;
		$query  = "SELECT * ";
		$query .= "FROM subjects ";
		$query .= "WHERE visible = 1 ";
		$set = mysql_query($query, $connection);
		$subj_total = mysql_num_rows($set);
		if(!((1 <= $id) && ($id <= $subj_total))) {
			$id = 1;
		}
		$query  = "SELECT * ";
		$query .= "FROM subjects ";
		$query .= "WHERE visible = 1 ";
		$query .= "AND id = " . mysql_prep($id) . " ";
		$query .= "LIMIT 1";
		$set = mysql_query($query, $connection);
		if(!$set) {
			die("Query failed: " . mysql_error());
		}
		$result = mysql_fetch_array($set);
		return $result;
	}
	function navigation($subject_set) {
		global $current_id;
		$output = "<ul>";
			While($row = mysql_fetch_array($subject_set, MYSQL_ASSOC)) {
				$output .= "<li";
				if($row["id"] == $current_id) {
					$output .= " class=\"selected\"";
				}
				$output .= "><a href=\"index.php?id=" . urlencode($row["id"]) . "\" >" . $row["menu_name"] . "</a></li>";
			}
		$output .= "</ul>";
		return $output;
	}
	function get_sch_for_agent($selected_agent, $day) {
	//function get_sch_for_agent($selected_agent, $day = "Wednesday") {
		global $connection;
		$selected_agent = mysql_prep($selected_agent);
		$day = mysql_prep($day);
		$sch_query  = "SELECT `start_time`, `end_time` ";
		$sch_query .= "FROM `sch_{$selected_agent}` ";
		$sch_query .= "WHERE `weekday` = '{$day}' ";
		$sch_set = mysql_query($sch_query, $connection);
		if(!$sch_set) {
			die("Query failed: " . mysql_error());
		}
		$result = mysql_fetch_array($sch_set, MYSQL_ASSOC);
		return $result;
	}
	function get_agents($selection = "all") {
		global $connection;
		$agent_query  = "SELECT `id`, 
								`user_name`, 
								`forum_name`, 
								`first_name`, 
								`last_name`, 
								`rank`, 
								`active` ";
		$agent_query .= "FROM `agents` ";
		if(is_numeric($selection)) {
			$agent_query .= "WHERE `id` = '{$selection}' ";
		} elseif($selection == "active") {
			$agent_query .= "WHERE `active` = '1' ";
		} elseif ($selection == "inactive") {
			$agent_query .= "WHERE `active` = '0' ";
		}		
		$agent_query .= "ORDER BY `id` ASC";
		$agent_set = mysql_query($agent_query, $connection);
		if(!$agent_set) {
			die("Query failed: " . mysql_error());
		}
		return $agent_set;
	}
	function get_selected_date() {
		$date_array = array();
		if(!isset($_GET["y"])) {
			$date_array["y"] = date("Y");
		} else {
			$date_array["y"] = urldecode($_GET["y"]);
		}
		if(!isset($_GET["m"])) {
			$date_array["m"] = date("m");
		} else {
			if((1 <= $_GET["m"]) && ($_GET["m"] <= 12)) {
				$date_array["m"] = urldecode($_GET["m"]);
			} else {
				$date_array["m"] = date("m");
			}
		}
		if(isset($_GET["d"])) {
			if((1<=$_GET["d"])&&($_GET["d"]<=cal_days_in_month(CAL_GREGORIAN, $date_array["m"], $date_array["y"]))) {
				$date_array["d"] = urldecode($_GET["d"]);
			} else {
				$date_array["d"] = date("d");
			}
		} else {
			$date_array["d"] = date("d");				
		}
		return $date_array;
	}
	function calendar($date){
		$selectedYear = $date["y"];
		$selectedMonth = $date["m"];
		$selectedDay = $date["d"];
		if($selectedMonth == 1) {
			$prevMonth = 12;
			$prevYear = $selectedYear - 1;
		} else {
			$prevMonth = $selectedMonth - 1;
			$prevYear = $selectedYear;
		}
		if($selectedMonth == 12) {
			$nextMonth = 1;
			$nextYear = $selectedYear + 1;
		} else {
			$nextMonth = $selectedMonth + 1;
			$nextYear = $selectedYear;
		}
		$firstDay = jddayofweek(cal_to_jd(CAL_GREGORIAN, $selectedMonth, 1, $selectedYear));
		$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
		$prevNumberOfDays = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
		$numberOfWeeks = ceil(($numberOfDays + $firstDay)/7);
		$daysLastMonthFirstWeek = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear)-$firstDay+1;
		$remainingLastMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear)-$daysLastMonthFirstWeek;
		$daysNextMonthLastWeek = cal_days_in_month(CAL_GREGORIAN, $nextMonth, $selectedYear);
		$output = "<div id=\"calendar_div\"><div id=\"month_select\">";
		$prevMonthNav = htmlspecialchars("?id={$_GET["id"]}&m=" . $prevMonth . "&y=" . $prevYear);
		$navLinks = "<div id=\"calPrev_div\"><a href=\"index.php{$prevMonthNav}\">Prev</a></div>";
		$nextMonthNav = htmlspecialchars("?id={$_GET["id"]}&m=" . $nextMonth . "&y=" . $nextYear);
		$navLinks .= "<div id=\"calNext_div\"><a href=\"index.php{$nextMonthNav}\">Next</a></div>";
		$navLinks .= "<div id=\"calCur_div\">" . date("F Y",strtotime($selectedDay . "-" . $selectedMonth . "-" . $selectedYear)) . "</div>";
		$output .= $navLinks . "</div>"; 
		// Beginning of the Table
		$output .= "<table id=\"calendar_table\">";
		$output .= "<thead><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr></thead>";
		$dayNo = $daysLastMonthFirstWeek;
		$output .= "<tbody>";
		for($weekNo = 1; $weekNo <= $numberOfWeeks; $weekNo++) {
			$output .= "<tr>";
			for($wDayNo = 1; $wDayNo <= 7; $wDayNo++) {
				$tdOutput1 = "<td class=\"";
				if(1 < $wDayNo && $wDayNo < 7) {
					$tdOutput1 .= "weekDay";
				}
				if($dayNo <= $prevNumberOfDays) {
					$urlMonth = $prevMonth;
					$urlYear = $prevYear;
					$tdOutput1 .= " prevMonth\">";
					$tdOutput2 = $dayNo;
				} elseif (($dayNo - $prevNumberOfDays) <= $numberOfDays) {
					$urlMonth = $selectedMonth;
					$urlYear = $selectedYear;
					if ($dayNo - $prevNumberOfDays == $selectedDay) {
						$tdOutput1 .= " selectedDay";
					}
					$tdOutput1 .= "\">";
					$tdOutput2 = ($dayNo - $prevNumberOfDays);
				} else {
					$urlMonth = $nextMonth;
					$urlYear = $nextYear;
					$tdOutput1 .= " nextMonth\">";
					$tdOutput2 = ($dayNo - $prevNumberOfDays - $numberOfDays);
				}
				$tdOutput3 = "</a></td>";
				$output .= $tdOutput1 . "<a href=\"index.php" . htmlspecialchars("?id={$_GET["id"]}&d={$tdOutput2}&m={$urlMonth}&y={$urlYear}") . "\" >" . $tdOutput2 . $tdOutput3;
				$dayNo++;
			}
			$output .= "</tr>";
		}
		$output .= "</tbody></table></div>";
		return $output;	
	}
	function mysql_prep($value) {
		$magic_quotes_active = get_magic_quotes_gpc();
		$php_uptodate = function_exists("mysql_real_escape_string");
		if($php_uptodate) {
			if($magic_quotes_active) {
				$value = mysql_real_escape_string(stripslashes($value));
			}
		} else {
			if(!$magic_quotes_active) {
				$value = addslashes($value);
			}
		}
		return $value;
	}
	function form_val_required($val_req_array) {
		global $errors;
		$val_errors_req_array = array();
		foreach($val_req_array as $fieldName) {
			if(!isset($_POST[$fieldName]) || ((empty($_POST[$fieldName])) && !(is_numeric($_POST[$fieldName])))) {
				//$val_errors_req_array[$fieldName] = "error_req";
				$errors[$fieldName] = "error_req";
			}
		}
		
	}
	function form_val_length($val_len_array) {
		global $errors;
		$val_errors_len_array = array();
		foreach($val_len_array as $fieldName => $minmax) {
			$string_array = explode("-", $minmax);
			$min = $string_array["0"];
			$max = $string_array["1"];
			if((strlen(trim($_POST[$fieldName])) < $min) || (strlen(trim($_POST[$fieldName])) > $max)) {
				//$val_errors_len_array[] = $fieldName;
				if(!isset($errors[$fieldName])) {
					$errors[$fieldName] = "error_len";
					//echo "ERROR";
				}
			}
		}
		return $val_errors_len_array;
	}
	function form_val_num($checkNum_array) {
		global $errors;
		$val_errors_num_array = array();
		foreach($checkNum_array as $fieldName) {
			if(!is_numeric($_POST[$fieldName]))
			$errors[$fieldName] = "error_num";
		}
	}
	function form_val_unique($val_uniq_array) {
		global $connection;
		global $errors;
		$user_query  = "SELECT `user_name`, `id` ";
		$user_query .= "FROM `agents` ";
		$user_set = mysql_query($user_query);
		if(!$user_set) {
			die("Query failed: " . mysql_error());
		}
		while($user_row = mysql_fetch_array($user_set, 1)) {
			$id = $user_row["id"];
			$user_array[$id] = $user_row["user_name"];
		}
		foreach($val_uniq_array as $fieldName) {
			$fieldName_array = preg_split("/([A-Z][a-z]+)|_/", $fieldName, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			foreach($user_array as $id => $name) {
				if($name == $_POST[$fieldName]) {
					if(is_numeric($fieldName_array[2])) {
						if($fieldName_array[2] != $id) {
							$errors[$fieldName] = "error_unique";
							break 1;
						}
					} elseif($fieldName_array[2] == "input") {
						if($_POST["agentId_input"] != $id) {
							$errors[$fieldName] = "error_unique";
							break 1;
						}
					}
					
				}
			}
		}
	}
	function form_val_time($val_time_array) {
		global $errors;
		foreach($val_time_array as $fieldName) {
			if(preg_match("/[0-9]{2}:[0-9]{2}:[0-9]{2}?/", $_POST[$fieldName]) == 0) {
				$errors[$fieldName] = "error_time";
			}
		}
	}
	function convert_rank($rank) {
		$convRank = "";
		if(is_numeric($rank)) {
			if((1 <= $rank) && ($rank < 10)) {
				$convRank = "Guest";
			} elseif ((10 <= $rank) && ($rank < 50)) {
				$convRank = "Agent";
			} elseif ((50 <= $rank) && ($rank < 100)) {
				$convRank = "Admin";
			} elseif ($rank == 100) {
				$convRank = "Superadmin";
			}
		} else {
			if ($rank == "Guest") {
				$convRank = 1;
			} elseif ($rank == "Agent") {
				$convRank = 10;
			} elseif ($rank == "Admin") {
				$convRank = 50;
			} elseif ($rank == "Superdmin") {
				$convRank = 50;
			}
		}
		return $convRank;
	}
?>