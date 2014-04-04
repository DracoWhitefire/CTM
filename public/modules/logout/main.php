<?php
    if(isset($_POST["logout_submit"])) {
        unset($_SESSION["id"]);
        header("location:index.php");
    }
?>
<form method="POST" id="logout_form" action="index.php?id=5">
    <input type="submit" id="logout_submit" name="logout_submit" value="Logout" />
</form>