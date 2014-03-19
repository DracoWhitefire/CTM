<?php
    if(isset($_POST["edit"])) {
        $editing = TRUE;
    } else {
        $editing = FALSE;
    }
    if(isset($_POST["teamSelect"])) {
        $_SESSION["teamSelect"] = $_POST["teamSelect"];
    }
?>
<div id="scheduleSelector_div">
    <div id="dateSelector_div">
        <?php 
            $date = new View_Date;
            echo $date->selector("GET");
        ?>
    </div>
    <div id="teamSelector_div">
        <?php
            if(($session->rank)>=50) {
                $url = new Controller_Url();
                (int) $selectedTeam = Model_Team::get_selected();
                $output = "<form id=\"teamSelector_form\" method=\"POST\" action=\"" . $url . "\"><label for=\"teamSelect\">Team: </label>";
                $output .= View_Team::selector($selectedTeam);
                $output .= "<input type=\"submit\" name=\"Submit\" value=\"Submit\"/></form>";
                echo $output;
            } else {
                (int) $selectedTeam = $session->team;
            }
        ?>
    </div>
</div>
<?php
    $users_array = Model_User::get_by_team($selectedTeam);
    $team = Model_Team::get($selectedTeam);
    $output = "<div id=\"scheduleSelected_div\">";
    if(count($users_array) > 0) {
        $output .= "<form id=\"scheduleSelected_form\" action=\"index.php" . htmlspecialchars("?id={$currentId}") . "\" method=\"POST\" ><table id = \"scheduleSelected_table\"><thead><tr><th>Name</th><th>Start Time</th><th>End Time</th><th>Working Hours</th></tr></thead><tbody>";
        $date = Controller_Date::get_selected();
        $selectedDay = date("l", strtotime($date["d"] . "-" . $date["m"] . "-" . $date["y"]));
        foreach($users_array as $user) {
            $output .= "<tr>";
            $output .= "<td>" . htmlspecialchars($user->firstName) . " " . htmlspecialchars($user->lastName) . "</td><td class=\"time\" >";
            $schedule = $user->get_sch($selectedDay);
            $endTime = strtotime($schedule["end_time"]);
            $startTime = strtotime($schedule["start_time"]);

            if(($endTime - $startTime) <= (4*60*60)) {
                $workingHours = is_bool(strftime("%k")) ? preg_replace("/^0?(\d{1,2}:\d{2})$/", "\\1", strftime("%H:%M", ($endTime - $startTime))) : strftime("%k:%M", ($endTime - $startTime));
            } else {
                $workingHours = is_bool(strftime("%k")) ? preg_replace("/^0?(\d{1,2}:\d{2})$/", "\\1", strftime("%H:%M", ($endTime - $startTime - (30*60)))) : strftime("%k:%M", ($endTime - $startTime - (30*60)));
            }

            if($editing == TRUE) {
                $output .= "<input type=\"text\" name=\"starttime_" . htmlspecialchars($user->id) . "\" value=\"";
            }		
            $output .= Controller_Time::format($schedule["start_time"], "html");
            if($editing == TRUE) {
                $output .= "\" />";
            }
            $output .= "</td><td class=\"time\" >";
            if($editing == TRUE) {
                $output .= "<input type=\"text\" name=\"endtime_" . htmlspecialchars($user->id) . "\" value=\"";
            }	
            $output .= Controller_Time::format($schedule["end_time"], "html");
            if($editing == TRUE) {
                $output .= "\" />";
            }
            $output .= "</td><td class=\"time\" >" .  htmlspecialchars($workingHours) . "</td>";
            $output .= "</tr>";
        }
        $output .= "</tbody></table>";
        if($editing == TRUE) {
            $output .= "<input type=\"submit\" value=\"Submit\" name=\"submit\" />";
            $output .= "<input type=\"submit\" value=\"Cancel\" name=\"cancel\" />";
        } else {
            $output .= "<input type=\"submit\" value=\"Edit\" name=\"edit\" />";
        }
        $output .= "</form>";
    } else {
        
        $output .= "There is no active user scheduled for {$team->name} today.";
    }
    $output .= "</div>";
    echo $output;
?>