<?php
    if(Controller_Request::post("logoutSubmit")) {
        $session->logout();
    }
?>
<form method="POST" id="logoutForm" action="index.php?id=<?php echo LOGOUT_MODULE_ID; ?>">
    <input type="submit" id="logoutSubmit" name="logoutSubmit" value="Logout" />
</form>