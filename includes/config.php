<?php
/**
  * Configures basic constants and paths
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

//Define constants to connect to MySql database
defined('DB_SERVER') ? null : define("DB_SERVER",   "sql3.freemysqlhosting.net");
defined('DB_USER')   ? null : define("DB_USER",     "sql3161168");
defined('DB_PASS')   ? null : define("DB_PASS",     "NwZrAl5e9U");
defined('DB_NAME')   ? null : define("DB_NAME",     "sql3161168");

//Define Mail constats
defined('MAIL_HOST') ? null : define ("MAIL_HOST", "smtp.gmail.com");
defined('MAIL_PORT') ? null : define ("MAIL_PORT", "587");
defined('SMTP_AUTH') ? null : define ("SMTP_AUTH", "true");
defined('MAIL_USER') ? null : define ("MAIL_USER", "big.arnold07@gmail.com");
defined('MAIL_PASS') ? null : define ("MAIL_PASS", "Arnaldo??846861894");
defined('MAIL_NAME') ? null : define ("MAIL_NAME", "Geek Tutorials");


//Define paths
//Http protocol type
defined('HTTP_PROTOCOL') ? null : define('HTTP_PROTOCOL', strtolower(substr($_SERVER["SERVER_PROTOCOL"]
,0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://');
//Site public root for linkS
defined('SITE_PUBLIC')? null : define ("SITE_PUBLIC", 'https://'.$_SERVER['SERVER_NAME'].'/public_html/');


?>
