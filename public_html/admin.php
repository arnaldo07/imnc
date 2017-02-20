<?php
/**
* Handles admins account managment
* Its only accessible for primary users
* @package IMNC Manager
* @link https:// github.com/      The github project
* @author Arnaldo Govene <arnaldo.govene@outlook.com>
* @copyright 2016 Xindiri
* @license
**/

// initialize
require_once("../includes/initialize.php");

// check session
if (!$session->is_logged_in()){redirect_to('index.php');}
$session_user_id = $_SESSION['user_id'];
/**
* Query admins in 10 limit set
**/
$admins = Admin::find_with_limit($limit=10);

/**
* Handle search
**/
if(isset($_POST['search'])){
  $search = $_POST['search'];
  $admins = Admin::search($search);
}

/**
* Sets admin to deleted mode
**/
if(isset($_POST['action'])){
  if($_POST['action'] == 'delete'){
    $admin_id = $_POST['adminId'];
    if(Admin::delete_by_id($admin_id)){
      $report = "administrador elimnado com sucesso";
      redirect_to($_SERVER['PHP_SELF']);
    } else {
      $report = "Ups, não foi possivel elimnar.";
    }
  }
}

/**
* Suspend admin
**/
if(isset($_POST['action'])){
  if($_POST['action'] == 'changeStatus'){
    $admin_id = $_POST['adminId'];
    $user = Admin::find_by_id($admin_id);
    $user_status = $user->admin_status;
    if($user_status == "Suspenso"){
      if(Admin::update_status_by_id($admin_id, 'Activo')){
        $report = "Administrador activo com sucesso.";
        redirect_to($_SERVER['PHP_SELF']);
      } else {
        $report = "Ups, não foi possivel activar.";
      }
    } elseif($user_status == 'Activo'){
      if(Admin::update_status_by_id($admin_id, 'Suspenso')){
        $report = "Administrador Suspenso com sucesso.";
        redirect_to($_SERVER['PHP_SELF']);
      } else {
        $report = "Ups, não foi possivel Suspender.";
      }
    }
  }
}

?>

<?php  include_once("templates/partials/header.php") //include header ?>
<link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/stylesheets/admin.css">

