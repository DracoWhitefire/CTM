<?php
    require_once("../includes/functions.php");
    $session = new Controller_Session;
    if((!$session->isLoggedIn()) && ($_GET["id"] != LOGIN_MODULE_ID)) {
        header("location:index.php?id=" . LOGIN_MODULE_ID);
    }
    $currentId = Model_Module::getCurrentId();
    $currentModule = Model_Module::get($currentId);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
        <title><?php echo SITE_TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="css/common.css" />
    </head>
    <body>
        <div id="site">
            <div id="siteTitle">
                <h1><?php echo SITE_TITLE; ?></h1>
            </div>
            <div id="body">
                <div id="nav">
                    <?php echo View_Module::menu(); ?>
                </div>
                <div id="main">
                    <div id="pageTitle">
                        <h2><?php echo $currentModule->menuName; ?></h2>
                    </div>
                    <div id="content">
                        <?php
                                $includefile = strtolower("modules/{$currentModule->menuName}/main.php");
                                include($includefile);

                        ?>
                    </div>
                    <hr />
                    <!--<div id="debug_div">
                        <pre>
                            <span>
                                Post:
                                <?php
                                    print_r($_POST);						
                                ?>
                            </span><br />
                            <span>
                                Errors:
                                <?php
                                    if(isset($errors)) {
                                        print_r($errors);
                                    }
                                ?>
                            </span>
                        </pre>
                    </div>-->
                </div>
            </div>
            <div id="copyright">
                Copyright <?php echo date("Y"); ?>
            </div>
        </div>
    </body>
</html>
<?php
    call_user_func(DB_CLASS . "::getInstance")->disconnect();