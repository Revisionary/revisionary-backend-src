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


// Start the session
session_save_path(realpath('..')."/sessions");
session_name("revisionary_session");
session_set_cookie_params(9999, '/', '.'.domain);
session_start();