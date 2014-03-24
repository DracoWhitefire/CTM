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
    $users = Model_User::get_by_team($selectedTeam);
    if(count($users) == 1) {
        $usersArray[] = $users;
    } else {
        $usersArray = $users;
    }
    $team = Model_Team::get($selectedTeam);
    $schedCount = 0;
    $output = "<div id=\"scheduleSelected_div\">";    
    if(count($usersArray) > 0) {
        $schedOutput = "<form id=\"scheduleSelected_form\" action=\"index.php" . htmlspecialchars("?id={$currentId}") . "\" method=\"POST\" ><table id = \"scheduleSelected_table\"><thead><tr><th>Name</th><th>Start Time</th><th>End Time</th><th>Working Hours</th></tr></thead><tbody>";
        $date = Controller_Date::get_selected();
        $selectedDay = date("l", strtotime($date["d"] . "-" . $date["m"] . "-" . $date["y"]));
        foreach($usersArray as $user) {
            $schedule = $user->get_sch($selectedDay);
            if(count($schedule) == 0) {
                continue;
            } else {
                $schedCount++;
            }
            $schedOutput .= "<tr>";
            $schedOutput .= "<td>" . htmlspecialchars($user->firstName) . " " . htmlspecialchars($user->lastName) . "</td><td class=\"time\" >";
            $startTime = $schedule->get_starttime();
            $endTime = $schedule->get_endtime();
            if($editing == TRUE) {
                $schedOutput .= "<input type=\"text\" name=\"starttime_" . htmlspecialchars($user->id) . "\" value=\"";
            }		
            $schedOutput .= $startTime;
            if($editing == TRUE) {
                $schedOutput .= "\" />";
            }
            $schedOutput .= "</td><td class=\"time\" >";
            if($editing == TRUE) {
                $schedOutput .= "<input type=\"text\" name=\"endtime_" . htmlspecialchars($user->id) . "\" value=\"";
            }
            $schedOutput .= $endTime;
            if($editing == TRUE) {
                $schedOutput .= "\" />";
            }
            $schedOutput .= "</td><td class=\"time\" >";
            $schedOutput .= $schedule->get_scheduledhours();
            $schedOutput .= "</tr>";
        }
        $schedOutput .= "</tbody></table>";
        if($editing == TRUE) {
            $schedOutput .= "<input type=\"submit\" value=\"Submit\" name=\"submit\" />";
            $schedOutput .= "<input type=\"submit\" value=\"Cancel\" name=\"cancel\" />";
        } else {
            $schedOutput .= "<input type=\"submit\" value=\"Edit\" name=\"edit\" />";
        }
        $schedOutput .= "</form>";
    }
    if($schedCount > 0) {
        $output .= $schedOutput;
    } else {
        $output .= "There is no active user scheduled for {$team->name} today.";
    }
    $output .= "</div>";
    echo $output;