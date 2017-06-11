<?php

require 'app/init.php';

$_url = get('url');
$_url = array_filter(explode('/', $_url));

if(!isset($_url[0])){
  $_url[0] = 'index';
}
if(!file_exists(controller($_url[0]))){
	http_response_code(404);

	// Show index
	$_url[0] = 'index';

	// If 404 page exists, better to show this one
	if(file_exists(controller('404')))
		$_url[0] = '404';
}

session_name("revisionary_session");
session_set_cookie_params(9999, '/', '.'.domain);
session_start();
ob_start();
require controller($_url[0]);
ob_end_flush();