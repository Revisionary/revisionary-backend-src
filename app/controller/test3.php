<?php
use Cocur\BackgroundProcess\BackgroundProcess;

$url = "https://soundcloud.com/";
$width = 1440;
$height = 900;
$output_image = dir."/screen.png";
$output_html = dir."/a.html";

$slimerjs = realpath('..')."/bin/slimerjs-0.10.3/slimerjs";
$capturejs = dir."/app/bgprocess/capture.js";

$process = "$slimerjs $capturejs $url $width $height $output_image $output_html";

echo $process;

$process = new BackgroundProcess($process);
$process->run();