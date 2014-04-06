<?php


/**
 * View_Team
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
