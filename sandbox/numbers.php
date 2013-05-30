<html>
	<head>
		<title>Numbers</title>
	</head>
	<body>
		<?php
			$var1 = 3;
			$var2 = 4;
		?>
		Basic Math: <?php echo ((1 + 2 + $var1) * $var2) / 2 - 5; ?> <br />
		+=: <?php echo $var1 += 3 ?> <br />
		-=: <?php echo $var1 -= 2 ?> <br />
		/=: <?php echo $var1 /= 2 ?> <br />
		*=: <?php echo $var1 *= 3 ?> <br />
		++: <?php $var1++; echo $var1 ?> <br />
	</body>
</html>
