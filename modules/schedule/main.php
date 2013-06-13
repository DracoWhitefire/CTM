<?php
	if(isset($_POST["edit"])) {
		$editing = TRUE;
	} else {
		$editing = FALSE;
	}
	$date = get_selected_date();
	echo calendar($date);
?>
<div>
		
</div>
<?php
	$output = "<div id=\"scheduleSelected_div\"><form id=\"scheduleSelected_form\" action=\"index.php" . htmlspecialchars("?id={$current_id}") . "\" method=\"POST\" ><table id = \"scheduleSelected_table\"><thead><tr><th>Forum Name</th><th>Start Time</th><th>End Time</th><th>Working Hours</th></tr></thead><tbody>";
	$agent_set = get_agents("active");
	$selectedDay = date("l", strtotime($date["d"] . "-" . $date["m"] . "-" . $date["y"]));
	while($agent_row = mysql_fetch_array($agent_set, MYSQL_ASSOC)) {
		$output .= "<tr>";
		$output .= "<td>" . $agent_row["forum_name"] . "</td><td class=\"time\" >";
		$schedule = get_sch_for_agent($agent_row["id"], $selectedDay);
		$endTime = strtotime($schedule["end_time"]);
		settype($endTime, "float");
		$startTime = strtotime($schedule["start_time"]);
		settype($startTime, "float");
		
		settype($workingHours, "float");
		if(($endTime - $startTime) <= (4*60*60)) {
			$workingHours = gmstrftime("%H:%M", ($endTime - $startTime));
		} else {
			$workingHours = gmstrftime("%H:%M", ($endTime - $startTime - (30*60)));
			//$workingHours = $endTime - $startTime - (30*60);
		}
		
		if($editing == TRUE) {
			$output .= "<input type=\"text\" name=\"starttime_" . $agent_row["id"] . "\" value=\"";
		}		
		$output .= $schedule["start_time"];
		if($editing == TRUE) {
			$output .= "\" />";
		}
		$output .= "</td><td class=\"time\" >";
		if($editing == TRUE) {
			$output .= "<input type=\"text\" name=\"endtime_" . $agent_row["id"] . "\" value=\"";
		}	
		$output .= $schedule["end_time"];
		if($editing == TRUE) {
			$output .= "\" />";
		}
		$output .= "</td><td class=\"time\" >" .  $workingHours . "</td>";
		$output .= "</tr>";
	}
	$output .= "</tbody></table>";
	if($editing == TRUE) {
		$output .= "<input type=\"submit\" value=\"Submit\" name=\"submit\" />";
		$output .= "<input type=\"submit\" value=\"Cancel\" name=\"cancel\" />";
	} else {
		$output .= "<input type=\"submit\" value=\"Edit\" name=\"edit\" />";
	}
	$output .= "</form></div>";
	echo $output;
?>