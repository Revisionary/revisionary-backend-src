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
$db = new MysqliDb ($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
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


// The Users global
$Users = array();

// Get the current user info
if ( userloggedIn() ) $Users[currentUserID()] = UserAccess::ID()->userData;