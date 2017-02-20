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

//check login
if (!$session->is_logged_in()){redirect_to('index.php');}

/**
* Handles all sign-up process
**/
if (isset ($_POST['submit'])){
  $name            = $_POST['name'];
  $surname         = $_POST['surname'];
  $email           = $_POST['email'];
  $account_type    = $_POST['user_type'];
  //Validate first name
  if(!preg_match('/^[A-Za-z]/', $name) || empty($name) || strlen($name)>12 || strlen($name)<3 ){
    $name_report       = "nome";
  } else {
    $name_report       = "";
  }
  //Validate Last name
  if(!preg_match('/^[A-Za-z]/', $surname) || empty($surname) || strlen($surname)>12 || strlen($surname)<3 ){
    $surname_report       = "apelido";
  } else {
    $surname_report       = "";
  }
  //Verify if is somebody using this email
  $email_exist = Admin::find_by_email($email);
  //If is somebody using email
  if(!empty($email_exist)){
    $email_report = "Este email já tem uma conta associada.";
  } else {
    $email_report = "";
  }
  //Validate email
  if(!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email) || strlen($email)>50){
    $email_report = "O email deve ter o formato <i>nome@example.com</i> e conter no máximo 50 caracters.";
  }
  //validate account type
  if(empty($account_type)){
    $account_type_report = "Por favor, seleccione uma opção.";
  } else {
    $account_type_report = "";
  }
  //If empty error messages
  if(empty($name_report) & empty($surname_report) & empty($email_report) & empty($account_type_report)){
    //Generate a token
    $time_hash = strftime("%Y%m%d %H:%M:%S", time());
    $random    = mt_rand(10000, 99999);
    $token = md5(crypt("{$name}{$time_hash}{$surname}{$random}"));

    //Create Master Admin
    if(!empty(Admin::create($name, $surname, $email, $account_type, $token))){
      /*
      Set to event DB table details about creation
      */
      /** Send email confirmation */
      //Time
      $time      = time();
      //year
      $year      = strftime('%Y', time());
      //Assign constant to a variable
      $site_public = SITE_PUBLIC;
      // Confirmation message
      $message  .= "<p> Olá {$name},</p>";
      $message  .= "<p>Foste convidado para te tornares administrador de uma conta de gestão da <b>Igreja Ministerial das Nações Para Cristo</b><P>";
      $message  .= "<P>Para activar a conta e ter acesso ao uso da mesma, por favor clique em <a href='{$site_public}setup_account.php?username={$name}&key={$token}&verification={$time}'>configurar conta</a>.</p>";
      $message  .= "<p>Se recebeu este e-mail de surpresa, Por favor, elimine-o de imediato e não clique no link acima de forma que o seu email não seja adicionado.</p>";
      $message  .= "<p><strong class='attention'>Nota: </strong><span>O link acima é valido por apenas 24 horas</span>.<p>";
      $message  .= "<br><p>Atenciosamente,</p>";
      $message  .= "<h5 class='team'>Equipe da IMNC</h5>";
      $message  .= "<p style='font-size: 10px;'>Por favor não responda ao e-mail pois o mesmo não recebe respostas.</p>";
      $message  .= "<h5 style='font-size: 10px;'>&copy;{$year}-IMNC</h5>";
      //Other variables
      $to           = $email;
      $to_name      = "{$name} {$surname}";
      $subject      = "Convite - Igreja Ministerial das Nações Para Cristo";
      $from_name    = MAIL_NAME;
      $from_name    = MAIL_USER;
      //Send email
      if (Mailer::sendmail($to_name, $to, $subject, $message, $from_name, $from)){
        $report = "Enviamos um e-mail de activação de conta a {$name}.";
      } else {
        $report = "A conta foi criada mas não pudemos enviar o e-mail. Elimine-a e tente novamente.";
      }
      //return empty variables to avoid resubmission
      $name          = ""; $surname       = "";  $email         = "";  $account_type  = "";
    } else {
      $report = "Ups, Alguma coisa correu mal, não sabemos o quê!!";
    }
  }
}  else {
  //return empty variables
  $name          = "";
  $surname       = "";
  $email         = "";
  $account_type  = "";
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
  <link rel="stylesheet" href="static/stylesheets/new_account.css">
  <title>Iniciar Sessão</title>
</head>
<body class="sign-up-page">
  <header class="main-header">
    <div class="header-content"><span class="logo">IMNC</span></div>
  </header>
  <div class="main-sign-up-page-container">
    <div class="sign-up-div-form">
      <div class="sign-up-form-header">
        <h1>Criar conta</h1>
        <?php if (isset($report)): ?>
          <p><?php print($report); //print report ?></p>
        <?php else: ?>
          <p>Uma confirmação será enviada ao e-mail de modo que o assinante da conta configure as senhas.</p>
        <?php endif; ?>
      </div>
      <div class="sign-up-form-container">
        <form method="POST" action="new_account.php" class="sign-up-form">
          <div class="form-row">
            <input type="text" name="name" placeholder="Primeiro nome" class="name half-width" value="<?php isset($name)? print(htmlentities($name)) : NULL; //return values ?>"/>
            <input type="text" name="surname" placeholder="Último nome" class="surname half-width" value="<?php isset($surname)? print(htmlentities($surname)) : NULL; //return values ?>"/>
            <div class="report-row">
              <span class="credential-error">  <?php
              /** Dispaying error messages for name and surname */
              if(!(empty($name_report)) & !(empty($surname_report))  ){
                echo output_message("O {$name_report} e ou {$surname_report} que inseriu está incorrecto.");
              } elseif (!empty($name_report))     {
                echo output_message("O {$name_report} que inseriu está incorrecto.");
              }  elseif (!empty($surname_report)) {
                echo output_message("O {$surname_report} que inseriu está incorrecto.");
              }
              ?>
            </span>
          </div>
        </div>
        <div class="form-row">
          <input type="text" name="email" placeholder="E-mail" class="email full-width" value="<?php isset($email)? print(htmlentities($email)) : NULL; //return values ?>"/>
          <div class="report-row">
            <span class="credential-error"><?php isset($email_report)? print(output_message($email_report)) : NULL; //print report message ?></span>
          </div>
        </div>
        <div class="form-row">
          <select name="user_type" class="full-width">
            <option selected="selected"<?php if (isset($account_type) & !empty($account_type)){ echo "value='{$account_type}'"; } else { echo "value=''"; }?>>
              <?php if (isset($account_type) & !empty($account_type)){ echo $account_type; } else { echo "Seleccione o nivel de utilizador";} ?>
            </option>
            <option value="Primário" >Primário</option>
            <option value="Básico" >Secundário</option>
            <option value="simples" >Básico</option>
          </select>
          <div class="report-row">
            <span class="credential-error"><?php isset($account_type_report)? print(output_message($account_type_report)) : NULL; //print report message ?></span>
          </div>
        </div>
        <div class="form-row">
          <input type="submit" name="submit" value="Criar conta" class="submit full-width"/>
          <div class="row">
            <a href="javascript:history.back()">Retornar a página anterior</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
