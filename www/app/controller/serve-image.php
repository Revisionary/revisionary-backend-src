<?php

$device_ID = intval( get('device_ID') );


$deviceData = Device::ID($device_ID);
if (!$deviceData) return false;
$device = $deviceData->getInfo();
//die_to_print($device);




$image_path = "projects/project-".$device['project_ID']."/page-".$device['page_ID']."/phase-".$device['phase_ID']."/screenshots/device-".$device['device_ID'].".jpg";
$image_url = cache_url($image_path);
//die_to_print($image_url);



$page_title = $device['page_name'];
require view('serve-image');