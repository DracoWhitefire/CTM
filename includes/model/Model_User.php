<?php
/**
 * Model_User
 */
class Model_User extends Model_Dao
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
		$sch_query .= "AND `user_id` = '{$selected_user}' ";
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
?>