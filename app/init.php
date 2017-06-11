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

//$db = new basicdb($config['db']['host'], $config['db']['name'], $config['db']['user'], $config['db']['pass']);
