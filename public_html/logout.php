<?php
/**
   * Logs user session out and deletes authentication cookie
   * @package IMNC Manager
   * @link https:// github.com/      The github project
   * @author Arnaldo Govene <arnaldo.govene@outlook.com>
   * @copyright 2016 Xindiri
   * @license
**/

//Initialize
require_once("../includes/initialize.php");

//session logout
$session->logout();
//cookie logout
Cookie::logout();
//redirect site public
redirect_to('index.php');

//END
?>
