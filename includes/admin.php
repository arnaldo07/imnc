<?php
/**
  * Responsible for handling all Admins functions
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Initialize
require_once("initialize.php");

class Admin{

  /**
  * admin table name
  * @var string
  * @access public
  * **/
  public static $table_name = "admin";

  /**
  * Admin ID
  * @var integer
  * @access public
  * **/
  public $admin_id = "";

  /**
  * Admin name
  * @var String
  * @access public
  * **/
  public $admin_first_name;

  /**
  * Admin surname
  * @var string
  * @access public
  * **/
  public $admin_last_name;

  /**
  * Admin email
  * @var string
  * @access public
  * **/
  public $admin_email;

  /**
  * Admin phone number
  * @var integer
  * @access public
  * **/

  public $admin_password;

  /**
  * Admin status
  * @var string
  * @access public
  * **/
  public $admin_status;

  /**
  * Admin token
  * @var string
  * @access public
  * **/
  public $admin_token;

  /**
  * Admin admin type
  * @var string
  * @access public
  * **/
  public $admin_type;


  /**
  * Admin account creation date
  * @var string
  * @access public
  * **/
  public $admin_created_on;

  /**
  * Admin account type Master
  * Master account has full access previleges
  * @var string
  * @access public
  * **/
  public $admin_master = "Master";

  /**
  * Admin account type Base
  * Base account has only access for write and read.
  * @var string
  * @access public
  * **/
  public $admin_base = "Base";

  /**
  * Admin account type Simple
  * Simple account has only access for write
  * @var string
  * @access public
  * **/
  public $admin_simple = "Simple";




  /**
  * Finds all Admins in the database
  * @global Instance   $database   MySQLDatabase class instance
  * @return object_array
  * @access public
  * **/
  public static function find_all() {
    global $database;
    return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }

  /**
  * Finds all Admins limited set of rows
  * @params int        $limit      Limit rows to be returned
  * @global Instance   $database   MySQLDatabase class instance
  * @return object_array
  * @access public
  * **/
  public static function find_with_limit($limit) {
    global $database;
    return self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE admin_status != 'Eliminado' LIMIT {$limit}");
  }

  /**
  * Finds all Admins in the database by ID
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
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE admin_id={$escaped_id} LIMIT 1");
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
  * Finds admins by email
  * @param  string     $email        The email
  * @global Instance   $database     MySQLDatabase Instance
  * @return object
  * @access public
  * **/
  public static function find_by_email($email=""){
    global $database;
    //Espace mysql string
    $escaped_email   = $database->escape_value($email);
    //Run query
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE admin_email='{$escaped_email}' LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Finds admins by search key
  * @param  string     $search       The search key
  * @global Instance   $database     MySQLDatabase Instance
  * @return object
  * @access public
  * **/
  public static function search($search=""){
    global $database;
    //Espace mysql string
    $escaped_search  = $database->escape_value($search);
    //Run query
    $sql  = "SELECT * FROM ".self::$table_name." WHERE admin_first_name  LIKE '%{$escaped_search}%' OR ";
    $sql .= " admin_last_name  LIKE '%{$escaped_search}%' OR admin_email LIKE '%{$escaped_search}%' AND admin_status != 'Eliminado' ";
    return self::find_by_sql($sql);
  }

  /**
  * Authenticates by token
  * @param  string   $token Token  The token
  * @global instance $database     MySQLDatabase class instance
  * **/
  public static function auth_by_token($token=""){
    global $database;
    //Mysql escape string
    $escaped_token  =   $database->escape_value($token);
    //Query
    $sql  = " SELECT admin_id FROM ".self::$table_name;
    $sql .= " WHERE admin_token = '{$escaped_token}' LIMIT 1";
    $result = $database->query($sql);
    $result_array = $database->fetch_array($result);
    //return result
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Finds by token
  * @param  integer  $id    The ID
  * @param  string   $token Token  The token
  * @global instance $database     MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function find_by_token($id="", $token=""){
    global $database;
    //Espace mysql string
    $escaped_id         = $database->escape_value($id);
    $escaped_token      = $database->escape_value($token);
    //Run query
    $result_array = self::find_by_sql("SELECT admin_id FROM ".self::$table_name."  WHERE admin_id = '{$escaped_id}' AND admin_token='{$escaped_token}' LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Finds by token
  * @param  string   $username Username
  * @param  string   $token Token  The token
  * @global instance $database     MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function verify_username_token($username="", $token=""){
    global $database;
    //Espace mysql string
    $escaped_username   = $database->escape_value($username);
    $escaped_token      = $database->escape_value($token);
    //Run query
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name."  WHERE admin_first_name = '{$escaped_username}' AND admin_token='{$escaped_token}' LIMIT 1");
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Checks if account is activated
  * @param  string   $username     The username
  * @param  string   $token Token  The token
  * @global instance $database     MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function account_active($id=""){
    global $database;
    //Espace mysql string
    $escaped_id   = $database->escape_value($id);
    //Status
    $status = "Activo";
    //query
    $sql  = "SELECT * FROM ".self::$table_name." ";
    $sql .= "WHERE ";
    $sql .= "admin_id      = '{$escaped_id}' ";
    $sql .= "AND ";
    $sql .= "admin_status = '{$status}' ";
    $sql .= " LIMIT 1 ";
    $result = $database->query($sql);
    $result_array = $database->fetch_array($result);
    return !empty($result_array) ? true : false;
  }


  /**
  * Finds by email or phone number
  * @param  int or string    $username   email or phone number
  * @global instance         $database   MysqlDatabase class instance
  * @return object or Boolean
  * @access public
  */
  public static function find_by_usernames($username = ""){
    global $database;
    $escaped_username = $database->escape_value($username);
    $sql  = "SELECT * FROM ";
    $sql  .= self::$table_name;
    $sql  .= " WHERE admin_email LIKE '{$escaped_username}' OR admin_phone LIKE '{$escaped_username}' LIMIT 1 ";
    $result_array = self::find_by_sql($sql);
    return !empty($result_array)? array_shift($result_array) : FALSE;
  }


  /**
  * Authenticated Admins
  * @param  string   $username     The username
  * @param  string   $password     The passwsord
  * @global instance $database     MySQLDatabase class instance
  * @return object
  * @access public
  * **/
  public static function authenticate($username="", $password="") {
    global $database;
    //Escape string
    $escaped_username    = $database->escape_value($username);
    $escaped_password    = $database->escape_value($password);
    //sql query
    $sql  = "SELECT * FROM ";
    $sql .= self::$table_name." ";
    $sql .= "WHERE        admin_email    = '{$escaped_username}' ";
    $sql .= "AND        admin_password   = '{$escaped_password}' ";
    $sql .= "LIMIT 1 ";
    //Run query
    $result_array = self::find_by_sql($sql);
    return !empty($result_array) ? array_shift($result_array) : false;
  }

  /**
  * Stores invited Admin to the database
  * @param  string   $admin_name     Admin name
  * @param  string   $admin_surname  Admin surname
  * @param  string   $admin_email    Admin email
  * @param  string   $admin_type     Admin type
  * @param  string   $token          Admin token for activation
  * @global instance $database       MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function create($admin_name, $admin_surname, $admin_email, $account_type, $token) {
    global $database;
    //Espace mysql string
    $escaped_admin_name      = $database->escape_value($admin_name);
    $escaped_admin_surname   = $database->escape_value($admin_surname);
    $escaped_admin_email     = $database->escape_value($admin_email);
    $escaped_admin_account_type = $database->escape_value($account_type);
    $escaped_admin_token     = $database->escape_value($token);
    //Query
    $sql  = "INSERT INTO ";
    $sql .= self::$table_name." ";
    $sql .= "( admin_first_name, admin_last_name, admin_email, admin_token, admin_type )";
    $sql .= "VALUES ('{$escaped_admin_name}', '{$escaped_admin_surname}', '{$escaped_admin_email}', ";
    $sql .= "'{$escaped_admin_token}', '{$escaped_admin_account_type}' )";
    //Run query
    if($database->query($sql)){ return true; } else { return false; }
  }


  /**
   * Updates Admin status
   * @param  string     $status   New Admin status
   * @param  string     $token    Admin token to identify admin to be updated
   * @global instance   $database MySQLDatabase class instance
   * @return Boolean
   * @access public
   * **/
  public static function update_status($status, $token){
    global $database;
    //Espace mysql string
    $escaped_status   = $database->escape_value($status);
    $escaped_token    = $database->escape_value($token);
    //Sql query
    $sql  = "UPDATE ";
    $sql .= self::$table_name." ";
    $sql .= "SET ";
    $sql .= "admin_status            = '{$escaped_status}', ";
    $sql .= "admin_token             = '' ";
    $sql .= "WHERE ";
    $sql .= "admin_token             = '{$escaped_token}' ";
    //Run query
    return $database->query($sql);
  }


  /**
   * Updates token
   * @param  integer  $user_id    Admin id
   * @param  string   $token      Admin token
   * @global instance $database   MySQLDatabase class instance
   * @return Boolean
   * @access public
   * **/
  public static function update_token($user_id, $token){
    global $database;
    //Espace mysql string
    $escaped_user_id   = $database->escape_value($user_id);
    $escaped_token     = $database->escape_value($token);
    //Query
    $sql  = "UPDATE ";
    $sql .= self::$table_name." ";
    $sql .= "SET ";
    $sql .= "admin_token           = '{$escaped_token}' ";
    $sql .= "WHERE ";
    $sql .= "admin_id              = '{$escaped_user_id}' ";
    //Run query
    return $database->query($sql);
  }

  /**
   * Sets up account phone number, password and email confirmation to true
   * @param   integer   $admin_id           The admin ID
   * @param   integer   $admin_phone        Admin phone
   * @param   string    $admin_password     Admin password
   * @param   Boolean   $emauil_confirmed   True if email was confirmed else false
   * @global instance $database   MySQLDatabase class instance
   * @return Boolean
   * @access public
   * **/
  public static function setup_account($admin_id, $admin_password){
    global $database;
    //Define some variables
    $status = "Activo"; //set account status to active
    $token  = ""; //set token empty as it will be updated if necessary to use it
    //Espace mysql string
    $escaped_admin_id          = $database->escape_value($admin_id);
    $escaped_admin_password    = $database->escape_value($admin_password);
    //Sql query
    $sql  = "UPDATE ";
    $sql .= self::$table_name." ";
    $sql .= "SET ";
    $sql .= "admin_password           = '{$escaped_admin_password}', ";
    $sql .= "admin_status             = '{$status}', ";
    $sql .= "admin_token              = '{$token}', ";
    $sql .= "WHERE ";
    $sql .= "admin_id                 = '{$escaped_admin_id}' ";
    //Run query
    if($database->query($sql)){ return true; } else { return fales; }
  }


  /**
  * Sets admin to deleted status
  * @param id    $admin_id
  * @global instance $database     MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function delete_by_id($admin_id){
    global $database;
    //Mysql escape string
    $escaped_admin_id  =   $database->escape_value($admin_id);
    //Query
    $sql  = "UPDATE ".self::$table_name;
    $sql .= " SET admin_status = 'Eliminado' ";
    $sql .= " WHERE admin_id = '{$escaped_admin_id}' ";
    $result = $database->query($sql);
    return $database->query($sql)? TRUE : FALSE;
  }

  /**
  * Updates admin status
  * @param int      $admin_id      ID of admin
  * @param string   $admin_status   Admin status
  * @global instance $database     MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function update_status_by_id($admin_id, $admin_status){
    global $database;
    //Mysql escape string
    $escaped_admin_id      =   $database->escape_value($admin_id);
    $escaped_admin_status  =   $database->escape_value($admin_status);
    //Query
    $sql  = "UPDATE ".self::$table_name;
    $sql .= " SET admin_status = '{$escaped_admin_status}' ";
    $sql .= " WHERE admin_id = '{$escaped_admin_id}' ";
    $result = $database->query($sql);
    return $database->query($sql)? TRUE : FALSE;
  }

  /**
  * Updates admin token
  * @param  int      $admin_id      ID of admin
  * @param  string   $admin_email   Admin email
  * @global instance $database     MySQLDatabase class instance
  * @return Boolean
  * @access public
  * **/
  public static function new_token($admin_id, $admin_email, $admin_token ){
    global $database;
    //Mysql escape string
    $escaped_admin_id      =   $database->escape_value($admin_id);
    $escaped_admin_email  =   $database->escape_value($admin_email);
    $escaped_admin_token  =   $database->escape_value($admin_token);
    //Query
    $sql  = "UPDATE ".self::$table_name;
    $sql .= " SET admin_token = '{$escaped_admin_token}' ";
    $sql .= " WHERE admin_id = '{$escaped_admin_id}' AND ";
    $sql.= "admin_email = '{$escaped_admin_email}' ";
    return $database->query($sql)? TRUE : FALSE;
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
