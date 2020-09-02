<?php
/**
 * @file
 * A single location to store configuration. https://dev.twitter.com/apps
 */
define('CONSUMER_KEY', '5Y3hvwbWXk44Xg9uZN56LMX6T');//'qI7sAvgV3cXvTahNRS1Ilw');//'3wUxdzFwR8kWLyhV3AgrQ');
define('CONSUMER_SECRET', 'mTCi9re7HeB91U4a8H1k11XZxFMNmRVm3IvTEEvi5Ql7vtBBth');//'mW4am1G4jTXM1dAmWbAChYxzBSo8BuVpKq9TGYMvI');//'IDHcFLt0Ibtif0tiXzPyVsR4NOZ4zwBKK9ZAf6HE5zM');
define('OAUTH_CALLBACK', 'http://127.0.0.1/askboot/callback.php');
define('ACCESS_TOKEN',"2365778293-G1E6wNBLoyCRLPYbl3VeFZMAMctodjwc25KZafu");
define('TOKEN_SECRET',"y1GaW8CAFfPDOx1LdemA4my5fiyXzQx1YRy2PBvXNPGNT");

define('C_ADMIN','admin');
define('C_PASSWORD','123456');
// =====================================================================================//
/**
* Database settings
* 
**/
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'askmadina'); // مستخدم القاعدة
define('DB_PASSWORD', ']bK-9OmrUY8k'); // كلمة المرور
define('DB_DATABASE', 'askmadina'); // اسم القاعدة


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