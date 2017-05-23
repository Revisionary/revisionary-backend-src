<?php

require 'app/init.php';

$_url = get('url');
$_url = array_filter(explode('/', $_url));

if(!isset($_url[0])){
  $_url[0] = 'index';
}
if(!file_exists(controller($_url[0]))){
  $_url[0] = 'index';
}

session_start();
ob_start();
require controller($_url[0]);
ob_end_flush();