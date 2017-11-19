<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'rb557');
define('USERNAME', 'rb557');
define('PASSWORD', 'ymsxxPdn');
define('CONNECTION', 'sql2.njit.edu');
class dbConn
{
//variable to hold connection object.
    protected static $db;

//private construct - class cannot be instatiated externally.
    private function __construct()
    {
        try {
// assign PDO object to db variable
            self::$db = new PDO('mysql:host=' . CONNECTION . ';dbname=' . DATABASE, USERNAME, PASSWORD);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        } catch (PDOException $e) {
//Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }

// get connection function. Static method - accessible without instantiation
    public static function getConnection()
    {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        self::$db->query("use rb557");
        return self::$db;
    }
}


class htmlTable
{
    static function createTable($data)
    {
        echo '<table>';
        foreach ($data as $row)
        {
            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>$column</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
class collection
{
    static public function create() {
        $model = new static::$modelName;
        return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'accounts';
}
class todos extends collection
{
    protected static $modelName = 'todos';
}
class model {

    protected $tableName;
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
        // echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";
        echo 'I just saved record: ' . $this->id;
    }
    private function insert()
    {
        $sql = 'sometthing';
        return $sql;
        //     $array = get_object_vars($this);
        //     unset($array['tableName']);
        //     $columnString = implode(',', array_keys($array));
        //     foreach ($array as $value) {
        //         $values[] .= "'" . $value . "'";
        //     }
        //     $valueString = implode(',', $values);
        //     $sql = "INSERT INTO $this->tableName ($columnString) VALUES ( $valueString )";
        //     return $sql;
    }

    private function update() {
        $sql = 'sometthing';
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    public function delete() {
        echo 'I just deleted record' . $this->id;
    }
}
class account extends model
{
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;

    public function __construct()
    {
        $this->tableName = 'todos';

    }
}
// this would be the method to put in the index page for accounts
echo '<h1>Select all from accounts table</h1>';
$obj=accounts::create();
$records = $obj->findAll();
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select all from todos table</h1>';
$obj=todos::create();
$records = $obj->findAll();
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select one from todos table</h1>';
$obj=todos::create();
$records = $obj->findOne(1);
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select one from accounts table</h1>';
$obj=accounts::create();
$records = $obj->findOne(1);
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

//echo '<h1>Insert a record into todos table</h1>';
//$obj=todos::create();
//$records = $obj->insert(7);
//$sql=
//$sql = $this->insert();
//$stmt = $db->prepare("INSERT INTO todos (id, owneremail, ownerid, createddate, duedate, message, isdone) VALUES
//(7, 'jane@njit.edu', 3, '2017-01-10 00:00:00', '2017-05-12 00:00:00', 'This is test #C', 0)");
//$stmt->execute();
//$id = $db->lastInsertId();
//$records= $obj->insert();
//htmlTable::createTable($records);
//echo '<br>';
//echo '<br>';


?>