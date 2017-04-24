<?php
	// MySQL/PDO database connection factory for PHP Alexandria - March 17, 2012

	function getDatabaseConnection() {
		# MySQL with PDO_MYSQL
		// $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		$DBH = new PDO("mysql:host=localhost;dbname=alexandria;charset=utf8", "user", "tomtom7");
		//echo "<p>Connected to the Alexandria MySQL database.</p>";

		// Configure PHP/PDO to throw an exception if an error occurs.
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $DBH;
	}
?>
