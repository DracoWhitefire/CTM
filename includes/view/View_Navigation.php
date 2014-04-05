<?php

/**
 * Navigation class to create menu
 */
Class View_Navigation 
{
    
    static function menu() {
        global $currentId;
        $db = call_user_func(DB_CLASS . "::getInstance");
        global $session;
        $output = "";
        
        //Check if user session exists
        if($session->is_loggedIn()) {
            $output .= "<ul>";
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
            $output .= "</ul>";
        }
        return $output;
    }
}