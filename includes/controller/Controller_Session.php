<?php

/**
 * Controller_Session
 */
class Controller_Session
{
    private $_loggedIn;
    private $_rank;
    public $userId;
    public $firstName;
    public $team;

    function __construct() {
        session_start();
        $this->_checkLogin();
    }
    
    /**
     * Checks whether user is logged in and sets session properties;
     */
    private function _checkLogin() {
        if(isset($_SESSION["id"])) {
            $this->userId = $_SESSION["id"];
            $this->_loggedIn = TRUE;
            $this->firstName = $_SESSION["firstname"];
            $this->setRank($_SESSION["rank"]);
            $this->team = $_SESSION["team"];
        } else {
            unset($this->userId);
            $this->_loggedIn = FALSE;
        }
    }	
    
    /**
     * Returns whether user is logged in;
     * @return bool - Is user logged in?
     */
    public function is_loggedIn() {
        return $this->_loggedIn;
    }
    
    /**
     * Logs in user $user;
     * @param Model_User $user - the user to log in
     */
    public function login(Model_User $user) {
        if($user) {
            $this->userId = $_SESSION["id"] = $user->id;
            $this->firstName = $_SESSION["firstname"] = $user->firstName;
            $this->setRank($user->rank);
            $this->team = $_SESSION["team"] = $user->team;
            $this->_loggedIn = TRUE;
        }
    }
    
    /**
     * Gets the set rank or sets it to 0 if not set;
     * @return int - the rank
     */
    public function getRank() {
        !isset($this->_rank) ? $this->setRank(0) : NULL;
        return $this->_rank;
    }
    
    /**
     * Sets the rank to $rank;
     * @param int $rank - the rank to be set
     */
    public function setRank($rank) {
        $this->_rank = $_SESSION["rank"] = $rank;
    }
    
    /**
     * Logs out current user;
     */
    public function logout() {
        unset($_SESSION["id"]);
        unset($this->userId);
        $this->_loggedIn = FALSE;
    }
}