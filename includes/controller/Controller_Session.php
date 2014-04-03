<?php

/**
 * Description of Controller_Session
 */
class Controller_Session
{
    private $_loggedIn;
    public $userId;
    public $firstName;
    public $rank;
    public $team;

    function __construct() {
        session_start();
        $this->_check_login();
    }
    
    private function _check_login() {
        if(isset($_SESSION["id"])) {
            $this->userId = $_SESSION["id"];
            $this->_loggedIn = TRUE;
            $this->firstName = $_SESSION["firstname"];
            $this->rank = $_SESSION["rank"];
            $this->team = $_SESSION["team"];
        } else {
            unset($this->userId);
            $this->_loggedIn = FALSE;
        }
    }	
    
    public function is_loggedIn() {
        return $this->_loggedIn;
    }
    
    public function login(Model_User $user) {
        if($user) {
            $this->userId = $_SESSION["id"] = $user->id;
            $this->firstName = $_SESSION["firstname"] = $user->firstName;
            $this->rank = $_SESSION["rank"] = $user->rank;
            $this->team = $_SESSION["team"] = $user->team;
            $this->_loggedIn = TRUE;
        }
    }
    
    public function logout() {
        unset($_SESSION["id"]);
        unset($this->userId);
        $this->_loggedIn = FALSE;
    }
}