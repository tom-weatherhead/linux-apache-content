<?php
/* See http://coreymaynard.com/blog/creating-a-restful-api-with-php/
 */

require_once 'API.php';

class Author
{
    public $id;
    public $first_name;
    public $last_name;
    public $birth_year;
    public $death_year;

    function __construct() {
    }
}

class MyAPI extends API
{
    protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);

	/*
        // Abstracted out for example
        $APIKey = new Models\APIKey();
        $User = new Models\User();

        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
             !$User->get('token', $this->request['token'])) {

            throw new Exception('Invalid User Token');
        }

        $this->User = $User;
	 */
    }

    /**
     * Example of an Endpoint
     */
    /*
     protected function example() {
        if ($this->method == 'GET') {
            //return "Your name is " . $this->User->name;
	    return "Success!";
        } else {
            return "Only accepts GET requests";
        }
     }
     */

    private function getDatabaseConnection() {
	// MySQL with PDO_MYSQL
	// $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
	$db = new PDO("mysql:host=localhost;dbname=resttest;charset=utf8", "user", "tomtom7");
	//echo "<p>Connected to the Labyrinth MySQL database.</p>";

	// Configure PHP/PDO to throw an exception if an error occurs.
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $db;
    }

    public function processAPI() {
        $return_data = "No data";
        $return_status = 400;

        $id = $this->endpoint;

        try {
            $db = $this->getDatabaseConnection();

            if ($this->method == 'GET') {

                if ($id == "") {
                    // Get all authors.
                    $stmt = $db->prepare("SELECT * FROM authors");
                    $stmt->execute();
/*
                    $result = array();

                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $result[] = $row;
                    }

                    $return_data = $result;
 */
                    // Either FETCH_ASSOC or FETCH_CLASS works here.
                    //$return_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $return_data = $stmt->fetchAll(PDO::FETCH_CLASS, "Author");
                    $return_status = 200;
                } else {
                    // Get the specified author, if the record exists.
                    $stmt = $db->prepare("SELECT * FROM authors WHERE id = :id");
                    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                    $stmt->setFetchMode(PDO::FETCH_CLASS, "Author");
                    $stmt->execute();
                    //$return_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    //$return_data = $stmt->fetch(PDO::FETCH_CLASS, "Author"); // This does not work.
                    $return_data = $stmt->fetch();

                    if ($return_data) {
                        $return_status = 200;
                    } else {
                        $return_data = "Record " . $id . " not found";
                        $return_status = 404;
                    }
                }
            } elseif ($this->method == 'POST' && $id == "") {
                // Should we only insert if $id (i.e. $this->endpoint) is "", since MySQL will automatically assign an id to the new record?  Done; see above.

                if (strpos($_SERVER["CONTENT_TYPE"], "json") !== false) {
                    // The posted data is JSON!
                    // See http://stackoverflow.com/questions/5806971/read-associative-array-from-json-in-post
                    $author = json_decode(file_get_contents('php://input'));
                } else {
                    // The posted data is not JSON.
                    $author = new Author();
                    $author->first_name = $_POST["first_name"];
                    $author->last_name = $_POST["last_name"];
                    $author->birth_year = $_POST["birth_year"];
                    $author->death_year = $_POST["death_year"];
                }

                if ($author->first_name == "" && $author->last_name == "") {
                    $return_data = "First name and last name are both empty";
                    $return_status = 400;
                } else {
                    $stmt = $db->prepare("INSERT INTO authors (first_name, last_name, birth_year, death_year) VALUES (:first_name, :last_name, :birth_year, :death_year)");
                    $stmt->bindValue(":first_name", $author->first_name, PDO::PARAM_STR);
                    $stmt->bindValue(":last_name", $author->last_name, PDO::PARAM_STR);
                    $stmt->bindValue(":birth_year", $author->birth_year, PDO::PARAM_INT);
                    $stmt->bindValue(":death_year", $author->death_year, PDO::PARAM_INT);
                    $stmt->execute();
                    $return_data = "Created";
                    $return_status = 201;
                }
            } elseif ($this->method == 'PUT' && $id != "") {
                $post_vars = json_decode($this->file, true /* Create an associative array */ );

                if ($post_vars["first_name"] == "" && $post_vars["last_name"] == "") {
                    $return_data = "First name and last name are both empty";
                    $return_status = 400;
                } else {
                    $stmt = $db->prepare("UPDATE authors SET first_name=:first_name, last_name=:last_name, birth_year=:birth_year, death_year=:death_year WHERE id=:id");
                    $stmt->bindValue(":first_name", $post_vars["first_name"], PDO::PARAM_STR);
                    $stmt->bindValue(":last_name", $post_vars["last_name"], PDO::PARAM_STR);
                    $stmt->bindValue(":birth_year", $post_vars["birth_year"], PDO::PARAM_INT);
                    $stmt->bindValue(":death_year", $post_vars["death_year"], PDO::PARAM_INT);
                    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $return_data = "Updated";
                    $return_status = 200;
                }
            } elseif ($this->method == 'DELETE' && $id != "") {
                $stmt = $db->prepare("DELETE FROM authors WHERE id = :id");
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $return_data = "Deleted";
                $return_status = 200;
            }
        } catch(PDOException $e) {
            $return_data = "PDO exception: " . $e->getMessage();
            $return_status = 500;
        }

        return $this->_response($return_data, $return_status);
    }
 }
?>
