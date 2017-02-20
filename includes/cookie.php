<?php
/**
* Manage all cookie related procedures and function
* @package IMNC Manager
* @link https:// github.com/      The github project
* @author Arnaldo Govene <arnaldo.govene@outlook.com>
* @copyright 2016 Xindiri
* @license
**/

class Cookie {


  /**
  * Token to check login cookie index
  * @type string
  */
  public static $login_cookie     = "LoginCheckCookie";

  /**
  * Log attempts count cookie index
  * @type string
  */
  public static $loggin_attempts = "LoginAttempts";

  /**
  * User id cookie index
  * @type string
  */
  public static $user_id   = "user_id";

  /**
  * User type cookie index
  * @type string
  */
  public static $user_type  = "user_type";

  /**
  * Token cookie index
  * @type string
  */
  public static $token     = "token";

  /**
  * Set on cookie index
  * @type string
  */
  public static $set_on    = "set_on";




  /**
  * Set token to user database
  * and cookie to authenticate user and keep him logged in
  *
  * @param  varchar        $token      Token to authenticate user login
  * @param  int            $user_id    ID of the user to be keepen logged in
  * @param  string         $user_type  Type of user
  */
  public static function keep_logged_in($token="", $user_id){
    if(isset($token)){

      //set array of data to set in cookie
      $login_cookie_val = array(
        'user_id'   => $user_id,
        'token'     => $token,
        'set_on'    => time() //current time
      );

      //json encode array
      $login_cookie_val = json_encode($login_cookie_val);

      Admin::update_token($user_id, $token); //set MasterAdmin token
      setCookie(self::$login_cookie, $login_cookie_val, time()+86400*365, '/'); //set token cookie valid for 1 year
    }
  }


  /**
  * Authenticates user with cookie pre set token every time is requested
  *
  * @param int     $user_uid   ID of user
  * @param string  $user_type  Type of user
  * @param varchar $token      Token
  */

  public function auth($user_id, $token){
    if(isset($user_id)){
      $found_user = Admin::find_by_token($user_id, $token);
      if(!empty($found_user)){ //return TRUE if not empty $found_user else return FALSE
        return TRUE;
      } else {
        return FALSE;
      }
    }
  }


  /**
  * Sets login attempts count
  *
  */
  public static function set_login_attempts(){
    if(isset($_COOKIE[self::$loggin_attempts])){
      $attempts_count = $_COOKIE[self::$loggin_attempts] + 1;
      setCookie(self::$loggin_attempts, $attempts_count, time()+3600*24, '/'); //token cookie expires in 24hours
    } else {
      $attempts_count = 1;
      setCookie(self::$loggin_attempts, $attempts_count, time()+3600*24, '/'); //token cookie expires in 24hours
    }
  }

  /**
  * Get login attempts count
  *
  */
  public static function login_attempts_count(){
    if(isset($_COOKIE[self::$loggin_attempts])){
      return $_COOKIE[self::$loggin_attempts];
    }
  }

  /**
  * Unset login attempts count
  *
  */
  public static function unset_login_attempts(){
    setCookie(self::$loggin_attempts, '', time()-3600, '/'); //Expire cookie
  }

  /**
  * Unsets authentication cookie
  *
  */
  public static function logout(){
    setCookie(self::$login_cookie, '', time()-3600, '/'); //expire token cookie
  }




}

//Instatiate Session
$cookie= new Cookie();


?>
