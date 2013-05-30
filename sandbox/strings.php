<html>
	<head>
		<title>Index</title>
	</head>
	<body>
		<?php
			$firstString = "The quick brown fox";
			$secondString = " jumped over the lazy dog";
			
			$thirdString = $firstString;
			$thirdString .= $secondString;
			echo $thirdString;
		?>
		<br />
		Lowercase: <?php echo strtolower($thirdString); ?> <br />
		Uppercase: <?php echo strtoupper($thirdString); ?> <br />
		First Letter: <?php echo ucfirst($thirdString); ?> <br />
		Every word: <?php echo ucwords($thirdString); ?> <br />
		<br />
		Length: <?php echo strlen($thirdString); ?> <br />
		Trim: <?php echo $fourthString = $firstString . trim($secondString); ?> <br />
		Find: <?php echo strstr($thirdString, "brown"); ?> <br />
		Replace: <?php echo str_replace("quick", "fast", $thirdString); ?> <br />	
		<br />
		Repeat: <?php echo str_repeat($thirdString, 2); ?> <br />
		Substring: <?php echo substr($thirdString, 5, 10); ?> <br />
		Position: <?php echo strpos($thirdString, "brown"); ?> <br />
		Character: <?php echo strchr($thirdString, "z"); ?> <br />
	</body>
</html>
