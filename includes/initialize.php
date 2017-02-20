<?php
/**
  * Initializes all files and libraries
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

// Define them as absolute paths to make sure that require_once works as expected
// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
//Library path (includes folder )
defined('LIB_PATH') ? null :  define('LIB_PATH', dirname(__FILE__));

//load config file first
require_once(LIB_PATH.DS.'config.php');
// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');
//Libraries
require_once(LIB_PATH.DS.'PHPMailer'.DS.'PHPMailerAutoload.php');
require_once(LIB_PATH.DS.'fpdf'.DS.'fpdf.php');
//load core objects
require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'cookie.php');
//Load database-related classes
require_once(LIB_PATH.DS.'admin.php');
require_once(LIB_PATH.DS.'logs.php');
require_once(LIB_PATH.DS.'credit.php');
require_once(LIB_PATH.DS.'debit.php');
require_once(LIB_PATH.DS.'count.php');
require_once(LIB_PATH.DS.'mailer.php');
require_once(LIB_PATH.DS.'pdf.php');




?>
