<?php
/**
  * Handles all login process
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

/**
  * Require initialize
**/
require_once("../includes/initialize.php");

/**
  * Deny login if login attemps more than 7
**/
if(cookie::login_attempts_count() > 7 ){ //Be aware that cookie delays one request to setup
  redirect_to('ActivateAccount');
}

/**
  *Checks if is set authentication cookie
**/
if(isset($_COOKIE[Cookie::$login_cookie])){
  //json decode array cookie
  $auth_cookie = json_decode($_COOKIE[Cookie::$login_cookie]);
  //get indexes for cookie
  $user_id    = Cookie::$user_id;
  $token      = Cookie::$token;
  //get cookie values
  $user_id    = $auth_cookie->user_id;
  $token      = $auth_cookie->token;
  //authenticate
  if($cookie->auth($user_id, $token)){
    //Generate a token
    $time_hash = strftime("%Y%m%d %H:%M:%S", time());
    $token = md5(crypt("{$user_type}{$time_hash}{$user_id}"));
    //reset authenticattion cookie
    $cookie->keep_logged_in($token, $user_id);
    //Find Admin company id
    $admin = Admin::find_by_id($user_id);
    //Session login
    $session->login($user_id);
    //redirect to dashboard
    redirect_to('Home.php'); //Make you reload/redirect to be able to get the cookie in the next request
  }
} else {
  /** If is logged in redirect to homepage */
  if ($session->is_logged_in()) { redirect_to('Home.php'); }
}

/**
  * Handles all login process
**/
if(isset($_POST['submit'])){
  $username  = $_POST['username'];
  $password  = $_POST['password'];
  //Get post keep-session (check box input index)
  isset($_POST['keep-session'])? $keep_signed = TRUE : $keep_signed = FALSE;

  //Encrypt pass as was encrypted on register
  $pass = sha1(md5($password));
  $pass = $password;
  // Authenticate
  $found_user = Admin::authenticate($username, $pass);
  if(!empty($found_user)){
    //if account admins is active
    if($account_is_active = Admin::account_active($found_user) == TRUE){
      /** Login user */
      if($session->login($found_user)){
        if($keep_signed == TRUE){
          //Generate a token
          $time_hash = strftime("%Y%m%d %H:%M:%S", time());
          $token = md5(crypt("{$authenticated}{$time_hash}{$found_user}"));
          //Keep signed in
          Cookie::keep_logged_in($token, $found_user);
        }
        // set log
        Logs::create($log_location='', $log_user_agent='', client_ip(), $log_device_type='', $found_user);
        //Redirect to homepage
        redirect_to('Home.php');
      }
    } else {
      // Accountbis not active
      $report = "A sua conta não está activada. Contacte o aedministrador.";
    }

  } else {
    //If $message has nothing assigned
    if(empty($message)){
      //Return message
      $report = "Email e/ou senha não correspondem.";
    }
    //set login attempts
    Cookie::set_login_attempts();
    /**
    *set event too here
    */

  }
  //Return inserted username
  $username = $_POST['username'];

} else {
  // return null values
  $username  = NULL;
  $password  = NULL;
  $keep_login= NULL;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="">
  <script src="js/script.js"></script>
  <link rel="stylesheet" href="static/stylesheets/index.css">
  <title>Iniciar Sessão</title>
</head>
<body class="login-page">
  <header class="main-header">
    <div class="header-content"><span class="logo">IMNC</span></div>
  </header>
  <div class="main-login-page-container">
    <div class="login-div-form">
      <div class="login-form-header">
        <h1>Bem Vindo ao IMNC Manager</h1>
        <p>Inicie a sessão para começar as actividades</p>
      </div>
      <div class="login-form-container">
        <form method="POST" action="index.php" class="login-form">
          <div class="form-row">
            <input type="text" name="username" placeholder="E-mail ou telemóvel" class="username full-width" value="<?php isset($username)? print(htmlentities($username)) : NULL; //return values ?>"/>
          </div>
          <div class="form-row">
            <input type="password" name="password" placeholder="Senha" autocomplete="off" class="password full-width" value="<?php isset($password)? print(htmlentities($password)) : NULL; //return values ?>"/>
          </div>
          <div class="form-row">
            <input type="checkbox" name="keep-session" class="keep-login" value="TRUE"/><span class="keep-login">Manter sessão iniciada.</span>
          </div>
          <div class="form-row">
            <span class="credential-error"><?php isset($report)? print(output_message($report)) : NULL; //print report message ?></span>
          </div>
          <div class="form-row">
            <input type="submit" name="submit" value="Iniciar sessão" class="submit full-width"/>
          </div>
          <div class="form-row"><a class="forgot-pass" href="#">Esqueceu senha ?</a></div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
