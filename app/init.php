<?php

// Bring the classes
function __autoload($className) {
	$classFile = __DIR__ . '/classes/class.' . strtolower($className) . '.php';
	if(file_exists($classFile)) {
		require $classFile;
	}
}

// Bring the helpers
Helper::Load();

// Config file
require 'system/config.php';

// Language file
require 'language/' . $config['default_language'] . '/lang.php';

$db = new basicdb($config['db']['host'], $config['db']['name'], $config['db']['user'], $config['db']['pass']);
