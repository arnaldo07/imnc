<?php
/**
  * Basic global functions
  * @package IMNC Manager
  * @link https:// github.com/      The github project
  * @author Arnaldo Govene <arnaldo.govene@outlook.com>
  * @copyright 2016 Xindiri
  * @license
**/

/**
 * Redirects to @location
 * @param string  $location   The location to redirect to
 * **/
function redirect_to($location = NULL){
  if ($location !=NULL){
    header("location: {$location}");
    exit;
  }
}


/**
 * Output a message
 * @param  string    $message    The message to output
 * @return string
 * **/
function output_message($message=""){
  if (!empty($message)){
    return $message;
  } else {
    return $message = "";
  }
}

/**
 * Autolaad classes
 * @param string    $class_name   The class name to be autoloaded
 * **/
function __autoload($class_name){
  $class  = strtolower($class_name);
  $path   = "{$class_name}.php";
  if(file_exists($path)){
    require_once($path);
  } else {
    require_once("initialize.php");
  }
}

/**
* Gets current directory
* @return string
*/
function current_dir(){
  $url = $_SERVER['REQUEST_URI']; //returns the current URL
  $parts = explode('/',$url);
  $dir = $_SERVER['SERVER_NAME'];
  for ($i = 0; $i < count($parts) - 1; $i++) {
    $dir .= $parts[$i] . "/";
  }
  return $dir;
}

/**
* Gets client ip address
* @return client ip address
*/
function client_ip(){
  $ip_address = NULL;
  if(isset($_SERVER['HTTP_CLIENT_IP'])){
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
  } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } elseif(isset($_SERVER['HTTP_X_FORWARDED'])){
    $ip_address = $_SERVER['HTTP_X_FORWARDED'];
  } elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])){
    $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
  } elseif(isset($_SERVER['REMOTE_ADDR'])){
    $ip_address = $_SERVER['REMOTE_ADDR'];
  } else {
    $ip_address = "UNKNOWN";
  }

  return $ip_address;
}






?>
