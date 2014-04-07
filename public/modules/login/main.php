<?php
if(isset($_POST["login"])) {
    //login attempt
    $username = call_user_func(DB_CLASS . "::queryPrep", $_POST["username"]);
    $password = call_user_func(DB_CLASS . "::queryPrep", $_POST["password"]);
    $checkReqArray = array("username", "password");
    $validator = new Controller_Validator;
    $validator->required($checkReqArray);
    if(empty($validator->errors)) {
        $users = Model_User::get("all");
        foreach($users as $user) {
            if($user->userName == $username) {
                if($user->pwCheck($password)) {
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

<div id="login">
    <?php echo $message; ?>
    <form id="loginForm" method="POST" action="index.php?id=<?php echo $currentId; ?>">
        <label for="username">Username</label> <input type="text" name="username" id="username" maxlength="30" value="<?php echo htmlentities($username); ?>" /><br />
        <label for="password">Password</label> <input type="password" name="password" id="password" maxlength="30" value="<?php echo htmlentities($password); ?>" /><br />
        <div id="loginButtons">
            <input type="submit" name="login" value="Log In" /><input type="submit" name="cancel" value="Cancel" />
        </div>
    </form>
</div>

<?php
    //print_r($_POST);
    //print_r($errors);