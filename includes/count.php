<?php
/**
  * Responsible for handling counts (accounts)
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Initialize
require_once("initialize.php");

class Count{

  /**
  * count table name
  * @var string
  * @access public
  * **/
  public static $table_name = "counts";

  /**
  * count ID
  * @var integer
  * @access public
  * **/
  public $count_id = "";

  /**
  * count description
  * @var String
  * @access public
  * **/
  public $count_description;

  /**
  * count type
  * @var string
  * @access public
  * **/
  public $count_type;

  /**
  * Finds all counts in the database
  * @global Instance   $database   MySQLDatabase class instance
  * @return object_array
  * @access public
  * **/
  public static function find_all() {
    global $database;
    return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }

  /**
  * Finds all counts in the database by ID
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
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE count_id={$escaped_id} LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Finds all counts in the database by type
  * @param  string    $type         type to search for
  * @global instance   $database   MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function find_by_type($type){
    global $database;
    //Espace mysql string
    $escaped_type   = $database->escape_value($type);
    //Fetch results from DB
    return  self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE count_type='{$escaped_type}' ");
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


  public static function create($count_description, $count_type) {
    global $database;
    //Espace mysql string
    $escaped_count_description    = $database->escape_value($count_description);
    $escaped_count_type           = $database->escape_value($count_type);
    //Query
    $sql  = "INSERT INTO ";
    $sql .= self::$table_name." ";
    $sql .= "( count_description, count_type, )";
    $sql .= "VALUES ('{$escaped_count_description}', '{$escaped_count_amount}')";
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
