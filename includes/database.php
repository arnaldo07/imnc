<?php
/**
  * Handles MySQL database connection and manipuladion
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//initialize
require_once("initialize.php");

class MySQLDatabase{

  /**
  * Calendar table name
  * @access public
  **/
  public static $calendar = "calendar";

  /**
  * Private connection to access only inside the object
  * @access private
  **/
  private $connection;

  /**
  * Mysql calendar table
  * @var string
  * @access public
  * **/

  /**
  * Construct open connection function
  * **/
  function __construct(){
    $this->open_connection();
  }

  /**
   * Open DB coonection
   * @access private
   * **/
  private function open_connection(){
    $this->connection= mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    $this->connection->query('SET NAMES utf8');
    if(mysqli_connect_errno()){
      die("database connection failed:  ".
      mysqli_connect_error() .
      "(" . mysqli_connect_errno() . ")"
    );
  }
}

/**
 * Close DB connection
 * @access private
 * **/
private function close_connection(){
  if (isset ($this->connection)){
    mysqli_close($this->connection);
    unset($this->connection);
  }
}

/**
 * Submitting query to MySql
 * @param string  $sql  The Mysql query
 * @return boolean
 * @access public
 * **/
public function query($sql){
  $result = mysqli_query($this->connection, $sql);
  $this->confirm_query($result);
  return $result;
}

/**
 * Confirm query
 * @param boolean  $result  Query result
 * @access public
 * **/
private function confirm_query($result){
  if (!$result){
    die("Database query failed: ".mysqli_error($this->connection));
  }
}

/**
 * Query and fetch single row
 * @$param string $sql  The sql query
 * @access public
 * **/
public function get_from_db($sql){
  $result_array = $this->fetch_array($this->query($sql));
  return !empty($result_array) ? array_shift($result_array) : false;
}

/**
 * Escape from unwanted string
 * @param string $string  The mysql string to be escaped_string
 * @access public
 * @return string
 * **/
public function escape_value($string){
  $escaped_string= mysqli_real_escape_string($this->connection, $string);
  return $escaped_string;
}

/**
 * Fetch query array result array
 * @param array $query_result   The query result
 * @access public
 * @return array or FALSE
 * **/
public function fetch_array($query_result){
  $found_result= mysqli_fetch_array($query_result);
  if (!empty($found_result)){
    return $found_result;
  } else {
    return FALSE;
  }
}

/**
 * Gets last calendar inserted date
 * @access public
 * @return date
**/
public function last_calendar_date(){
$sql = "SELECT max(datefield) FROM ".self::$calendar;
return $result = $this->get_from_db($sql);
}

/**
 * Inserts date intervals into calendar table
 * running sql stored procedure
 * @param date  $startdate   Interval start date
 * @param date  $enddate     Interval end date
 * @access public
 * @return boolean
**/
public function add_to_calendar(){
$last_calendar_date = $this->last_calendar_date();
$startdate = date('Y-m-d', strtotime($last_calendar_date."+1 day"));
$endate   = date('Y-m-d', strtotime($startdate."+10 year"));
$sql = "CALL fill_calendar({$startdate}, {$endate})";
if($res = $this->query($sql)){
} else {
  return FALSE;
}
}


/**
 * Number of affected rows
 * @param array $result_set   The query result set
 * @access public
 * @return integer
 * **/
public function num_rows($result_set){
  return mysqli_num_rows($result_set);
}

/**
 * Last inserted id
 * @access public
 * @return integer
 * **/
public function insert_id (){
  return mysqli_insert_id($this->connection);
}

/**
 * Affected rows
 * @access public
 * **/
public function affected_rows(){
  return mysqli_affectted_rows($this->connection);
}


}


//Instatiate the object
$database = new MySQLDatabase();


?>
