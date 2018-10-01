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


// Connect to DB
$db = new MysqliDb ($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);


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