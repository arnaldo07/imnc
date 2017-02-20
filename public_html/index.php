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

// check session
if ($session->is_logged_in()){redirect_to('home.php');}

/**
* Add dates to calendar
**/
// $cur_date = date("Ymd");
// $update_date = date("Y-12-20");
// if($cur_date >= $update_date){
//   $database->add_to_calendar();
// }


/**
* Deny login if login attemps more than 7
**/
if(cookie::login_attempts_count() > 7 ){ //Be aware that cookie delays one request to setup
  redirect_to('ActivateAccount');
}

/**
*Checks if is set authentication cookie
**/
if (!$session->is_logged_in()){
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
      //POST log data in login form using javascript
      $get_log_info = TRUE;
      //Generate a token
      $time_hash = strftime("%Y%m%d %H:%M:%S", time());
      $random    = mt_rand(10000, 99999);
      $token = md5(crypt("{$random}{$time_hash}{$user_id}"));
      //reset authenticattion cookie
      $cookie->keep_logged_in($token, $user_id);
      //Find Admin company id
      $admin = Admin::find_by_id($user_id);
      if(!empty($admin)){
        $session_id = md5(sha1("{$time_hash}{$user_id}"));
        $user_type = $admin->admin_type;
        //Session login
        if($session->login($user_id, $user_type, $session_id)){
          //Get session log data
          isset($_POST['device-type']) ? $device_type = $_POST['device-type'] : $device_type="Não detectado"; // Client Device type
          isset($_POST['browserinfo']) ? $browserinfo = $_POST['browserinfo'] : $browserinfo="Não detectado"; // Client browser info
          isset($_POST['ip']) ? $ipaddress = $_POST['ip'] : $ipaddress=client_ip(); // Client ip address
          isset($_POST['location']) ? $log_location = $_POST['location'] : $log_location="Não detectado"; // Client location
          empty($browserinfo)? $browserinfo = $_SERVER['HTTP-USER-AGENT'] : $browserinfo="Não detectado";

          Logs::create($log_location, $browserinfo, $ipaddress , $device_type, $user_id);

          //redirect to dashboard
          redirect_to('home.php'); //Make you reload/redirect to be able to get the cookie in the next request
        }
      }
    }
  }
}

