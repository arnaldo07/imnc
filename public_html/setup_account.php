<?php
/**
* Handles creates new admin accounts
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

//If session is logged in
if ($session->is_logged_in()){
  //Return message
  $session->message("Você já tem uma conta com sessão iniciada.");
  //Redirect to home
  redirect_to("home.php");
}

/**
* Handles all password setup process
**/

if (isset($_GET['username']) and isset($_GET['key']) and $_GET['verification']){
  //Get them
  $username       = $_GET['username'];
  $token          = $_GET['key'];
  $verification   = $_GET['verification'];
  $verfication = time() - $verification;   //time() = $verification
  if($verfication <= 86400 &  $verfication > 0){ // 86400 = 24hours
    if($result = Admin::verify_username_token($username, $token)){
      if(!empty($result)){
        if (isset($_POST['submit'])){
          $password            = $_POST['password'];
          $pwd_confirm         = $_POST['pwd-confirm'];
          $account_id = $result->admin_id;
          $name = $result->admin_first_name;
          $surname = $result->admin_last_name;
          $email = $result->admin_email;
          //Validate password
          if(!preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("#[0-9]+#", $password) ||  empty($password) || strlen($password)>25 || strlen($password)<6){
            $password_report = "Senhas devem conter pelo menos uma letra maiúscula, minúscula e um número de 6 á 25 caracters.";
          } else {
            $password_report = "";
          }
          // Confirm password
          if($pwd_confirm === $password){
            $pwd_confirm_report  ="";
          } else {
            $pwd_confirm_report = "A confirmação de senha deve ser igual a senha.";
          }
          //If empty error messages
          if(empty($password_report) & empty($pwd_confirm_report)){
            //Encripty password
            $password = sha1(md5($password));
            //Create Master Admin
            if(!empty(Admin::setup_account($account_id, $password))){
              //Time
              $time      = time();
              //year
              $year      = strftime('%Y', time());
              //Assign constant to a variable
              $site_public = SITE_PUBLIC;
              // Confirmation message
              $message  .= "<p> Olá {$username},</p>";
              $message  .= "<p>A sua senha foi configurada com sucesso e a conta activada. Poderá acessar a mesma com e-mail e senha apartir de agora.<P>";
              $message  .= "<p>Se recebeu este e-mail de surpresa, Por favor, elimine-o de imediato e não use o seu conteúdo.</p>";
              $message  .= "<br><p>Atenciosamente,</p>";
              $message  .= "<h5 class='team'>Equipe da IMNC</h5>";
              $message  .= "<p style='font-size: 10px;'>Por favor não responda ao e-mail pois o mesmo não recebe respostas.</a>.</p>";
              $message  .= "<h5 style='font-size: 10px;'>&copy;{$year}-IMNC</h5>";
              //Other variables
              $to           = $email;
              $to_name      = "{$name} {$surname}";
              $subject      = "Configuração de senha";
              $from_name    = MAIL_NAME;
              $from    = MAIL_USER;
              //Send email
              Mailer::sendmail($to_name, $to, $subject, $message, $from_name, $from);
              $report = "A conta foi configurada com sucesso!!";
              //return empty variables to avoid resubmission
              $password          = ""; $pwd_confirm       = "";
            } else {
              $report = "Ups, Alguma coisa correu mal, não sabemos o quê!!";
            }
          }
        }  else {
          //return empty variables
          $password          = "";
          $pwd_confirm       = "";
        }
      } else {
        $report = "Nenhuma conta associada. Contacte ao administrador primário.";
      }
    }
  } else {
    $report = "O seu e-mail de activação expirou. Contacte ao administrador primário para reenviar-lo.";
  }
}  else {
  //return empty variables
  $password          = "";
  $pwd_confirm       = "";
  //redirect back
  //redirect_to('javascript:history.back()') ;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="">
  <script src=""></script>
  <link rel="stylesheet" href="static/stylesheets/setup_account.css">
  <title>Configurar Senhas</title>
</head>
<body class="setup-account-page">
  <header class="main-header">
    <div class="header-content"><span class="logo">IMNC</span></div>
  </header>
  <div class="main-setup-account-page-container">
    <div class="setup-account-div-form">
      <div class="setup-account-form-header">
        <h1>Configurar Senhas</h1>
        <?php if (isset($report)): ?>
          <p><?php print($report); //print report ?></p>
        <?php endif; ?>
      </div>
      <div class="setup-account-form-container">
        <form method="POST" action="<?php
        if(isset($username) & isset($token) & isset($verfication)){
          print("setup_account.php?username={$username}&key={$token}&verification={$verification}");
        } else {
          print("setup_account.php");
        }
        ?>" class="setup-account-form">
          <div class="form-row">
            <input type="password" name="password" placeholder="Nova Senha" class="password full-width" value="<?php isset($password)? print(htmlentities($password)) : NULL; //return values ?>"/>
            <div class="report-row">
              <span class="credential-error"><?php isset($password_report)? print(output_message($password_report)) : NULL; //print report message ?></span>
            </div>
        </div>
        <div class="form-row">
          <input type="password" name="pwd-confirm" placeholder="Confirmação de Senha" autocomplete="off" class="pwd-confirm full-width" value="<?php isset($pwd_confirm)? print(htmlentities($pwd_confirm)) : NULL; //return values ?>"/>
          <div class="report-row">
            <span class="credential-error"><?php isset($pwd_confirm_report)? print(output_message($pwd_confirm_report)) : NULL; //print report message ?></span>
          </div>
        </div>
        <div class="form-row">
          <input type="submit" name="submit" value="Criar conta" class="submit full-width"/>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
