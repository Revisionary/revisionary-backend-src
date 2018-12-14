<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( request('processID') );
$page_ID = request('page_ID');
$queue_ID = request('queue_ID');


// Get the page data
$pageData = Page::ID($page_ID);


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() )
	$status = 'running';

// If not running
elseif ( is_numeric($queue_ID) ) {


	// Logger
	$logger = new Katzgrau\KLogger\Logger($pageData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $pageData->logFileName,
	    'extension' => 'log', // changes the log file extension
	));



	$queue = new Queue();

	// If project is not complete when the process stopped
	if ( $queue->info($queue_ID)['queue_status'] != "done" ) {

		$last_message = $queue->info($queue_ID)['queue_message'];

		// Update the queue status as an error
		$queue->update_status($queue_ID, "error", "Last Message: ".$last_message, $process->getPid());

	}


}

//$process->stop();


// CREATE THE RESPONSE
$data = array(

	// JUST TO SEE
	'page_ID' => $page_ID,
	'userID' => $pageData->user_ID,
	'queue_ID' => $queue_ID,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => $pageData->pageStatus['status'],
	'processDescription' => $pageData->pageStatus['description'],
	'processPercentage' => $pageData->pageStatus['percentage'],



	// REAL DATA
	'final' => [
		'status' => $status,
		'processStatus' => $pageData->pageStatus['status'],
		'processDescription' => $pageData->pageStatus['description'],
		'processPercentage' => $pageData->pageStatus['percentage'],
		'processID' => $process->getPid(),
		'queue_ID' => $queue_ID,
		'pageUrl' => $pageData->cachedUrl,
		'internalized' => $pageData->internalizeCount,
	]
);

echo json_encode(array(
  'data' => $data
));
