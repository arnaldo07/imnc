<?php
/**
  * Handles session related functions
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Sesssion class
class Session {

  //class variables
  private $logged_in = FALSE;
  public  $user_id;
  public  $user_type;
  public  $session_id;
  public  $message;

  /**
  * Index of session variable to be set throught requests instead of url set values;
  *
  * @type string
  */
  public static $SESSION_POST = 'SessionPost';


  /**
  * Constructs
  *
  */
  function __construct(){
    session_start();
    $this->check_message();
    $this->check_login();

  }


  /**
  * Returns TRUE if is logged in else FALSE
  *
  */
  public function is_logged_in(){
    return $this->logged_in;
  }

  /**
  * Logs user session in
  *
  * @param     Int       $found_user       ID of user to be logged in
  * @param     int       $session_id       session IO
  */
  public function login($found_user, $user_type, $session_id){
    if ($found_user){
      $this->user_id     = $_SESSION['user_id'] = $found_user;
      $this->user_type   = $_SESSION['user_type'] = $user_type;
      $this->session_id  = $_SESSION['session_id'] = $session_id;
      //set login true
      $this->logged_in = true;
      //return TRUE
      return TRUE;
    }
  }

  /**
  * Logs user session out
  *
  */
  public function logout(){
    unset($_SESSION['user_id']);
    unset($_SESSION['user_type']);
    unset($_SESSION['session_id']);
    unset($this->user_id);
    unset($this->session_id);
    $this->logged_in = false;
  }


  /**
  * Check if session id is set and make $this->logged_in = true
  *
  */
  private function check_login(){
    if (isset($_SESSION['user_id'])){
      $this->user_id    = $_SESSION['user_id'];
      $this->user_type  = $_SESSION['user_type'];
      $this->session_id = $_SESSION['session_id'];
      $this->logged_in  = TRUE;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }


  /**
  * Handles session messages
  *
  * @param string $msg The message content
  *
  */
  public function message($msg=""){
    if (!empty($msg)){
      $_SESSION['message']=$msg;
    } else {
      $this->message;
    }
  }

  /**
  * Sets values to sent to next requests
  *
  * @param  array   $values    Array of values to sent to next request
  */
  public static function session_post($values){
    if (!empty($values)){
      $_SESSION[self::$SESSION_POST] = $values;
    }
  }


  /**
  * Unsets session_post() function values
  *
  */
  public static function unset_session_post(){
    if (isset($_SESSION[self::$SESSION_POST])){
      unset($_SESSION[self::$SESSION_POST]);
    }
  }


  /**
  * Checks session message and unset before next request
  *
  */
  private function check_message(){
    if (isset($_SESSION['message'])){
      $this->message = $_SESSION['message'];
      unset($_SESSION['message']);
    } else {
      $this->message = "";
    }
  }




}


//** Instatiate Session */
$session= new Session();
$message = $session->message();

//END

?>