/**
* Handles all login process
**/
if(isset($_POST['submit'])){
  $username  = $_POST['username'];
  $password  = $_POST['password'];
  //Get post keep-session (check box input index)
  isset($_POST['keep-session'])? $keep_signed = TRUE : $keep_signed = FALSE;

  //Get session log data
  isset($_POST['device-type']) ? $device_type = $_POST['device-type'] : $device_type="Não detectado"; // Client Device type
  isset($_POST['browserinfo']) ? $browserinfo = $_POST['browserinfo'] : $browserinfo="Não detectado"; // Client browser info
  isset($_POST['ip']) ? $ipaddress = $_POST['ip'] : $ipaddress=client_ip(); // Client ip address
  isset($_POST['location']) ? $log_location = $_POST['location'] : $log_location="Não detectado"; // Client location

  //Encrypt pass as was encrypted on register
  $pass = sha1(md5($password));
  // Authenticate
  $found_result = Admin::authenticate($username, $pass);
  if(!empty($found_result)){
    $found_user = $found_result->admin_id;
    $user_type = $found_result->admin_type;
    //if account admins is active
    if($account_is_active = Admin::account_active($found_user) == TRUE){
      $time_hash = strftime("%Y%m%d %H:%M:%S", time());
      $session_id = md5(sha1("{$time_hash}{$found_user}"));
      /** Login user */
      if($session->login($found_user, $user_type, $session_id)){
        if($keep_signed == TRUE){
          //Generate a token
          $time_hash = strftime("%Y%m%d %H:%M:%S", time());
          $token = md5(crypt("{$authenticated}{$time_hash}{$found_user}"));
          //Update token
          Admin::update_token($found_user, $token);
          //Keep signed in
          Cookie::keep_logged_in($token, $found_user);
        }
        // set log
        Logs::create($log_location, $browserinfo, $ipaddress , $device_type, $found_user);
        //Redirect to homepage
        redirect_to('home.php');
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

/**
* Handles forgot password staff for retriving
**/
if(isset($_POST['ForgotPass']) & isset($_POST['username'])){
  $username = $_POST['username'];
  $exists_user = Admin::find_by_email($username);
  if(!empty($exists_user)){
    $time_hash = strftime("%Y%m%d %H:%M:%S", time());
    $random    = mt_rand(10000, 99999);
    $token = md5(crypt("{$exists_user->admin_first_name}{$time_hash}{$exists_user->admin_last_name}{$random}")); //Generate token
    if(Admin::new_token($exists_user->admin_id, $exists_user->admin_email, $token)){
      // Send assword retriving email
      //Time
      $time      = time();
      //year
      $year      = strftime('%Y', time());
      $site_public = SITE_PUBLIC;
      $message  .= "<p> Olá {$exists_user->admin_first_name},</p>";
      $message  .= "<p>Recebemos seu pedido de recuperação de conta. Para recuperar e continuar a usa-la, precisa criar uma nova senha ";
      $message  .= ", para proceder clique em <a href='{$site_public}setup_account.php?username={$exists_user->admin_first_name}&key={$token}&verification={$time}'>alterar senha</a>.</p>";
      $message  .= "<p>Se recebeu este e-mail de surpresa, Por favor, elimine-o de imediato e não clique no link acima de forma que o seu email não seja adicionado.</p>";
      $message  .= "<p><strong class='attention'>Nota: </strong><span>O link acima é valido por apenas 24 horas</span>.<p>";
      $message  .= "<br><p>Atenciosamente,</p>";
      $message  .= "<h5 class='team'>Equipe da IMNC</h5>";
      $message  .= "<p style='font-size: 10px;'>Por favor não responda ao e-mail pois o mesmo não recebe respostas.</p>";
      $message  .= "<h5 style='font-size: 10px;'>&copy;{$year}-IMNC</h5>";
      //Other variables
      $to           = $exists_user->admin_email;
      $to_name      = "{$exists_user->admin_first_name} {$exists_user->admin_last_name}";
      $subject      = "Recuperação de Senha";
      $from_name    = MAIL_NAME;
      $from         = MAIL_USER;
      //Send email
      if (Mailer::sendmail($to_name, $to, $subject, $message, $from_name, $from)){
        $report = "Enviamos um e-mail de recuperação de senha.<a href='javascript:history.back()'>Voltar</a>";
        $session->message($report);
        redirect_to('report.php');
      } else {
        $report = "Não consiguimos enviar o e-mail de recuperação.";
      }
    }
  } else {
    $report = "Não consiguimos encontrar a sua conta.";
  }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="">
  <link rel="stylesheet" href="static/stylesheets/index.css">
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/ajax/jquery.js"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.js" type="text/javascript"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.min.js" type="text/javascript"></script>
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
            <input type="text" name="username" placeholder="E-mail" class="username full-width" id="username" value="<?php isset($username)? print(htmlentities($username)) : NULL; //return values ?>"/>
          </div>
          <div class="form-row">
            <input type="password" name="password" placeholder="Senha" autocomplete="off" class="password full-width" value="<?php isset($password)? print(htmlentities($password)) : NULL; //return values ?>"/>
          </div>
          <div class="form-row">
            <input type="checkbox" name="keep-session" class="keep-login" value="TRUE"/><span class="keep-login">Manter sessão iniciada.</span>
          </div>
          <div class="form-row">
            <span class="credential-error report-span"><?php isset($report)? print(output_message($report)) : NULL; //print report message ?></span>
          </div>
          <div class="form-row">
            <input type="submit" name="submit" value="Iniciar sessão" class="submit full-width"/>
          </div>
          <div class="form-row"><a class="forgot-pass" href="#">Esqueceu senha ?</a></div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">

  <?php
  // POST login form data
  if(isset($get_log_info)){
    if($get_log_info === TRUE){
      echo
      "
      var deviceType   = $('.device-type').val(); // device-type input data
      var browserinfo  = $('.browserinfo').val(); // browserinfo input data
      var ipaddress    = $('.ipaddress').val(); // ipaddress input data
      var location     = $('.location').val(); // location input data

      $.post('index.php', {device-type: deviceType, browserinfo: browserinfo, ip: ipaddress, location: location}, function(data, status){
        //pass
      });
      ";
    }
  }
  ?>

  /**
  * Forgot password handlers
  **/
  $('.forgot-pass').click(function(){
    var username   = $('.username').val(); // Username input data
    if(username != null && username != ""){
      $.post('index.php', {ForgotPass: true, username: username}, function(data, status){
        $("body").html(data);
      });
    } else {
      $('span.report-span').html('Por favor, insira o seu email de acesso.')
    }
  });

  //POST logs data
  var device = navigator.platform // Device operating system
  var browserinfo = navigator.appVersion // Browser info
  // Append logs device data to hidden input
  $('.login-form').append("<input type = 'text' name = 'device-type' class='device-type' value='"+device+"'style='display:none;'>");
  $('.login-form').append("<input type = 'text' name = 'browserinfo' class='browserinfo' value='"+browserinfo+"'style='display:none;'>");
  // Requestclient  location and ip data
  $.get("http://ipinfo.io", function(response){
    var ip   = response.ip; // Client ip
    var location = response.city+', '+response.region; //Client location
    // Append logs location data to hidden input
    $('.login-form').append("<input type = 'text' name = 'ip' class='ipaddress' value='"+ip+"' style='display:none;'>");
    $('.login-form').append("<input type = 'text' name = 'location' class='location' value='"+location+"'style='display:none;'>");
  }, 'jsonP');

  </script>
</body>
</html>
