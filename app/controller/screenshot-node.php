<?php
use Cocur\BackgroundProcess\BackgroundProcess;



$pageID = 27;
$projectID = Page::ID($pageID)->getPageInfo('project_ID');


// Screenshots
$page_image = Page::ID($pageID)->pageDeviceDir."/".Page::ID($pageID)->getPageInfo('page_pic');
$project_image = Page::ID($pageID)->projectDir."/".Project::ID( $projectID )->getProjectInfo('project_pic');
$htmlFile = Page::ID($pageID)->pageFile;

$page_captured = file_exists($page_image);
$project_captured = file_exists($project_image);
$html_captured = file_exists($htmlFile);

// If all already captured
if ($project_captured && $page_captured && $html_captured) return false;


// Get info
$url = Page::ID($pageID)->remoteUrl;
$deviceID = Page::ID($pageID)->getPageInfo('device_ID');
$width = Device::ID($deviceID)->getDeviceInfo('device_width');
$height = Device::ID($deviceID)->getDeviceInfo('device_height');
$page_image = $page_captured ? "done" : $page_image;
$project_image = $project_captured ? "done" : $project_image;
$htmlFile = $html_captured ? "done" : $htmlFile;



$nodejs = realpath('..')."/bin/nodejs-mac/bin/node"; // For Mac now
$scriptFile = dir."/app/bgprocess/screenshot.js"; // For Mac now
$command = "$nodejs $scriptFile --url=$url --viewportWidth=$width --viewportHeight=$height --pageScreenshot=$page_image --projectScreenshot=$project_image --htmlFile=$htmlFile";



// Initiate Internalizator
$process = new BackgroundProcess($command);
$process->run(Page::ID($page_ID)->logDir."/browser.log", true);


while ( $process->isRunning() ) {
	sleep(1);
}