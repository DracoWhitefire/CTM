<?php
require_once("config.php");

//database functions
class MySqlDatabase
{
		private $connection;
		private $magic_quotes_active;
		private $mysqli_real_escape_string_exists;
		public $last_query;
		
		function __construct() {
			$this->connect();
			$this->magic_quotes_active = get_magic_quotes_gpc();
			$this->mysqli_real_escape_string_exists = function_exists("mysqli_real_escape_string");
		}
		
		private function connect() {
			$this->connection = mysqli_connect(DB_SERVER , DB_USER, DB_PW, DB_NAME);
			if($this->connection->connect_errno) {
				die("MySQL connection failed: " . $this->connection->connect_error . " (" . $this->connection->connect_errno . ") ");
			}
		}
		public function disconnect() {
			if(isset($this->connection)) {
				mysqli_close($this->connection);
				unset($this->connection);
			}
		}
		
 		public function query_prep($value) {
			if($this->mysqli_real_escape_string_exists) {
				if($this->magic_quotes_active) {
					$value = $this->connection->real_escape_string(stripslashes($value));
				}
			} else {
				if(!$this->magic_quotes_active) {
					$value = $this->connection->real_escape_string($value);
				}
			}
			return trim($value);
		}
		public function fetch_assoc($result) {
			return mysqli_fetch_assoc($result);
		}
		public function num_rows($result) {
			return mysqli_num_rows($result);
		}
		public function insert_id() {
			return mysqli_insert_id($this->connection);
		}
		public function affected_rows() {
			return mysqli_affected_rows($this->connection);
		}
		
		private function mysqli_confirm($result) {
			global $debug;
			$message = "Database query failed: " . $this->connection->error;
			if($debug == TRUE) {
				$message .= "<br />" . $this->last_query;
			}
			if(!$result) {
				die($message);
			}
		}
		public function query($sql) {
			$this->last_query = $sql;
			$result = mysqli_query($this->connection, $sql);
			$this->mysqli_confirm($result);
			
			return $result;
		}
	}
class Dao
{
	public $id;
	
	private static function instantiate(array $row) {
		$object = new static;
		foreach($row as $attribute => $value) {
			$converted_attribute = preg_replace("/^(\w+?)_(\w+?)$/e", "\"$1\" . ucfirst(\"$2\")", $attribute);
			if($object->has_attribute($converted_attribute)) {
				$object->$converted_attribute = $value;
			}
		}
		return $object;
	}
	private function has_attribute($attribute) {
		$vars = get_object_vars($this);
		return array_key_exists($attribute, $vars);
	}
	public static function get_by_query($query) {
		global $db;
		$object_array = array();
		$result_set = $db->query($query);
		while($row = $db->fetch_assoc($result_set)) {
			$object_array[] = static::instantiate($row);
		}
		mysqli_free_result($result_set);
		if(count($object_array) == 1) {
			return $object_array[0];
		} else {
			return $object_array;
		}
	}

	protected function create() {
		global $db;
		$vars = get_object_vars($this);
		$query  = "INSERT INTO `" . $table . "` (";
		$count = 0;
		foreach($vars as $var->$value) {
			$count += 1;
			$query .= $db->query_prep($var);
			if ($count < count($vars)) {
				$query .= ", ";
			} else {
				$query .= ")";
			}
		}
		$query .= " VALUES (";
		foreach($vars as $var->$value) {
			$count += 1;
			$query .= $db->query_prep($value);
			if ($count < count($vars)) {
				$query .= ", ";
			} else {
				$query .= ")";
			}
		}
		echo $query;
	}
	protected function update() {
		global $db;
	}
	public function save() {
		return isset($this->id) ? $this->update() : $this->create();
	}
	public function delete() {
		global $db;
	}
}

