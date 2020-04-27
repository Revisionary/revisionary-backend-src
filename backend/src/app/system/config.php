<?php

$config = array('last_update' => '2020-04-27-04:00');



// Environmental settings
$config['env'] = [
	'name' 				 		   => $_ENV['ENV_NAME'] ?? "local-dev",
	'debug'  			 		   => $_ENV['DEBUG'] ?? false,

	'domain'			 		   => $_ENV['API_DOMAIN'] ?? "revisionaryapp.com",
	'subdomain' 		 		   => $_ENV['API_SUBDOMAIN'] ?? "dev",
	'insecure_subdomain' 		   => $_ENV['API_INSECURE_SUBDOMAIN'] ?? "dev",

	'dashboard_domain'			   => $_ENV['DASHBOARD_DOMAIN'] ?? "revisonary.co",
	'dashboard_subdomain' 		   => $_ENV['DASHBOARD_SUBDOMAIN'] ?? "app",
	'dashboard_insecure_subdomain' => $_ENV['DASHBOARD_INSECURE_SUBDOMAIN'] ?? "app",

	'landing_domain'			   => $_ENV['LANDING_DOMAIN'] ?? "revisonary.co",
	'landing_subdomain' 		   => $_ENV['LANDING_SUBDOMAIN'] ?? "app",
	'landing_insecure_subdomain'   => $_ENV['LANDING_INSECURE_SUBDOMAIN'] ?? "app",

	'db_host' 			 		   => $_ENV['DB_HOST'] ?? "revisionary_database",
	'db_port' 			 		   => $_ENV['DB_PORT'] ?? 3306,
	'db_name' 			 		   => $_ENV['DB_NAME'] ?? "revisionaryapp",
	'db_user' 			 		   => $_ENV['DB_USER'] ?? "user",
	'db_pass' 			 		   => $_ENV['DB_PASSWORD'] ?? "test",
	
	'db_choice' 			 	   => $_ENV['DB_CHOICE'] ?? "local",

	'smtp_user' 		 		   => $_ENV['SMTP_USER'],
	'smtp_pass' 		 		   => $_ENV['SMTP_PASS'],

	's3_key'					   => $_ENV['S3_KEY'],
	's3_secret' 		 		   => $_ENV['S3_SECRET'],
	's3_region' 		 		   => $_ENV['S3_REGION'],
	's3_bucket' 		 		   => $_ENV['S3_BUCKET'],
	
];



if ($config['env']['name'] == 'local-dev') { // For Local SSL Check

	// SSL Check
	$_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

} elseif ($config['env']['name'] == 'remote-dev') { // For Digital Ocean

	// SSL Check (because of CloudFlare)
	$_https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https";

	// Cloud DB connection
	$config['env']['db_choice'] = 'cloud';

	// Check the mounted Digital Ocean volume
	if ( !file_exists( realpath('.').'/cache/lost+found' ) ) die('V: Please try again in a few minutes...');

}


// Cloud DB Choice
if ($config['env']['db_choice'] == 'cloud') {

	$config['env']['db_host'] = $_ENV['DB_CLOUD_HOST'];
	$config['env']['db_port'] = $_ENV['DB_CLOUD_PORT'];
	$config['env']['db_name'] = $_ENV['DB_CLOUD_NAME'];
	$config['env']['db_user'] = $_ENV['DB_CLOUD_USER'];
	$config['env']['db_pass'] = $_ENV['DB_CLOUD_PASSWORD'];

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
define('timezone', "UTC");
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