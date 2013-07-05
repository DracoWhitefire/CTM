<?php
	if(isset($_POST["login"])) {
		//login attempt
		$username = trim($db->query_prep($_POST["username_input"]));
		$password = trim($db->query_prep($_POST["password_input"]));
		$checkReq_array = array("username_input", "password_input");
		$validator = new Validator;
		$validator->required($checkReq_array);
		if(empty($validator->errors)) {
			$users = User::get("all");
			foreach($users as $user) {
				if($user->userName == $username) {
					if($user->pw_check($password)) {
						$session->login($user);
						header("location: index.php");
					} else {
						$message = "Login Failed!";
					}
					break;
				} else {
					$message = "User not found";
				}
			}
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