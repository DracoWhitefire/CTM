<html>
	<head>
		<title>Array Functions</title>
	</head>
	<body>
		<?php $array1 = array(1, 2, 5, 3, 15, 18, 2); ?>
		Count: <?php echo count($array1); ?> <br />
		Max Value: <?php echo max($array1); ?> <br />
		Min Value: <?php echo min($array1); ?> <br />
		<br />
		Sort: <?php sort($array1); echo print_r($array1); ?> <br />
		Reverse Sort: <?php rsort($array1); echo print_r($array1); ?> <br />
		<br />
		Implode: <?php echo $string1 = implode(" * ", $array1); ?> <br />
		Explode: <?php $array2 = explode(" * ", $string1); print_r($array2); ?> <br />
		<br />
		In array: <?php echo in_array(3, $array2); ?><br />
	</body>
</html>
