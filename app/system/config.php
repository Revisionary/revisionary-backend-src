<?php

$config = array();



// Environmental settings
$config['env'] = [
	'name' 		=> 'local-dev',
	'subdomain' => 'dev'
];
/*
$config['env'] = [
	'name' 		=> 'remote-dev',
	'subdomain' => 'new'
];
*/
/*
$config['env'] = [
	'name' 		=> 'production',
	'subdomain' => 'www'
];
*/



// Database
$config['db'] = [
  'host' => '127.0.0.1',
  'name' => 'revisionary_app',
  'user' => 'root',
  'pass' => 'root'
];

if ($config['env']['name'] == "remote-dev") {
	$config['db']['user'] = "***";
	$config['db']['pass'] = "***";
}



// Default Language
$config['default_language'] = 'en';



// Definitions
define('domain' , 'revisionaryapp.com');
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', backdir."/logs");
define('bindir', backdir."/bin");
define('sessiondir', backdir."/sessions");
define('session_lifetime', 9999);
define('session_name', 'revisionary_session');
define('model' , dir . '/app/model');
define('view' , dir . '/app/view');
define('controller', dir . '/app/controller');
define('cache' , dir . '/assets/cache');
define('port' , (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : "") );
define('ssl' , (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? true : false);
define('secure_url', "https://".$config['env']['subdomain']."." . domain . (port != "" && port != "80" && port != "443" ? ":".port : "" ) );
define('insecure_url', "http://".$config['env']['subdomain']."." . domain . (port != "" && port != "80" && port != "443" ? ":".port : "" ) );
define('url', ssl ? secure_url : insecure_url);

// TEMP - Image Names !!!
define('project_image_name', 'project.jpg');
define('page_image_name', 'page.jpg');