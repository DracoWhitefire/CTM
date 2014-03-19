<?php

/**
 * View_Rank
 */
Class View_Rank {
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
        $ranks_array = Model_Rank::get();
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
