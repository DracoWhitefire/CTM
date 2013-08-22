<?php
	require_once("../includes/functions.php");
	$session = new Session;
	if((!$session->is_loggedIn()) && ($_GET["id"] != "7")) {
		header("location:index.php?id=7");
	}
	$db = new MySqlDatabase;
//	$subject_set = get_all_subjects();
	$current_id = Subject::get_id();
	$current_subject = Subject::get($current_id);
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
					<?php echo navigation(); ?>
				</div>
				<div id="main_div">
					<div id="pageTitle_div">
						<h2><?php echo $current_subject->menuName; ?></h2>
					</div>
					<div id="content_div">
						<?php
							$includefile = strtolower("modules/{$current_subject->menuName}/main.php");
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