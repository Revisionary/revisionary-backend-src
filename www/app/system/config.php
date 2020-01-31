<?php

$config = array('last_update' => '2020-01-31-06:30');



// Environmental settings
$config['env'] = [
	'name' 				 => $_ENV['ENV_NAME'],
	'domain'			 => $_ENV['DOMAIN'],
	'subdomain' 		 => $_ENV['SUBDOMAIN'],
	'insecure_subdomain' => $_ENV['INSECURE_SUBDOMAIN'],
	'db_host' 			 => $_ENV['DB_HOST'],
	'db_port' 			 => $_ENV['DB_PORT'],
	'db_name' 			 => $_ENV['DB_NAME'],
	'db_user' 			 => $_ENV['DB_USER'],
	'db_pass' 			 => $_ENV['DB_PASSWORD'],
	'timezone'  		 => $_ENV['DB_TIMEZONE'],
	'smtp_user' 		 => $_ENV['SMTP_USER'],
	'smtp_pass' 		 => $_ENV['SMTP_PASS'],
	'debug'  			 => $_ENV['DEBUG']
];



if ($config['env']['name'] == 'local-dev') {

	// SSL Check
	$_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

} elseif ($config['env']['name'] == 'remote-dev') {

	// SSL Check (because of CloudFlare)
	$_https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https";

	// Cloud DB connection
	$config['env']['db_host'] = $_ENV['DB_CLOUD_HOST'];
	$config['env']['db_port'] = $_ENV['DB_CLOUD_PORT'];
	$config['env']['db_name'] = $_ENV['DB_CLOUD_NAME'];
	$config['env']['db_user'] = $_ENV['DB_CLOUD_USER'];
	$config['env']['db_pass'] = $_ENV['DB_CLOUD_PASSWORD'];
	$config['env']['timezone'] = $_ENV['DB_CLOUD_TIMEZONE'];

	if ( !file_exists( realpath('.').'/cache/lost+found' ) ) die('V: Please try again in a few minutes...');

}



// Database Info
$config['db'] = [
  'host' => $config['env']['db_host'],
  'port' => $config['env']['db_port'],
  'name' => $config['env']['db_name'],
  'user' => $config['env']['db_user'],
  'pass' => $config['env']['db_pass']
];



// Default Language
$config['default_language'] = 'en';



// Definitions
define('timezone', $config['env']['timezone']);
define('domain', $config['env']['domain']);
define('subdomain', $config['env']['subdomain']);
define('insecure_subdomain', $config['env']['insecure_subdomain']);
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', dir . '/app/logs');
define('session_name', 'revisionary_session');
define('sessiondir', backdir."/sessions");
define('session_lifetime', 99999);
define('model', dir . '/app/model');
define('view', dir . '/app/view');
define('controller', dir . '/app/controller');
define('cache', dir . '/cache');
define('port', (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : "") );
define('ssl', $_https);
define('secure_url', "https://".subdomain."." . domain);
define('insecure_url', "http://".insecure_subdomain."." . domain);
define('url', ssl ? secure_url : insecure_url);



// Unset the variables
unset($_https);