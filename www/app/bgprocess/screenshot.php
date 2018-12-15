<?php

// BG Process Settings
ignore_user_abort(true);
set_time_limit(0);


// Get the data
$page_ID = $argv[1];
$device_ID = $argv[2];
$sessionID = $argv[3];
$queue_ID = isset($argv[4]) && is_numeric($argv[4]) ? $argv[4] : "";


// Correct the session ID
session_id($sessionID);


// Call the system
require realpath('.').'/app/init.php';


// Needs to be closed to allow working other PHP codes
session_write_close();


// Get the page data
$pageData = Page::ID($page_ID);


// Logger
$logger = new Katzgrau\KLogger\Logger(
	$pageData->logDir,
	Psr\Log\LogLevel::DEBUG,
	array(
		'filename' => 'screenshot',
	    'extension' => $pageData->logFileExtension, // changes the log file extension
	)
);


// Initiate Queue class
$queue = new Queue();


// Initialize internalizator
$screenshot = new Screenshot($page_ID, $device_ID, $queue_ID);


// Reset the variables
$job_ready = $browser_done = $files_detected = $html_filtred = $css_filtred = false;


echo "Queue ID: $queue_ID  \r\n";
echo "Page ID: $page_ID \r\n";
echo "SessionID: $sessionID \r\n";



// JOBS:

// 1. Wait for the queue
if ($queue_ID) $job_ready = $screenshot->waitForQueue();


// 2. 	If job is ready to get done, open the site with Chrome
// 2.1. Take a screenshot for the page, and project if not exist
if ($job_ready) $browser_done = $screenshot->browserWorks();


// 3. Complete the job!
if ($browser_done) $complete = $screenshot->completeTheJob();


if ($complete) echo "Screenshot taken \r\n";