//navigation functions
Class Subject extends Dao
{
	public $id;
	public $minRank;
	public $visible;
	public $menuName;
	
	

	public static function get($selection = "all") {
		global $db;
		global $session;
		$loginId = 7;
		if($selection == "all") {
			$query  = "SELECT * ";
			$query .= "FROM `subjects` ";
			$query .= "WHERE `visible` = 1 ";
			$query .= "ORDER BY `position` ASC ";
			return self::get_by_query($query);
		} elseif(is_numeric($selection)) {
			if(isset($session->rank)) {
				$query  = "SELECT * ";
				$query .= "FROM `subjects` ";
				$set = $db->query($query);
				$subj_total = $db->num_rows($set);
				mysqli_free_result($set);
				if(!((1 <= $selection) && ($selection <= $subj_total))) {
					$selection = 1;
				}
				$query  = "SELECT * ";
				$query .= "FROM `subjects` ";
				$query .= "WHERE `id` = '" . $db->query_prep($selection) . "' ";
				$query .= "LIMIT 1";
				$object = self::get_by_query($query);
				if($object->minRank <= $session->rank) {
					return $object;
				} else {
					return "error";
				}
			} else {
				$query  = "SELECT * ";
				$query .= "FROM `subjects` ";
				$query .= "WHERE `id` = '{$loginId}' ";
				$query .= "LIMIT 1";
				$object = self::get_by_query($query);
				return $object;
			}
		}
	}
	public static function get_current_id() {
		if(isset($_GET["id"])) {
			(integer) $current_id = (int) $_GET["id"];
		} else {
			(integer) $current_id = (int) 1;
		}
		return $current_id;
	}
}
function navigation() {
		//requires the (result of the) function get_all_subjects()
		//requires the function get_selected_id()
		global $current_id;
		global $db;
		global $session;
		
		if($session->is_loggedIn()) {
			$output = "<ul>";
			$subjects_array = Subject::get();
			foreach($subjects_array as $subject) {
				if($subject->visible == 1 && $subject->minRank <= $session->rank) {
					$output .= "<li";
					if($subject->id == $current_id) {
						$output .= " class=\"selected\"";
					}
					$output .= "><a href=\"" . htmlspecialchars("index.php?id=" . urlencode($subject->id)) . "\" >" . htmlspecialchars($subject->menuName) . "</a></li>";
				}
			}
			$output .= "</ul>";
			return $output;
		} else {
			return NULL;
		}
	}

//user functions
class User extends Dao
{
	public $id;
	public $userName;
	public $forumName;
	public $firstName;
	public $lastName;
	public $rank;
	public $active;
	public $passwordhash;
	public $team;
	public $employeeNr;
	
	public static function get($selection = "all") {
		$user_query  = "SELECT * ";
		$user_query .= "FROM `users` ";
		if(is_numeric($selection)) {
			$user_query .= "WHERE `id` = '{$selection}' ";
		} elseif($selection == "active") {
			$user_query .= "WHERE `active` = '1' ";
		} elseif ($selection == "inactive") {
			$user_query .= "WHERE `active` = '0' ";
		}
		$user_query .= "ORDER BY `id` ASC";
		return self::get_by_query($user_query);
	}
	
	public static function get_by_team($team) {
		global $db;
		$team = $db->query_prep($team);
		$user_query  = "SELECT * ";
		$user_query .= "FROM `users` ";
		$user_query .= "WHERE `team` = '{$team}' ";
		$user_query .= "AND `active` = '1' ";
		$user_query .= "ORDER BY `id` ASC";
		return self::get_by_query($user_query);
	}
	
