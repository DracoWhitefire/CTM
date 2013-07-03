<?php
	require_once("constants.php");
	
//database functions
	function db_connect() {
		global $connection;
		$connection = mysqli_connect(DB_SERVER , DB_USER, DB_PW, DB_NAME);
		if(mysqli_connect_errno()) {
			die("MySQL connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ") ");
		}
	}
	function mysqli_prep($value) {
		$magic_quotes_active = get_magic_quotes_gpc();
		$php_uptodate = function_exists("mysqli_real_escape_string");
		if($php_uptodate) {
			if($magic_quotes_active) {
				$value = mysqli_real_escape_string($connection, stripslashes($value));
			}
		} else {
			if(!$magic_quotes_active) {
				$value = mysqli_real_escape_string($connection, $value);
			}
		}
		return trim($value);
	}
	function mysqli_confirm($result_set) {
		global $connection;
		if(!$result_set) {
			die("Database query failed: " . mysqli_error($connection));
		}
	}
	
//navigation functions
	function get_all_subjects() {
		global $connection;
		$query  = "SELECT * ";
		$query .= "FROM `subjects` ";
		$query .= "WHERE `visible` = 1 ";
		$query .= "ORDER BY `position` ASC ";
		$nav_set = mysqli_query($connection, $query);
		mysqli_confirm($nav_set);
		return $nav_set;
	}
	function get_selected_id() {
		if(isset($_GET["id"])) {
			(integer) $current_id = (int) $_GET["id"];
		} else {
			(integer) $current_id = (int) 1;
		}
		return $current_id;
	}
	function get_subject_by_id($id) {
		global $connection;
		if(isset($_SESSION["rank"])) {
			$query  = "SELECT * ";
			$query .= "FROM `subjects` ";
			$set = mysqli_query($connection, $query);
			mysqli_confirm($set);
			$subj_total = mysqli_num_rows($set);
			mysqli_free_result($set);
			if(!((1 <= $id) && ($id <= $subj_total))) {
				$id = 1;
			}
			$query  = "SELECT * ";
			$query .= "FROM `subjects` ";
			$query .= "WHERE `id` = '" . mysqli_prep($id) . "' ";
			$query .= "LIMIT 1";
			$set = mysqli_query($connection, $query);
			mysqli_confirm($set);
			$result = mysqli_fetch_assoc($set);
			mysqli_free_result($set);
			if($result["min_rank"] <= $_SESSION["rank"]) {
				return $result;
			}
		} else {
			$query  = "SELECT * ";
			$query .= "FROM `subjects` ";
			$query .= "WHERE `id` = '7' ";
			$query .= "LIMIT 1";
			$set = mysqli_query($connection, $query);
			mysqli_confirm($set);
			$result = mysqli_fetch_assoc($set);
			mysqli_free_result($set);
			return $result;
		}
	}
	function navigation($subject_set) {
		//requires the (result of the) function get_all_subjects()
		//requires the function get_selected_id()
		global $current_id;
		if(isset($_SESSION["id"])) {
			$output = "<ul>";
			While($row = mysqli_fetch_array($subject_set, MYSQL_ASSOC)) {
				if($row["visible"] == TRUE && $row["min_rank"] <= $_SESSION["rank"]) {
					$output .= "<li";
					if($row["id"] == $current_id) {
						$output .= " class=\"selected\"";
					}
					$output .= "><a href=\"" . htmlspecialchars("index.php?id=" . urlencode($row["id"])) . "\" >" . htmlspecialchars($row["menu_name"]) . "</a></li>";
				}
			}
			$output .= "</ul>";
			return $output;
		} else {
			return NULL;
		}
	}
	
