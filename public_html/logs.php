<?php
require_once("../includes/initialize.php");

/**
  * Add dates to calendar
**/
$cur_date = date("Ymd");
$update_date = date("Y-12-20");
if($cur_date >= $update_date){
  $database->add_to_calendar();
}

?>

<?php  include_once("templates/partials/header.php") //include header ?>
<link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/stylesheets/logs.css">
<div class="text">EM DESENVOLVIMENTO USE O MENU A ESQUERDA</div>
