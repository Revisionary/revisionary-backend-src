<?php

$config = array();

$config['db'] = [
  'host' => 'localhost',
  'name' => 'revisionary_app',
  'user' => 'root',
  'pass' => 'root'
];

$config['default_language'] = 'en';

define('dir', realpath('.'));
define('controller', dir . '/app/controller');
define('view' , dir . '/app/view');
define('url', 'http://' . $_SERVER['SERVER_NAME']);