//user functions
	class user {
		public $id;
		public $userName;
		public $forumName;
		public $firstName;
		public $lastName;
		public $rank;
		public $active;
		public $password;
		
		private static function instantiate(array $row) {
			$object = new self;
			foreach($row as $attribute => $value) {
				$converted_attribute = preg_replace("/^(\w+?)_(\w+?)$/e", "\"$1\" . ucfirst(\"$2\")", $attribute);
				if($object->has_attribute($converted_attribute)) {
					$object->$converted_attribute = $value;
				}
			}
			return $object;
		}
		public static function get($selection = "all") {
			global $connection;
			$object_array = array();
			$user_query  = "SELECT 	`id`, 
									`user_name`, 
									`forum_name`, 
									`first_name`, 
									`last_name`, 
									`rank`, 
									`active` ";
			$user_query .= "FROM `users` ";
			if(is_numeric($selection)) {
				$user_query .= "WHERE `id` = '{$selection}' ";
			} elseif($selection == "active") {
				$user_query .= "WHERE `active` = '1' ";
			} elseif ($selection == "inactive") {
				$user_query .= "WHERE `active` = '0' ";
			}		
			$user_query .= "ORDER BY `id` ASC";
			$user_set = mysqli_query($connection, $user_query);
			mysqli_confirm($user_set);
			while($row = mysqli_fetch_assoc($user_set)) {
				$object_array[] = self::instantiate($row);
			}
			mysqli_free_result($user_set);
			return $object_array;
		}
		private function has_attribute($attribute) {
			$vars = get_object_vars($this);
			return array_key_exists($attribute, $vars);
		}
		public function get_sch($day) {
			global $connection;
			$selected_user = mysqli_prep($this->id);
			$day = mysqli_prep($day);
			$sch_query  = "SELECT `start_time`, `end_time` ";
			$sch_query .= "FROM `schedules` ";
			$sch_query .= "WHERE `weekday` = '{$day}' ";
			$sch_query .= "AND `id` = '{$selected_user}' ";
			$sch_set = mysqli_query($connection, $sch_query);
			mysqli_confirm($sch_set);
			$result_array = mysqli_fetch_array($sch_set, MYSQL_ASSOC);
			mysqli_free_result($sch_set);
			return $result_array;
		}		
		
		private static function generate_salt($length) {
			return substr(str_replace("+", ".", base64_encode(md5(uniqid(mt_rand(), TRUE)))),0, $length);
		}
		public static function pw_encrypt($pw_string) {
			$hashFormat = "$2y$10$";
			$saltLength = 22;
			$hashSalt = self::generate_salt($saltLength);
			$hashFormatSalt = $hashFormat . $hashSalt;
			$hashPw = crypt(trim($pw_string), $hashFormatSalt);
			return $hashPw;
		}
		public static function pw_check($pw_string, $existing_hash) {
			$hash = crypt($pw_string, $existing_hash);
			if($hash === $existing_hash) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
//date functions
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
	function date_to_url($year = "", $month = "", $day = "") {
		$urlQueries_array = array();
		$urlQueries = explode("&", $_SERVER["QUERY_STRING"]);
		foreach($urlQueries as $urlQuery) {
			$query_array = explode("=", $urlQuery);
			$urlQueries_array[$query_array[0]] = $query_array[1];
		}
		$urlQueries_array["y"] = 	!empty($year) 
									? urlencode($year) 
									: (isset($urlQueries_array["y"]) 
										? $urlQueries_array["y"]
										: date("Y"));
		$urlQueries_array["m"] = 	!empty($month) 
									? urlencode($month) 
									: (isset($urlQueries_array["m"]) 
										? $urlQueries_array["m"]
										: date("n"));
		$urlQueries_array["d"] = 	!empty($day) 
									? urlencode($day) 
									: (isset($urlQueries_array["d"]) 
										? $urlQueries_array["d"]
										: date("j"));
		$url = $_SERVER["PHP_SELF"] . "?" . http_build_query($urlQueries_array);
		return $url;
	}
	function calendar($date = ""){
		//requires the function date_to_url()
		$selectedYear = isset($date["y"]) ? $date["y"] : date("Y");
		$selectedMonth = isset($date["m"]) ? $date["m"] : date("n");
		$selectedDay = isset($date["d"]) ? $date["d"] : date("j");
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
		//$prevMonthNav = "index.php" . "?id=" . urlencode($_GET["id"]) . "&m=" . urlencode($prevMonth) . "&y=" . urlencode($prevYear);
		$prevMonthNav = date_to_url($prevYear, $prevMonth);
		$navLinks = "<div id=\"calPrev_div\"><a href=\"" . htmlspecialchars($prevMonthNav) . "\">Prev</a></div>";
		
		//$nextMonthNav = "index.php" . "?id=" . urlencode($_GET["id"]) . "&m=" . urlencode($nextMonth) . "&y=" . urlencode($nextYear);
		$nextMonthNav = date_to_url($nextYear, $nextMonth);
		$navLinks .= "<div id=\"calNext_div\"><a href=\"" . htmlspecialchars($nextMonthNav) . "\">Next</a></div>";
		
		$navLinks .= "<div id=\"calCur_div\">" . htmlspecialchars(date("F Y",strtotime($selectedDay . "-" . $selectedMonth . "-" . $selectedYear))) . "</div>";
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
				$dateUrl = date_to_url($urlYear, $urlMonth, $tdOutput2);
				$output .= $tdOutput1 . "<a href=\"" . htmlspecialchars($dateUrl) . "\" >" . $tdOutput2 . $tdOutput3;
				$dayNo++;
			}
			$output .= "</tr>";
		}
		$output .= "</tbody></table></div>";
		return $output;	
	}
	
//time functions
	function format_time($time_string, $target) {
		if($target == "db") {
			$tempresult = preg_replace("/^([0-9]:[0-9]{2})(:[0-9]{2})?$/", "0\\1\\2", $time_string);
			$result = preg_replace("/^([0-9]{2}:[0-9]{2})$/", "\\1:00", $tempresult);
		} elseif ($target == "html") {
			$result = preg_replace("/^(0)?([1-9]?[0-9]:[0-9]{2})(:[0-9]{2})$/", "\\2", $time_string);			
		}
		return htmlspecialchars($result);
	}

