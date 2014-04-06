<?php
    if(isset($_POST["edit"])) {
        $editing = TRUE;
    } else {
        $editing = FALSE;
    }
    $_SESSION["teamSelect"] = Model_Team::getSelected();
    $_SESSION["selectedDate"] = Controller_Date::getSelected();
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
            if($session->getRank() >= 50) {
                $url = new Controller_Url();
                (int) $selectedTeam = Model_Team::getSelected();
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
    $users = Model_User::getByTeam($selectedTeam);
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
        $date = Controller_Date::getSelected();
        (int) $selectedDay = date("w", strtotime($date["d"] . "-" . $date["m"] . "-" . $date["y"])) +  1;
        foreach($usersArray as $user) {
            $schedule = $user->getSchedule($selectedDay);
            if(count($schedule) == 0) {
                continue;
            } else {
                $schedCount++;
            }
            $schedOutput .= "<tr>";
            $schedOutput .= "<td>" . htmlspecialchars($user->firstName) . " " . htmlspecialchars($user->lastName) . "</td><td class=\"time\" >";
            $startTime = $schedule->getStarttime();
            $endTime = $schedule->getEndtime();
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
            $schedOutput .= $schedule->getScheduledHours();
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