<body class="main-admin-page">
  <div class="main-page-container">
    <div class="main-page-head"></div>
  </div>
  <div class="main-admin-managment-container">
    <div class="search-form">
      <form method="POST" action="admin.php" class="search">
        <input type="text" name="search" class="search" placeholder="Procurar. ex: nome ou email" autocomplete="off"/>
        <a href="#" class="search-loop">
          <!-- Search icon SVG
          --------------------------------------->
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="header-search-icon" x="0px" y="0px" width="24px" height="24px"  viewBox="0 0 485.213 485.213" style="enable-background:new 0 0 485.213 485.213;" xml:space="preserve">
            <g>
              <g>
                <path d="M363.909,181.955C363.909,81.473,282.44,0,181.956,0C81.474,0,0.001,81.473,0.001,181.955s81.473,181.951,181.955,181.951    C282.44,363.906,363.909,282.437,363.909,181.955z M181.956,318.416c-75.252,0-136.465-61.208-136.465-136.46    c0-75.252,61.213-136.465,136.465-136.465c75.25,0,136.468,61.213,136.468,136.465    C318.424,257.208,257.206,318.416,181.956,318.416z" fill="#d9d9d9"/>
                <path d="M471.882,407.567L360.567,296.243c-16.586,25.795-38.536,47.734-64.331,64.321l111.324,111.324    c17.772,17.768,46.587,17.768,64.321,0C489.654,454.149,489.654,425.334,471.882,407.567z" fill="#d9d9d9"/>
              </g>
            </g>
          </svg>
        </a>
      </form>
    </div>
    <div class="main-admin-div">
      <?php if(!empty($admins)): ?>
        <?php foreach ($admins as $admin): ?>
          <div class="admin-row">
            <div class="admin-svg float-left">
              <!-- User SVG iCON
              ----------------------------------->
              <svg version="1.1" id="profile-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
              width="40px" heigh="60px" viewBox="0 0 53 53" style="enable-background:new 0 0 53 53;" xml:space="preserve">
              <path style="fill:#E7ECED;" d="M18.613,41.552l-7.907,4.313c-0.464,0.253-0.881,0.564-1.269,0.903C14.047,50.655,19.998,53,26.5,53
              c6.454,0,12.367-2.31,16.964-6.144c-0.424-0.358-0.884-0.68-1.394-0.934l-8.467-4.233c-1.094-0.547-1.785-1.665-1.785-2.888v-3.322
              c0.238-0.271,0.51-0.619,0.801-1.03c1.154-1.63,2.027-3.423,2.632-5.304c1.086-0.335,1.886-1.338,1.886-2.53v-3.546
              c0-0.78-0.347-1.477-0.886-1.965v-5.126c0,0,1.053-7.977-9.75-7.977s-9.75,7.977-9.75,7.977v5.126
              c-0.54,0.488-0.886,1.185-0.886,1.965v3.546c0,0.934,0.491,1.756,1.226,2.231c0.886,3.857,3.206,6.633,3.206,6.633v3.24
              C20.296,39.899,19.65,40.986,18.613,41.552z" />
              <g>
                <path d="M26.953,0.004C12.32-0.246,0.254,11.414,0.004,26.047C-0.138,34.344,3.56,41.801,9.448,46.76
                c0.385-0.336,0.798-0.644,1.257-0.894l7.907-4.313c1.037-0.566,1.683-1.653,1.683-2.835v-3.24c0,0-2.321-2.776-3.206-6.633
                c-0.734-0.475-1.226-1.296-1.226-2.231v-3.546c0-0.78,0.347-1.477,0.886-1.965v-5.126c0,0-1.053-7.977,9.75-7.977
                s9.75,7.977,9.75,7.977v5.126c0.54,0.488,0.886,1.185,0.886,1.965v3.546c0,1.192-0.8,2.195-1.886,2.53
                c-0.605,1.881-1.478,3.674-2.632,5.304c-0.291,0.411-0.563,0.759-0.801,1.03V38.8c0,1.223,0.691,2.342,1.785,2.888l8.467,4.233
                c0.508,0.254,0.967,0.575,1.39,0.932c5.71-4.762,9.399-11.882,9.536-19.9C53.246,12.32,41.587,0.254,26.953,0.004z" fill="#acafab"/>
              </g>
            </svg>
          </div>
          <div class="admin-data">
            <div class="admin-name">
              <p><?php echo $admin->admin_first_name; ?> <?php echo $admin->admin_last_name; ?></p>
            </div>
            <div class="admin-identification">
              <?php if($admin->admin_id == $session_user_id): ?>
                <span class="admin-type">Você | Administrador <?php echo $admin->admin_type; ?></span>
              <?php else: ?>
                <span class="admin-type">Administrador <?php echo $admin->admin_type; ?></span>
              <?php endif; ?>
              <span class="admin-email"><?php echo $admin->admin_email; ?></span></div>
              <div class="admin-status">
                <?php
                if($admin->admin_status == "Activo" ){
                  echo "Activo";
                } elseif ($admin->admin_status == "Suspenso"){
                  echo "Suspenso";
                } elseif ($admin->admin_status == "Bloqueado") {
                  echo "Bloqueado";
                } else {
                  echo "Sem estado";
                }
                ?>
              </div>
            </div>
            <div class="admin-account-actions float-right">
              <?php if($admin->admin_id != $session_user_id): ?>
                <button class="admin-delete" data-key="<?php echo $admin->admin_id; ?>">Eliminar</button>
                <?php if($admin->admin_status == 'Suspenso'): ?>
                  <button class="admin-activate" data-key="<?php echo $admin->admin_id; ?>">Activar</button>
                <?php elseif($admin->admin_status == 'Activo'): ?>
                  <button class="admin-activate" data-key="<?php echo $admin->admin_id; ?>">Suspender</button>
                <?php elseif($admin->admin_status == 'Bloqueado'): ?>
                  <button class="admin-activate" data-key="<?php echo $admin->admin_id; ?>">Activar</button>
                <?php endif; ?>
              <?php endif; ?>
            </div>
            <div class="clearfix"></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-row">
          Nenhum resultado
        </div>
      <?php endif;?>
    </div>
  </div>
  <script type="text/javascript">
  //search loop
  $('.search-loop').click(function(){
    $('form.search').submit();
  });
  //delete
  $('.admin-delete').click(function(){
    var id = $(this).attr('data-key');
    $.post("admin.php", {action:'delete', adminId: id},  function(data, status){
      //Load new data
      $('body').html(data);
    });
  });

  // Supspend or activate
  $('.admin-activate').click(function(){
    var id = $(this).attr('data-key');
    $.post("admin.php", {action:'changeStatus', adminId: id},  function(data, status){
      //Load new data
      $('body').html(data);
    });
  });
  </script>
</body>
</html>
