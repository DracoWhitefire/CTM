<?php
	require_once("../includes/functions.php");
	$session = new Controller_Session;
	if((!$session->is_loggedIn()) && ($_GET["id"] != "7")) {
		header("location:index.php?id=7");
	}
	$db = new Model_MySqlDb;
	$currentId = Model_Module::get_current_id();
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
		<div id="site_div">
			<div id="siteTitle_div">
				<h1><?php echo SITE_TITLE; ?></h1>
			</div>
			<div id="body_div">
				<div id="nav_div">
					<?php echo View_Navigation::menu(); ?>
				</div>
				<div id="main_div">
					<div id="pageTitle_div">
						<h2><?php echo $currentModule->menuName; ?></h2>
					</div>
					<div id="content_div">
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
			<div id="copyright_div">
				Copyright <?php echo date("Y"); ?>
			</div>
		</div>
	</body>
</html>
<?php
	$db->disconnect();
?>