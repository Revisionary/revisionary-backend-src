<?php

$config = array();

$config['db'] = [
  'host' => '127.0.0.1',
  'name' => 'revisionary_app',
  'user' => 'root',
  'pass' => 'root'
];

$config['default_language'] = 'en';

define('domain' , 'revisionaryapp.com');
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', backdir."/logs");
define('bindir', backdir."/bin");
define('sessiondir', backdir."/sessions");
define('session_lifetime', 9999);
define('session_name', 'revisionary_session');
define('model' , dir . '/app/model');
define('view' , dir . '/app/view');
define('controller', dir . '/app/controller');
define('cache' , dir . '/assets/cache');
define('ssl' , (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? true : false);
define('secure_url', "https://secure." . domain);
define('unsecure_url', "http://new." . domain);
define('url', ssl ? secure_url : unsecure_url);

// TEMP - Image Names !!!
define('project_image_name', 'project.jpg');
define('page_image_name', 'page.jpg');