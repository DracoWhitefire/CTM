<html>
	<head>
		<title>Arrays</title>
	</head>
	<body>
		<?php
			$array1 = array(1, 2, 5, 3, 15, 18);
			echo $array1[3];
		?>
		<br />
		<?php
			$array2 = array(6, "fox", "dog", array("a", "b", "c"));
			echo $array2[3][1];
		?>
		<br />
		<?php
			$array2[3][0] = "boogie";
			echo $array2[3][0];
		?>
		<br />
		<?php
			$array3 = array("first_name"=>"Draco", "last_name"=>"Whitefire");
			echo $array3["first_name"];
		?>
		<br />
		<?php
			print_r($array2);
		?>
		<br />
		<pre>
			<?php
				print_r($array2);
			?>
		</pre>
	</body>
</html>
