<?php
/**
* Primary header
* @package IMNC Manager
* @link https:// github.com/      The github project
* @author Arnaldo Govene <arnaldo.govene@outlook.com>
* @copyright 2016 Xindiri
* @license
**/

//Initialize
require_once("../includes/initialize.php");

if ($session->is_logged_in()){
  $admin = Admin::find_by_id($_SESSION['user_id']);
  if(!empty($admin)){
    $name = $admin->admin_first_name;
    $surname = $admin->admin_last_name;
  }
}


?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/stylesheets/header.css">
  <link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.css">
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/ajax/jquery.js"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.js" type="text/javascript"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.min.js" type="text/javascript"></script>
  <title>Default</title>
</head>
<body class="body">
  <header class="main-header">
    <div class="header-content">
      <div class="primary-menu-container">
        <div class="primary-menu-arrow float-left">
          <a id="menu-arrow" href="#">
            <!---Menu SVG Icon
            ---------------------------------->
            <svg version="1.1"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="28px" height="28px" viewBox="0 0 24.75 24.75" style="enable-background:new 0 0 24.75 24.75;" xml:space="preserve">
            <g>
              <path id="svg-icon-hover" d="M0,3.875c0-1.104,0.896-2,2-2h20.75c1.104,0,2,0.896,2,2s-0.896,2-2,2H2C0.896,5.875,0,4.979,0,3.875z M22.75,10.375H2
              c-1.104,0-2,0.896-2,2c0,1.104,0.896,2,2,2h20.75c1.104,0,2-0.896,2-2C24.75,11.271,23.855,10.375,22.75,10.375z M22.75,18.875H2
              c-1.104,0-2,0.896-2,2s0.896,2,2,2h20.75c1.104,0,2-0.896,2-2S23.855,18.875,22.75,18.875z" fill="#acafab"/>
            </g>
          </svg>
        </a>
        <div class="clearfix"></div>
      </div>
      <div class="primary-menu-content" id="the-primary-menu">
        <div class="close-container">
          <div class="menu-logo float-left"><a href="home.php">IMNC</a></div>
          <div class="menu-closer float-right"><a href="#" id="close-primary-menu">Fechar</a></div>
          <div class="clearfix"></div>
        </div>
        <ul class="primary-menu-ul">
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>home.php" class="menu-anchor">Painel de Tarefas</a></li>
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>moviments.php" class="menu-anchor">Novos movimentos</a></li>
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>statement.php" class="menu-anchor">Balancetes</a></li>
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>cashFlow.php" class="menu-anchor">Fluxos de Caixa</a></li>
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>new_account.php" class="menu-anchor">Novos Administradores</a></li>
          <li class="menu-li"><a href="<?php echo SITE_PUBLIC ?>admin.php" class="menu-anchor">Gerir Administradores</a></li>
        </ul>
      </div>
      <div class="primary-logo float-left"><span class="logo"><a href="<?php echo SITE_PUBLIC ?>">IMNC</a></span></div>
      <div class="logout float-right">
        <div class="logout-content float-right">
          <span><a href="<?php echo SITE_PUBLIC ?>logout.php" class="logout float-right">Terminar Sessão</a></span>
          <span class="greetings float-right">Olá, <?php if(isset($name) & isset($surname)){ print("{$name} {$surname}"); }?></span>
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</header>
<body>
  <script>
  // Menu hide and show
  $('#menu-arrow').click(function(){
    $('#the-primary-menu').show("slide", {direction: "left"}, 500);
    $('#menu-arrow').hide(200);
  });
  $('#close-primary-menu').click(function(){
    $('#the-primary-menu').hide("slide", {direction: "left"}, 500);
    $('#menu-arrow').show();
  });
  </script>
