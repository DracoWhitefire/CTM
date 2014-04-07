<?php
    if(isset($_POST["logout_submit"])) {
        $session->logout();
    }
?>
<form method="POST" id="logout_form" action="index.php?id=<?php echo LOGOUT_MODULE_ID; ?>">
    <input type="submit" id="logout_submit" name="logout_submit" value="Logout" />
</form>