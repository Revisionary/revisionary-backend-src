<?php

// Composer Autoloader
$autoload_file = realpath('.')."/vendor/autoload.php";
if ( !file_exists($autoload_file) ) die('Please try again in a few minutes...');
require $autoload_file;


// Config file
require 'system/config.php';


// Language file
require 'language/' . $config['default_language'] . '/lang.php';


// Bring the classes
$classesDir = realpath('.') . '/app/classes';
if ($dh = opendir($classesDir)){
	while($file = readdir($dh)){
		if (is_file($classesDir . '/' . $file) && substr($file, -4) == ".php"){
			require $classesDir . '/' . $file;
		}
	}
}


// Bring the helpers functions
Helper::Load();


// Connect to DB
$db = new MysqliDb(array(
	'host' 		=> $config['db']['host'],
	'username' 	=> $config['db']['user'],
	'password' 	=> $config['db']['pass'],
	'db'		=> $config['db']['name'],
	'port'		=> 3306,
	'prefix' 	=> '',
	'charset' 	=> 'utf8mb4'
));


// MySQL timezone update
date_default_timezone_set(timezone);
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
$db->rawQuery("SET time_zone='$offset';");
unset($now, $mins, $sgn, $mins, $hrs, $offset);


// Start the session
load_session();


// Debug Mode
$debug_mode = $config['env']['debug'] == "TRUE";
if ($debug_mode) $db->setTrace(true);


// If logged in and user not found
$User = User::ID();
if ( userLoggedIn() && !$User ) {


	// Log out and go home
	if( session_destroy() ) {
		header('Location: '.site_url());
		die();
	}


}


// Get limitations
if ($User) {

	require model('limitations');

}