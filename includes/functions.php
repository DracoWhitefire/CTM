<?php
require_once("config.php");

function __autoload($className) {
    $classArray = explode("_", $className);
    $classPath = $classArray[0] . "/" . $className . ".php";
    include($classPath);
	public $id;
	
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

$debug = TRUE;
?>