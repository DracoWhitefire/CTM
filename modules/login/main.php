<?php
	$errors = array();
	require_once("includes/functions.php");
	if(isset($_POST["login"])) {
		//login attempt
		$username = trim(mysqli_prep($_POST["username_input"]));
		$password = trim(mysqli_prep($_POST["password_input"]));
		$checkReq_array = array("username_input", "password_input");
		form_val_required($checkReq_array);
		if(empty($errors)) {
			$query = "SELECT `id`, `user_name`, `passwordhash`, `rank`, `first_name` FROM `agents`";
			$agent_set = mysqli_query($connection, $query);
			while($agent_row = mysqli_fetch_assoc($agent_set)) {
				if($agent_row["user_name"] == $username) {
					if(pw_check($password, $agent_row["passwordhash"])) {
						$_SESSION["firstname"] = $agent_row["first_name"];
						$_SESSION["rank"] = $agent_row["rank"];
						$_SESSION["id"] = $agent_row["id"];
						header("location: index.php");
					} else {
						$message = "Login Failed!";
					}
					break;
				} else {
					$message = "User not found";
				}
			}
			mysqli_free_result($agent_set);
		} else {
			$message = "Some fields are not filled in";
		}
	} else {
		//no login attempt
		$message = "Please enter your user name and password.";
		$username = "";
		$password = "";
		if(isset($_POST["cancel"])) {
			header("location: index.php");
			exit;
		}
	}
?>

<div id="login_div">
	<?php echo $message; ?>
	<form id="login_form" method="POST" action="index.php?id=<?php echo $current_id; ?>">
		<label for="username_input">Username</label> <input type="text" name="username_input" id="username_input" maxlength="30" value="<?php echo htmlentities($username); ?>" /><br />
		<label for="password_input">Password</label> <input type="password" name="password_input" id="password_input" maxlength="30" value="<?php echo htmlentities($password); ?>" /><br />
		<div id="loginButtons_div">
			<input type="submit" name="login" value="Log In" /><input type="submit" name="cancel" value="Cancel" />
		</div>
	</form>
</div>

<?php
	//print_r($_POST);
	//print_r($errors);
?>