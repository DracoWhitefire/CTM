<?php
//print_r($_POST);
//print_r($_COOKIE);
$columnName_array = array("userName", "firstName", "lastName", "forumName", "rank", "active");
function listFilter($columnName_array) {
    $columnFilter_array = array();
    $expiration = time()+60*60*24*31;
    foreach($columnName_array as $columnName) {
        $columnName;
        $columnFilter_array[$columnName] = FALSE;
        if(isset($_POST["userListFilter_submit"])) {
            if(isset($_POST["filter_" . $columnName . "Vis_check"])) {
                if($_POST["filter_" . $columnName . "Vis_check"] == "on") {
                    setcookie($columnName, TRUE, $expiration);
                    $columnFilter_array[$columnName] = TRUE;
                } else {
                    unset($_COOKIE[$columnName]);
                    setcookie($columnName, FALSE, $expiration);
                }
            } else {
                unset($_COOKIE[$columnName]);
                setcookie($columnName, FALSE, $expiration);
            }
        } else {
            if(isset($_COOKIE[$columnName])) {
                if($_COOKIE[$columnName] == TRUE) {
                    $columnFilter_array[$columnName] = TRUE;
                }
            }
        }
    }
    return $columnFilter_array;
}
$columnFilter_array = listFilter($columnName_array);
$userList = "<div id=\"userListFilter_div\" ><form id=\"userlistFilter_form\"  action=\"index.php?id=" . urlencode($currentId) . "\" method=\"POST\" >";
$userList .= "<div id=\"listColumnFilter_div\" >";
foreach($columnName_array as $columnName) {
    $columnNameSplit_array = preg_split("/([A-Z][a-z]+)/", $columnName, 0, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    if(count($columnNameSplit_array) > 1) {
        $columnNameOutput = ucfirst($columnNameSplit_array[0]) . " " . ucfirst($columnNameSplit_array[1]);
    } else {
        $columnNameOutput = ucfirst($columnName);
    }
    $userList .= "<label for \"filter_{$columnName}Vis_check\" >" . $columnNameOutput . "</label><input type=\"checkbox\" id=\"filter_" . $columnName . "Vis_check\" name=\"filter_" . $columnName . "Vis_check\" ";
    if($columnFilter_array[$columnName]) {
        $userList .= "checked=\"checked\" ";
    }
    $userList .= ">";
}
$userList .= "</div>";
$userList .= "<input type=\"submit\" id=\"userListFilter_submit\" name=\"userListFilter_submit\" value=\"Filter\" >";
$userList .= "</div>";

$userList .= "<div id=\"userlist_div\"><form id=\"userlist_form\"  action=\"index.php?id=" . urlencode($currentId) . "\" method=\"POST\" >";
$userList .= "<table id=\"userlist_table\">";
$userList .= "<thead>";
$userList .= "<tr><th></th>";
if($columnFilter_array["userName"]) {
    $userList .= "<th>Username</th>";
}
if($columnFilter_array["firstName"]) {
    $userList .= "<th>First Name</th>";
}
if($columnFilter_array["lastName"]) {
    $userList .= "<th>Last Name</th>";
}
if($columnFilter_array["forumName"]) {
    $userList .= "<th>Forum Name</th>";
}
if($columnFilter_array["rank"]) {
    $userList .= "<th>Rank</th>";
}
if($columnFilter_array["active"]) {
    $userList .= "<th>Active</th>";
}
$userList .= "<th></th></tr>";
$userList .= "</thead>";
$userList .= "<tbody>";
foreach($users_array as $user) {
    $id = $user->id;
    $fieldname = "edit_{$id}";
    $editRow = FALSE;
    if((isset($_POST["$fieldname"]))) {
        if($_POST["$fieldname"] == "on") {
            $editRow = TRUE;
        }
    } elseif((!empty($validator->errors)) && ((isset($errorId_array[$id])) || isset($_POST["user_name_{$id}"]))) {
        $editRow = TRUE;
    }
    $userList .= "<tr><td class=\"check\" >";
    if($editList == FALSE) {
        $userList .= "<input type=\"checkbox\" name=\"" . htmlspecialchars("edit_{$id}") . "\" />";
    }
    $userList .= "</td>";
    if($columnFilter_array["userName"]) {
        $userList .= "<td class=\"name";
        if(isset($validator->errors["user_name_{$id}"])) {
            $userList .= " error\"";
        } else {
            $userList .= "\"";
        }
        $userList .= " >";
        if($editRow == TRUE) {
            $userList .= "<input type=\"text\" name=\"" . htmlspecialchars("user_name_{$id}") . "\" value=\"";
        }
        if(isset($_POST["user_name_{$id}"])) {
            $userList .= htmlspecialchars($_POST["user_name_{$id}"]);
        } else {
            $userList .= htmlspecialchars($user->userName);
        }		
        if($editRow == TRUE) {
            $userList .= "\" />";
        }
        $userList .= "</td>";
    }
    if($columnFilter_array["firstName"]) {
        $userList .= "<td class=\"name";
        if(isset($validator->errors["first_name_{$id}"])) {
            $userList .= " error\"";
        } else {
            $userList .= "\"";
        }
        $userList .= " >";
        if($editRow == TRUE) {
            $userList .= "<input type=\"text\" name=\"" . htmlspecialchars("first_name_{$id}") . "\" value=\"";
        }
        if(isset($_POST["first_name_{$id}"])) {
            $userList .= htmlspecialchars($_POST["first_name_{$id}"]);
        } else {
            $userList .= htmlspecialchars($user->firstName);
        }		
        if($editRow == TRUE) {
            $userList .= "\" />";
        }
        $userList .= "</td>"; 
    }
    if($columnFilter_array["lastName"]) {
        $userList .= "<td class=\"name";
        if(isset($validator->errors["last_name_{$id}"])) {
            $userList .= " error\"";
        } else {
            $userList .= "\"";
        }
        $userList .= " >";
        if($editRow == TRUE) {
            $userList .= "<input type=\"text\" name=\"" . htmlspecialchars("last_name_{$id}") . "\" value=\"";
        }
        if(isset($_POST["last_name_{$id}"])) {
            $userList .= htmlspecialchars($_POST["last_name_{$id}"]);
        } else {
            $userList .= htmlspecialchars($user->lastName);
        }
        if($editRow == TRUE) {
            $userList .= "\" />";
        }
        $userList .= "</td>";
    }
    if($columnFilter_array["forumName"]) {
        $userList .= "<td class=\"name";
        if(isset($validator->errors["forum_name_{$id}"])) {
            $userList .= " error\"";
        } else {
            $userList .= "\"";
        }
        $userList .= ">";
        if($editRow == TRUE) {
            $userList .= "<input type=\"text\" name=\"" . htmlspecialchars("forum_name_{$id}") . "\" value=\"";
        }
        if(isset($_POST["forum_name_{$id}"])) {
            $userList .= htmlspecialchars($_POST["forum_name_{$id}"]);
        } else {
            $userList .= htmlspecialchars($user->forumName);
        }
        if($editRow == TRUE) {
            $userList .= "\" />";
        }
        $userList .= "</td>";
    }
    if($columnFilter_array["rank"]) {
        $userList .= "<td class=\"rank\" >";
        if($editRow == TRUE) {
            $userList .= View_Rank::selector($user);
        } else {
            $rank = Model_Rank::get($user->rank);
            $userList .= htmlspecialchars($rank->name);
        }
        $userList .= "</td>";
}
if($columnFilter_array["active"]) {
        $userList .= "<td><input type=\"checkbox\" name=\"" . htmlspecialchars("active_check_{$id}") . "\" ";
        if($editRow == FALSE) {
            $userList .= "disabled=\"disabled\" ";
        }
        if($user->active == TRUE) {
            $userList .= "checked=\"checked\" ";
        }
        $userList .= "/>";
        $userList .= "</td>";
    }
    $userList .= "<td>";
    if($editList == FALSE) {
        $userList .= "<button type=\"submit\" name=\"singleEditList\" formmethod=\"post\" value=\"" . htmlspecialchars($id) . "\"  >Edit</button>";
    }
    $userList .= "</td></tr>";
}

$userList .= "</tbody>";
$userList .= "</table>";
if($editList == TRUE) {
    $userList .= "<input type=\"submit\" value=\"Submit\" name=\"submitList\" />";
    $userList .= "<input type=\"submit\" value=\"Cancel\" name=\"cancelEditList\" />";
} else {
    $userList .= "<input type=\"submit\" value=\"Bulk Edit\" name=\"editList\" />";
    $userList .= "<input type=\"submit\" value=\"Add\" name=\"addList\" />";
}
$userList .= "</form></div>";