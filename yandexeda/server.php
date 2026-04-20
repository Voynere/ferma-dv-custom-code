<?php
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');

require $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST;

require_once('OAuth2/Autoloader.php');
OAuth2\Autoloader::register();
$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASSWORD));

$server = new OAuth2\Server($storage);
$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
?>