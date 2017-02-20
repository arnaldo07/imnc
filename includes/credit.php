<?php
/**
  * Responsible for handling credits
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Initialize
require_once("initialize.php");

class Credit{

  /**
  * Logs table name
  * @var string
  * @access public
  * **/
  public static $table_name = "credit";

  /**
  * Credit ID
  * @var integer
  * @access public
  * **/
  public $credit_id = "";

  /**
  * Credit description
  * @var String
  * @access public
  * **/
  public $credit_description;

  /**
  * Credit amount
  * @var decimail (12, 2)
  * @access public
  * **/
  public $credit_amount;

  /**
  * Credit date
  * @var date
  * @access public
  * **/
  public $credit_date;

  /**
  * Credit session
  * @var string
  * @access public
  * **/
  public $credit_session;

  /**
  * Credit account id
  * @var int
  * @access public
  * **/
  public $credit_account_id;


  /**
  * Finds all logs in the database
  * @global Instance   $database   MySQLDatabase class instance
  * @return object_array
  * @access public
  * **/
  public static function find_all() {
    global $database;
    return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }

  /**
  * Finds all logs in the database by ID
  * @param  integer    $id         ID to search for
  * @global instance   $database   MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function find_by_id($id=0){
    global $database;
    //Espace mysql string
    $escaped_id   = $database->escape_value($id);
    //Fetch results from DB
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE credit_id={$escaped_id} LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Finds by account ID
  * @param  integer    $id         Account ID to search for
  * @global instance   $database   MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function find_by_account_id($id=0){
    global $database;
    //Espace mysql string
    $escaped_id   = $database->escape_value($id);
    //Fetch results from DB
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE credit_account_id={$escaped_id} LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }


  /**
  * Finds Admins by sql query
  * @param  string     $sql        The sql query
  * @global instance   $database   MySQLDatabase class instance
  * @return object_array
  * @access public
  * **/
  public static function find_by_sql($sql=""){
    global $database;
    //Run sql query
    $result_set = $database->query($sql);
    //Fetch and Instatiate
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
  }

  /**
   * credit amount sum for each session
   * @param $user_id   User ID
   * @param $session_id Seesion ID
   * @global instance $database        MySQLDatabase class instance
   * @return array
   * @access public
   * **/
   public static function sum($user_id, $session_id){
     global $database;
     //Espace mysql string
     $escaped_user_id   = $database->escape_value($user_id);
     $escaped_session_id   = $database->escape_value($session_id);
     //Query
     $sql = "SELECT sum(credit_amount) FROM ".Credit::$table_name." ";
     $sql.= " WHERE credit_session = '{$session_id}' AND credit_account_id = '{$user_id}'";
     //Fetch results from DB
     return $database->get_from_db($sql);

   }

   /**
    * Returns monthly ordered cashflow for current year
    * @param  string   $description     Credit description
    * @global instance $database        MySQLDatabase class instance
    * @return array
    * @access public
    * **/
    public static function monthly_flow($id, $year=""){
      global $database;
      //Query
      $sql = " SELECT MONTH(".MySQLDatabase::$calendar.".datefield) AS 'DATE', IFNULL(SUM(".self::$table_name.".credit_amount),0.00) AS 'credit_amount', ";
      $sql.= " (SELECT count_description FROM ".Count::$table_name." WHERE count_id = {$id} LIMIT 1 ) as credit_description FROM ".self::$table_name." JOIN ".Count::$table_name." RIGHT JOIN ".MySQLDatabase::$calendar;
      $sql.= " ON (DATE(".self::$table_name.".credit_date)) = ".MySQLDatabase::$calendar.".datefield and count_id = credit_count_id ";
      $sql.= " and count_id = '{$id}' and YEAR(credit_date) = {$year}  WHERE ";
      $sql.= " YEAR(".MySQLDatabase::$calendar.".datefield) = {$year} GROUP BY MONTH(".MySQLDatabase::$calendar.".datefield) ";
      return self::find_by_sql($sql);
    }


    /**
     * Total credit sum per month
     * @global instance $database        MySQLDatabase class instance
     * @return array
     * @access public
     * **/
     public static function monthly_total($year){
       global $database;
       //Query
       $sql = "SELECT MONTH(".MySQLDatabase::$calendar.".datefield) AS 'DATE', IFNULL(SUM(".self::$table_name.".credit_amount),0.00) AS 'credit_amount' ";
       $sql.= " FROM ".self::$table_name." RIGHT JOIN ".MySQLDatabase::$calendar." ON (DATE(".self::$table_name.".credit_date)) ";
       $sql.= " = ".MySQLDatabase::$calendar.".datefield  and YEAR(credit_date) = {$year} WHERE  ";
       $sql.= " YEAR(".MySQLDatabase::$calendar.".datefield) = {$year} GROUP BY MONTH(".MySQLDatabase::$calendar.".datefield)";
       return self::find_by_sql($sql);
      }


  /**
  * Stores invited Admin to the database
  * @param  string   $credit_description  Credit description
  * @param  decimal(12,2)   $credit_amount       Credit description
  * @param  date  $credit_date         Credit date
  * @param  string   $credit_session      Credit session
  * @param  int   $credit_account_id   Credit account id
  * @global instance $database        MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function create($credit_count_id, $credit_amount, $credit_date, $credit_session, $credit_account_id) {
    global $database;
    //Espace mysql string
    $escaped_credit_count_id       = $database->escape_value($credit_count_id);
    $escaped_credit_amount         = $database->escape_value($credit_amount);
    $escaped_credit_date           = $database->escape_value($credit_date);
    $escaped_credit_session        = $database->escape_value($credit_session);
    $escaped_credit_account_id     = $database->escape_value($credit_account_id);
    //Query
    $sql  = "INSERT INTO ";
    $sql .= self::$table_name." ";
    $sql .= "( credit_count_id, credit_amount, credit_date, credit_session, credit_account_id )";
    $sql .= "VALUES ('{$escaped_credit_count_id}', '{$escaped_credit_amount}', '{$escaped_credit_date}', '{$escaped_credit_session}', '{$escaped_credit_account_id}' )";
    //Run query
    if($database->query($sql)){ return true; } else { return false; }
  }


  /**
  * Instatiates
  * @param  array   $record   Array to be Instatiated
  * @return object
  * @access private
  * **/
  private static function instantiate($record) {
    $object = new self;
    foreach($record as $attribute=>$value){
      if($object->has_attribute($attribute)){
        $object->$attribute = $value;
      }
    }
    return $object;
  }

  /**
  * Gets object variables and check if it exists
  * @param  Any type   $attribute     Object variable to be checkens
  * @return array keys
  * @access private
  * **/
  private function has_attribute($attribute){
    $object_vars = get_object_vars($this);
    return array_key_exists($attribute, $object_vars);
  }
}



?>
