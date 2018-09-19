<?php

$autoload_file = realpath('.')."/vendor/autoload.php";

// If Composer isn't installed
if ( !file_exists($autoload_file) ) die('Please try again in a few minutes...');

// Call the Composer
require $autoload_file;