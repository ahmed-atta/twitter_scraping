<?php
/**
 * @file
 * A single location to store configuration. https://dev.twitter.com/apps
 */
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('OAUTH_CALLBACK', 'http://127.0.0.1/askboot/callback.php');
define('ACCESS_TOKEN',"");
define('TOKEN_SECRET',"");

define('C_ADMIN','admin');
define('C_PASSWORD','123456');
// =====================================================================================//
/**
* Database settings
* 
**/
define('DB_SERVER', 'localhost');
define('DB_USERNAME', ''); // مستخدم القاعدة
define('DB_PASSWORD', ''); // كلمة المرور
define('DB_DATABASE', ''); // اسم القاعدة


//======================================================  v1.1  MySQLi  ENGINE
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD,DB_DATABASE);

/*
 * This is the "official" OO way to do it,
 * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
 *//* check connection */
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
} else {
  $mysqli->query("SET NAMES 'utf8mb4'");
}
//============================================

?>