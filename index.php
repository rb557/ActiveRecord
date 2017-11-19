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
abstract class model{
    // static $tableName;
    static $key;
    static $value;
    static $id;
    public function save()
    {
        if (static::$idOfColumn == '')
        {
            $array = get_object_vars($this);
            self::$key = implode(', ', $array);
            self::$value = implode(', ', array_fill(0, count($array), '?'));
            $sql = $this->insert();
            // echo $sql;
            $db = dbConn::getConnection();
            $statement = $db->prepare($sql);
            $statement->execute(static::$dataToInsert);
        }
        else
        {
            $sql = $this->update();
            $db = dbConn::getConnection();
            $statement = $db->prepare($sql);
            $statement->execute();
            return $sql;
            echo '<br>';
            //  print_r( static::$columnId);
        }
    }
    private function insert()
    {
        $sql = "INSERT INTO ".static::$tableName." (".self::$key.") VALUES (".self::$value.")";
        return $sql;
    }
    private function update()
    {
        $sql = "UPDATE ".static::$tableName." SET ".static::$columnToUpdate." = '".static::$updateData."' WHERE id=".static::$idOfColumn;
        return $sql;
    }
    public function delete()
    {
        $db = dbConn::getConnection();
        $sql = "DELETE from ".static::$tableName." WHERE id=".static::$idOfColumn;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}

class account extends model
{
    //column names
    public $email = 'email';
    public $fname = 'fname';
    public $lname = 'lname';
    public $phone = 'phone';
    public $birthday = 'birthday';
    public $gender = 'gender';
    public $password = 'password';
    //corresponding data
    protected static $dataToInsert = array('nr123@njit.com','rohit','john','08103550744','10-10-1997','male','nr123');
    //table name
    public static $tableName = 'accounts';
    //column to be updated
    public static $columnToUpdate='lname';
    //data to be inserted into column
    protected static $updateData = 'Abraham';
    //id to update
    public static $idOfColumn = '6';
}
class todo extends model {
    // column names
    public $owneremail = 'owneremail';
    public $ownerid = 'ownerid';
    public $createddate = 'createddate';
    public $duedate = 'duedate';
    public $message = 'message';
    public $isdone = 'isdone';
    //corresponding data
    protected static $dataToInsert = array('rb42@njit.com','42','10/25/2017','12/28/2017','This is test #C','1');
    //table name
    public static $tableName = 'todos';
    //column to be updated for update query
    public static $columnToUpdate='owneremail';
    //data to be updated into column
    public static $updateData = 'nnn@test.com';
    //id to update
    public static $idOfColumn = '';
}

// this would be the method to put in the index page for accounts
echo '<h1>Select all from accounts table</h1>';
$obj = new account;
$obj->save();
$obj=accounts::create();
$records = $obj->findAll();
//$tab=new htmlTable;
//$tab->makeTable($records);
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select all from todos table</h1>';
$obj = new todo;
$obj->save();
$obj=todos::create();
$records = $obj->findAll();
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select one from todos table</h1>';
$obj = new todo;
$obj->save();
$obj=todos::create();
$records = $obj->findOne(1);
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

echo '<h1>Select one from accounts table</h1>';
$obj = new account;
$obj->save();
$obj=accounts::create();
$records = $obj->findOne(1);
htmlTable::createTable($records);
echo '<br>';
echo '<br>';

//echo '<h1>Insert New Row in Todos Column <h1>';
//$obj = new todo;
//$obj->save();
//$records = todos::create();
//$result= $records->findAll();
//table::makeTable($result);
//echo '<br>';
//echo '<br>';

echo '<h1>Update Phone Column in Accounts Table where ID is : 8 <h1>';
$obj = new account;
$obj->save();
$obj = accounts::create();
$result = $obj->findAll();
htmlTable::createTable($result);
echo '<br>';
echo '<br>';

echo '<h1>Delete ID 6 from Todos Table <h1>';
$obj = new todo;
$obj->save();
$records = todos::create();
$result= $records->findAll();
htmlTable::createTable($result);
//table::makeTable($result);

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