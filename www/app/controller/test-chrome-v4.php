<?php
use Cocur\BackgroundProcess\BackgroundProcess;

header("content-type: image/png");
echo file_get_contents('http://chrome:3000/screenshot/http://tr.bilaltas.net/');