//form validation functions
	class validator {
		public $errors;
		
		public function required($val_req_array) {
			foreach($val_req_array as $fieldName) {
				if(!isset($_POST[$fieldName]) || ((empty($_POST[$fieldName])) && !(is_numeric($_POST[$fieldName])))) {
					$this->errors[$fieldName] = "error_req";
				}
			}
			
		}								
		public function length($val_len_array) {
			foreach($val_len_array as $fieldName => $minmax) {
				$string_array = explode("-", $minmax);
				$min = $string_array["0"];
				$max = $string_array["1"];
				if((strlen(trim($_POST[$fieldName])) < $min) || (strlen(trim($_POST[$fieldName])) > $max)) {
					if(!isset($this->errors[$fieldName])) {
						$this->errors[$fieldName] = "error_len";
					}
				}
			}
		}												
		public function numeric($checkNum_array) {
			foreach($checkNum_array as $fieldName) {
				if(!is_numeric($_POST[$fieldName])) {
					if(!isset($this->errors[$fieldName])) {
						$this->errors[$fieldName] = "error_num";	
					}
				}
			}
		}									
		public function unique($val_uniq_array) {
			global $connection;
			$user_query  = "SELECT `user_name`, `id` ";
			$user_query .= "FROM `users` ";
			$user_set = mysqli_query($connection, $user_query);
			mysqli_confirm($user_set);
			while($user_row = mysqli_fetch_assoc($user_set)) {
				$id = (int) $user_row["id"];
				$user_array[$id] = $user_row["user_name"];
			}
			mysqli_free_result($user_set);
			foreach($val_uniq_array as $fieldName) {
				$fieldName_array = preg_split("/([A-Z][a-z]+)|_/", $fieldName, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
				foreach($user_array as $id => $name) {
					if($name == $_POST[$fieldName]) {
						if(is_numeric($fieldName_array[2])) {
							if($fieldName_array[2] != $id) {
								if(!isset($this->errors[$fieldName])) {
									$this->errors[$fieldName] = "error_unique";	
								}
								break 1;
							}
						} elseif($fieldName_array[2] == "input") {
							if(isset($_POST["userId_input"])) {
								if($_POST["userId_input"] != $id) {
									if(!isset($this->errors[$fieldName])) {
										$this->errors[$fieldName] = "error_unique";	
									}
									break 1;
								}
							}
						}
					}
				}
			}
		}
		public function time($val_time_array) {
			foreach($val_time_array as $fieldName) {
				if(preg_match("/^([0](?=[0-9])|1(?=[0-9])|2(?=[0-3]))?[0-9]:[0-5][0-9](:[0-5][0-9])?$/", $_POST[$fieldName]) == 0) {
					if(!isset($this->errors[$fieldName])) {
						$this->errors[$fieldName] = "error_time";	
					}
				}
			}
		}									
		public function timediff($val_timediff_array) {
			//requires the function format_time()
			foreach($val_timediff_array as $startTime_fieldname => $endTime_fieldname) {
				$startTime = (float) strtotime(format_time($_POST[$startTime_fieldname], "db"));
				$endTime = (float) strtotime(format_time($_POST[$endTime_fieldname], "db"));
				if(($endTime - $startTime) < 0) {
					if(!isset($this->errors[$startTime_fieldname]) && (!isset($this->errors[$endTime_fieldname]))) {
						$this->errors[$startTime_fieldname] = "error_timediff";	
						$this->errors[$endTime_fieldname] = "error_timediff";
					}
				}
			}
		}													
		public function password($val_pw_array) {
			foreach($val_pw_array as $pw_field) {
				$success = preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s]).{8,20}$/", trim($_POST[$pw_field])); 
				if(!$success) {
					if(!isset($this->errors[$pw_field])) {
						$this->errors[$pw_field] = "error_pw";
					}
				}
			}
		}										
		public function compare($val_compare_array) {
			foreach($val_compare_array as $firstField => $secondField) {
				if($_POST[$firstField] !== $_POST[$secondField]) {
					if(!isset($this->errors[$firstField]) && !isset($this->errors[$secondField])) {
						$this->errors[$firstField] = "error_compare";
						$this->errors[$secondField] = "error_compare";
					}
				}
			}
		}										
	}
//general functions
	function convert_rank($rank) {
		$convRank = "";
		if(is_numeric($rank)) {
			if((1 <= $rank) && ($rank < 10)) {
				$convRank = "Guest";
			} elseif ((10 <= $rank) && ($rank < 50)) {
				$convRank = "User";
			} elseif ((50 <= $rank) && ($rank < 100)) {
				$convRank = "Admin";
			} elseif ($rank == 100) {
				$convRank = "Superadmin";
			}
		} else {
			if ($rank == "Guest") {
				$convRank = 1;
			} elseif ($rank == "User") {
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