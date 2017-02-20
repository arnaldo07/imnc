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
defined('DB_SERVER') ? null : define("DB_SERVER",   "localhost");
defined('DB_USER')   ? null : define("DB_USER",     "root");
defined('DB_PASS')   ? null : define("DB_PASS",     "");
defined('DB_NAME')   ? null : define("DB_NAME",     "imnc");

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
defined('SITE_PUBLIC')? null : define ("SITE_PUBLIC", HTTP_PROTOCOL.$_SERVER['SERVER_NAME']."/imnc/public_html/");


?>
