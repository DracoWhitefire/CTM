<?php
	session_start();
	if((!isset($_SESSION["id"])) && ($_GET["id"] != "7")) {
	header("location:index.php?id=7");
}
?>
<!DOCTYPE HTML>
<?php
	require_once("includes/functions.php");
	db_connect();
	$subject_set = get_all_subjects();
	$current_id = get_selected_id();
	$current_subject = get_subject_by_id($current_id);
?>

<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title><?php echo SITE_TILE; ?></title>
		<link rel="stylesheet" type="text/css" href="css/common.css" />
	</head>
	<body>
		<div id="site_div">
			<div id="siteTitle_div">
				<h1><?php echo SITE_TILE; ?></h1>
			</div>
			<div id="body_div">
				<div id="nav_div">
					<?php echo navigation($subject_set); ?>
				</div>
				<div id="main_div">
					<div id="pageTitle_div">
						<h2><?php echo $current_subject["menu_name"]; ?></h2>
					</div>
					<div id="content_div">
						<?php
							$includefile = strtolower("modules/{$current_subject["menu_name"]}/main.php");
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
	if(isset($connection)) {
		mysqli_close($connection);
	}
?>