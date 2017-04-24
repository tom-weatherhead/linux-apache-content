<!DOCTYPE HTML>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-CA">
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Tom Weatherhead" />
        <meta name="description" content="The Alexandria 'author' table" />

        <title>Authors</title>

        <link rel="icon" type="image/png" href="favicon3.png" />

        <link rel="stylesheet" type="text/css" href="Style.css" />
        <link rel="stylesheet" type="text/css" href="FretInfoStyle.css" />

        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript" src="PageFooter.js"></script>
        <script type="text/javascript" src="DefaultDocumentReady.js"></script>
        <script type="text/javascript" src="author.js"></script>
    </head>
    <body class="standardBody">
        <div id="topContent">Authors</div>

        <div id="mainContent" class="box centreText">
			<?php
				// See http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/

				require("connection.php");
												
				class Author {
					public $AUTHOR_ID;
					public $VERSION;
					public $Surname;
					public $GivenNames;
					public $YearOfBirth;
					public $YearOfDeath;

					function __construct() {
					}
				}

				function writeTwoColumnTableRow($name, $value) {
					//echo "<tr><td>" . $name . "</td><td>" . $value . "</td></tr>";
					echo "<tr><td>$name</td><td>$value</td></tr>";
				}

				function transformYear($year, $showBCAD) {

					if (!$showBCAD) {
						return $year;
					}

					$value = "Undefined";

					if ($year < 0) {
						$value = -$year . " B.C.";
					} elseif ($year > 0) {
						$value = "A.D. " . $year;
					}

					return $value;
				}

				//print_r(PDO::getAvailableDrivers());

				// For the population of the drop-down list:
				// "SELECT * FROM AUTHOR ORDER BY Surname, GivenNames"

				// When an option is selected in the drop-down list, reload the page with "author.php?id=...", and do this:
				// "SELECT * FROM AUTHOR WHERE AUTHOR_ID = '" . $AUTHOR_ID . "'"

			    try {
					$DBH = getDatabaseConnection();

					// GUID generation test.
					//echo "<p>GUID: " . strtolower(trim(com_create_guid(), "{}")) . "</p>";

					// 1) Drop-down list or listbox
					// This query is safe as is (i.e. it doesn't need to be a prepared statement) because it takes no parameters.
					$STH = $DBH->query("SELECT * FROM author ORDER BY Surname, GivenNames");
					$STH->setFetchMode(PDO::FETCH_CLASS, "Author");
					echo "<p>Select an author:</p>";
					// Setting the size attribute of the <select> element changes it from a drop-down list into a listbox.
					echo "<select id='ddlAuthorNames' size='5' onchange='ddlAuthorNames_onChange()'>";
                    echo "<option value='00000000-0000-0000-0000-000000000000'></option>";

					//while($obj = $STH->fetch()) {
					foreach ($STH->fetchAll() as $obj) {
						$selected = "";

						if (isset($_GET["id"]) && $_GET["id"] == $obj->AUTHOR_ID) {
							$selected = " selected='selected'";
						}

						//echo "<option" . $selected . " value='" . $obj->AUTHOR_ID . "'>" . $obj->GivenNames . " " . $obj->Surname . "</option>";
						echo "<option$selected value='$obj->AUTHOR_ID'>$obj->GivenNames $obj->Surname</option>";
					}

	                echo "</select>";
					//echo "</p>";

					if (isset($_GET["id"])) {
						$authorID = $_GET["id"];
						//$STH = $DBH->query("SELECT * FROM author WHERE AUTHOR_ID = '" . $authorID . "'");
						$STH = $DBH->prepare("SELECT * FROM author WHERE AUTHOR_ID = :authorID");
						$STH->bindValue(":authorID", $authorID, PDO::PARAM_STR);
						$STH->execute();
						$STH->setFetchMode(PDO::FETCH_CLASS, "Author");

						if ($obj = $STH->fetch()) {
							// 2) Table
							echo "<br />";
							echo "<br />";
							echo "<table>";
							echo "<tr><th>Field Name</th><th>Field Value</th></tr>";
							writeTwoColumnTableRow("Surname", $obj->Surname);
							writeTwoColumnTableRow("Given name(s)", $obj->GivenNames);

							$showBCAD = $obj->YearOfBirth < 0 || $obj->YearOfDeath < 0;

							if ($obj->YearOfBirth != 0) {
								//writeTwoColumnTableRow("Year of birth", $obj->YearOfBirth);
								writeTwoColumnTableRow("Year of birth", transformYear($obj->YearOfBirth, $showBCAD));
							}

							if ($obj->YearOfDeath != 0) {
								//writeTwoColumnTableRow("Year of death", $obj->YearOfDeath);
								writeTwoColumnTableRow("Year of death", transformYear($obj->YearOfDeath, $showBCAD));
							}

							echo "</table>";
							echo "<br />";

							// 3) Books table
							echo "<table>";
							echo "<tr><th>Books</th></tr>";
							//$STH = $DBH->query("SELECT b.BOOK_ID, b.Title FROM book b, author_book ab WHERE ab.AUTHOR_ID = '" . $authorID . "' AND ab.BOOK_ID = b.BOOK_ID ORDER BY b.Title");
							$STH = $DBH->prepare("SELECT b.BOOK_ID, b.Title FROM book b, author_book ab WHERE ab.AUTHOR_ID = :authorID AND ab.BOOK_ID = b.BOOK_ID ORDER BY b.Title");
							$STH->bindValue(":authorID", $authorID, PDO::PARAM_STR);
							$STH->execute();
							$STH->setFetchMode(PDO::FETCH_OBJ);

							//while ($obj = $STH->fetch()) {
							foreach ($STH->fetchAll() as $obj) {
								//echo "<tr><td><a href='book.php?id=" . $obj->BOOK_ID . "'>" . $obj->Title . "</a></td></tr>";
								echo "<tr><td><a href='book.php?id=$obj->BOOK_ID'>$obj->Title</a></td></tr>";
							}

							echo "</table>";
						}
					}

					# Close the connection.
					$DBH = null;
					//echo "<p>Connection closed.</p>";
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			?>
        </div>
    </body>
</html>
