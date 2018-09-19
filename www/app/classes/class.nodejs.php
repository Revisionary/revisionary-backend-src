<?php

$node_file = realpath('.')."/app/bgprocess/package-lock.json";


// If NPM isn't installed
if ( !file_exists($node_file) ) die('Please try again in a few minutes..');