<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View_Team
 *
 * @author deckers.5
 */
class View_Team {
    public static function selector($selectedTeam) {
        $output = "<select id=\"teamSelect\" name=\"teamSelect\" >";
        $teamsArray = Model_Team::get();
        foreach($teamsArray as $team) {
            $output .= "<option value=\"" . $team->id . "\" ";
            if($team->id == $selectedTeam) {
                $output .= "selected=\"selected\" ";
            }
            $output .= " >" . $team->name . "</option>";
        }
        $output .= "</select>";
        return $output;
    }
}
