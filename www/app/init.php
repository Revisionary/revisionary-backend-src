<?php

// Bring the classes
$classesDir = realpath('.') . '/app/classes';
if ($dh = opendir($classesDir)){
	while($file = readdir($dh)){
		if (is_file($classesDir . '/' . $file) && substr($file, -4) == ".php"){
			require $classesDir . '/' . $file;
		}
	}
}


// Bring the helpers
Helper::Load();


// Config file
require 'system/config.php';


// Language file
require 'language/' . $config['default_language'] . '/lang.php';


// Update the timezone
date_default_timezone_set(timezone);


// MySQL timezone update
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);


// Connect to DB
$db = new MysqliDb (Array (
                'host' 		=> $config['db']['host'],
                'username' 	=> $config['db']['user'],
                'password' 	=> $config['db']['pass'],
                'db'		=> $config['db']['name'],
                'port' => 3306,
                'prefix' => '',
                'charset' => 'utf8mb4'));

$db->rawQuery("SET time_zone='$offset';");


// Unset the variables
unset($now, $mins, $sgn, $mins, $hrs, $offset);


// Initiate Logger
$log = new Katzgrau\KLogger\Logger(
	logdir,
	Psr\Log\LogLevel::DEBUG,
	array(
		'filename' => 'site',
	    'extension' => 'log'
	)
);


// Start the session
load_session();


$debug_mode = false;
if ($debug_mode) $db->setTrace(true);


// The Users global
$Users = array();

// Get the current user info
if ( userloggedIn() ) {

	$current_user_data = User::ID()->getData();
	if ($current_user_data) {

		$Users[currentUserID()] = $current_user_data;

	} else { // If user not found

		// Log out and go home
		if( session_destroy() ) {
			header('Location: '.site_url());
			die();
		}

	}
	unset($current_user_data);

}