	public function get_sch($day) {
		global $db;
		$selected_user = $db->query_prep($this->id);
		$day = $db->query_prep($day);
		$sch_query  = "SELECT `start_time`, `end_time` ";
		$sch_query .= "FROM `schedules` ";
		$sch_query .= "WHERE `weekday` = '{$day}' ";
		$sch_query .= "AND `id` = '{$selected_user}' ";
		$sch_set = $db->query($sch_query);
		$result_array = $db->fetch_assoc($sch_set);
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
	public function pw_check($pw_string) {
		$hash = crypt($pw_string, $this->passwordhash);
		if($hash === $this->passwordhash) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
class Rank extends Dao
{
	public $id;
	public $value;
	public $name;
	
	public static function get($selection = "all") {
		global $db;
		$query  = "SELECT MAX(`value`) ";
		$query .= "FROM `ranks` ";
		$result = $db->query($query);
		$maxValue = $db->fetch_assoc($result)["MAX(`value`)"];
		mysqli_free_result($result);
		$query  = "SELECT * ";
		$query .= "FROM `ranks` ";
		if($selection == "all") {
			return self::get_by_query($query);
		} elseif(is_numeric($selection)) {	
			if((1 <= $selection) && ($selection <= $maxValue)) {
				$selection = $db->query_prep($selection);
			} else {
				$selection = 1;
			}
			$query .= "WHERE `value` = {$selection} ";
			$query .= "LIMIT 1 ";
			return self::get_by_query($query);
		}
	}

	public static function selector($user = "") {
		$output = "<select id=\"" . htmlspecialchars("rank_select");
		if($user!="") {
			$output .= htmlspecialchars("_" . $user->id);
		}
		$output .= "\" name=\"" . htmlspecialchars("rank_select");
		if($user!="") {
			$output .= htmlspecialchars("_" . $user->id);
		}
		$output .= "\">";
		$ranks_array = self::get();
		foreach($ranks_array as $rank) {
			$output .= "<option value=\"" . htmlspecialchars($rank->value) . "\" ";
				if($user!="") {
					if($user->rank == $rank->value) {
						$output .= "selected=\"selected\" ";
					}
				}
			$output .= ">" . htmlspecialchars($rank->name) . "</option>";
		}
		$output .= "</select>";
		return $output;
	}
}
class Team extends Dao
{
	public $id;
	public $project;
	public $name;
	public $count;
	
	public static function get($selection = "all") {
		global $db;
		$query  = "SELECT * ";
		$query .= "FROM `teams` ";
		$result = $db->query($query);
		$numRows = $db->num_rows($result);
		mysqli_free_result($result);		
		if($selection == "all") {
			return self::get_by_query($query);
		} elseif(is_numeric($selection)) {
			if((1 <= $selection) && ($selection <= $numRows)) {
				$selection = $db->query_prep($selection);
			} else {
				$selection = 2;
			}			
			$query .= "WHERE `id` = {$selection} ";
			return self::get_by_query($query);
		}		
	}

	public static function selector($selectedTeam) {
		$output = "<select id=\"teamSelect\" name=\"teamSelect\" >";
		$teams_array = self::get();
		foreach($teams_array as $team) {
			$output .= "<option value=\"" . $team->id . "\" ";
			if($team->id == $selectedTeam) {
				$output .= "selected=\"selected\" ";
			}
			$output .= "\">" . $team->name . "</option>";
		}
		$output .= "</select>";
		return $output;
	}
}
class Session
{
	private $loggedIn;
	public $userId;
	public $firstName;
	public $rank;
	public $team;
			
	function __construct() {
		session_start();
		$this->check_login();
	}
	private function check_login() {
		if(isset($_SESSION["id"])) {
			$this->userId = $_SESSION["id"];
			$this->loggedIn = TRUE;
			$this->firstName = $_SESSION["firstname"];
			$this->rank = $_SESSION["rank"];
			$this->team = $_SESSION["team"];
		} else {
			unset($this->userId);
			$this->loggedIn = FALSE;
		}
	}	
	public function is_loggedIn() {
		return $this->loggedIn;
	}
	public function login(User $user) {
		if($user) {
			$this->userId = $_SESSION["id"] = $user->id;
			$this->firstName = $_SESSION["firstname"] = $user->firstName;
			$this->rank = $_SESSION["rank"] = $user->rank;
			$this->team = $_SESSION["team"] = $user->team;
			$this->loggedIn = TRUE;
		}
	}
	public function logout() {
		unset($_SESSION["id"]);
		unset($this->userId);
		$this->loggedIn = FALSE;
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
class Validator
{
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
		global $db;
		$user_query  = "SELECT `user_name`, `id` ";
		$user_query .= "FROM `users` ";
		$user_set = $db->query($user_query);
		while($user_row = $db->fetch_assoc($user_set)) {
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
			if(preg_match("/^(0(?=\d)|1(?=\d)|2(?=[0-3]))?\d:[0-5]\d(:[0-5]\d)?$/", $_POST[$fieldName]) == 0) {
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

?>