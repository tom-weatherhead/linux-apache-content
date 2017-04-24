<!DOCTYPE HTML>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-CA">
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Tom Weatherhead" />
        <meta name="description" content="The Alexandria 'category' table" />

        <title>Categories</title>

        <link rel="icon" type="image/png" href="favicon3.png" />

        <link rel="stylesheet" type="text/css" href="Style.css" />
        <link rel="stylesheet" type="text/css" href="FretInfoStyle.css" />

        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript" src="PageFooter.js"></script>
        <script type="text/javascript" src="DefaultDocumentReady.js"></script>
        <script type="text/javascript" src="category.js"></script>
    </head>
    <body class="standardBody">
        <div id="topContent">Categories</div>

        <div id="mainContent" class="box centreText">
			<?php
				// See http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/

				require("connection.php");

				class Category {
					public $CATEGORY_ID;
					public $VERSION;
					public $Name;
					public $PARENT_CATEGORY_ID;

					function __construct() {
					}
				}

			    try {
					$DBH = getDatabaseConnection();

					// 1) Drop-down list or listbox
					$STH = $DBH->query("SELECT * FROM category ORDER BY Name");
					$STH->setFetchMode(PDO::FETCH_CLASS, "Category");
					echo "<p>Select a category:</p>";
					// Setting the size attribute of the <select> element changes it from a drop-down list into a listbox.
					echo "<select id='ddlCategoryNames' size='5' onchange='ddlCategoryNames_onChange()'>";
                    echo "<option value='00000000-0000-0000-0000-000000000000'></option>";

					while($obj = $STH->fetch()) {
						$selected = "";

						if (isset($_GET["id"]) && $_GET["id"] == $obj->CATEGORY_ID) {
							$selected = " selected='selected'";
						}

						echo "<option$selected value='$obj->CATEGORY_ID'>$obj->Name</option>";
					}

	                echo "</select>";

					# Close the connection.
					$DBH = null;
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			?>
        </div>
    </body>
</html>