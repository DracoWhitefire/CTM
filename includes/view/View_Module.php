<?php

/**
 * Navigation class to create menu
 */
Class View_Module 
{
    static function menu() {
        global $currentId;
        global $session;
        $output = "";
        if($session->isLoggedIn()) {
            $output .= "<nav><ul>";
            $modulesArray = Model_Module::get();
            foreach($modulesArray as $module) {
                //Only generate menu items for visible modules allowed by rank
                if($module->visible == 1 && $module->minRank <= $session->getRank()) {
                    $output .= "<li";
                    if($module->id == $currentId) {
                        $output .= " class=\"selected\"";
                    }
                    $output .= "><a href=\"" . htmlspecialchars("index.php?id=" . urlencode($module->id)) . "\" >" . htmlspecialchars($module->menuName) . "</a></li>";
                }
            }
            $output .= "</ul></nav>";
        }
        return $output;
    }
}