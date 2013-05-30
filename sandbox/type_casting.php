<html>
	<head>
		<title>Type Casting</title>
	</head>
	<body>
		<?php
			$var1 = " 2 ";
			$var2 = $var1 + 4;
			echo $var1; 
		?>
		<br />
		<?php
			echo gettype($var1) . gettype($var2);
		?>
	</body>
</html>
