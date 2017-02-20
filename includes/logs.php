<?php
/**
  * Responsible for handling all logs tracking
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Initialize
require_once("initialize.php");

class Logs{

  /**
  * Logs table name
  * @var string
  * @access public
  * **/
  public static $table_name = "logs";

  /**
  * Log ID
  * @var integer
  * @access public
  * **/
  public $log_id = "";

  /**
  * Log location
  * @var String
  * @access public
  * **/
  public $log_location;

  /**
  * Log user agent
  * @var string
  * @access public
  * **/
  public $log_user_agent;

  /**
  * Log user ip
  * @var string
  * @access public
  * **/
  public $log_user_ip;

  /**
  * Log user device type
  * @var string
  * @access public
  * **/
  public $log_user_device_type;

  /**
  * Log date and time
  * @var datetime
  * @access public
  * **/
  public $log_datetime;

  /**
  * Log account ID
  * @var integer
  * @access public
  * **/
  public $log_account_id;


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
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE log_id={$escaped_id} LIMIT 1");
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
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE log_account_id={$escaped_id} LIMIT 1");
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
  * Stores invited Admin to the database
  * @param  string   $log_location   Location where login ocurred
  * @param  string   $log_user_agent Log user agent
  * @param  string   $log_user_ip    Log user ip
  * @param  string   $log_user_device_type Log device type
  * @param  string   $log_account_id  Log account ID
  * @global instance $database        MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function create($log_location, $log_user_agent, $log_user_ip, $log_device_type, $log_account_id) {
    global $database;
    //Espace mysql string
    $escaped_log_location      = $database->escape_value($log_location);
    $escaped_log_user_agent    = $database->escape_value($log_user_agent);
    $escaped_log_user_ip       = $database->escape_value($log_user_ip);
    $escaped_log_device_type   = $database->escape_value($log_device_type);
    $escaped_log_account_id    = $database->escape_value($log_account_id);
    //Query
    $sql  = "INSERT INTO ";
    $sql .= self::$table_name." ";
    $sql .= "( log_location, log_user_agent, log_user_ip, log_device_type, log_account_id )";
    $sql .= "VALUES ('{$escaped_log_location}', '{$escaped_log_user_agent}', '{$escaped_log_user_ip}', '{$escaped_log_device_type}', '{$escaped_log_account_id}' )";
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
