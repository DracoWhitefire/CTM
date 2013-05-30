<?php
	$errors = array();
	require_once("includes/functions.php");
	if(isset($_POST["login"])) {
		//login attempt
		$username = trim(mysql_prep($_POST["username_input"]));
		$password = trim(mysql_prep($_POST["password_input"]));
		$checkReq_array = array("username_input", "password_input");
		form_val_required($checkReq_array);
		$checkLen_array = array("username_input" => "5-30", "password_input" => "5-30");
		form_val_length($checkLen_array);
	} else {
		//no login attempt
		$username = "";
		$password = "";
		if(isset($_POST["cancel"])) {
			header("location: index.php");
			exit;
		}
	}
?>

<div id="login_div">
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