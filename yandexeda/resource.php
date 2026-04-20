<?php
require __DIR__ . '/functions.php';
require __DIR__.'/server.php';

// Handle a request to a resource and authenticate the access token
/*
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
}
*/
$request = $_SERVER['REQUEST_METHOD'];
switch ($request) {
	case 'GET':
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$uri = explode( '/', $uri );
		if (isset($uri[1]) AND $uri[1] == 'nomenclature'){
			if (isset($uri[3]) AND $uri[3] == 'composition') request_list_products();
			elseif (isset($uri[3]) AND $uri[3] == 'availability') request_stocks_products();
		}
		break;
	case 'POST':
		break;
	case 'PUT':
		break;
	case 'DELETE':
		break;
	
}