<?php

$token = isset($_url[1]) ? $_url[1] : null;


if ($token != "xgbar3p6369gl43x")
	die('No access');



$pullLogFile = logdir."/pull.log";
file_put_contents($pullLogFile, print_r($_REQUEST, true));