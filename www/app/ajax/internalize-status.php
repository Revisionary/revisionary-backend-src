<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( request('processID') );
$page_ID = request('page_ID');
$queue_ID = request('queue_ID');


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() )
	$status = 'running';

// If not running
elseif ( is_numeric($queue_ID) ) {


	// Logger
	$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => Page::ID($page_ID)->logFileName,
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
	'userID' => Page::ID($page_ID)->user_ID,
	'queue_ID' => $queue_ID,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => Page::ID($page_ID)->pageStatus['status'],
	'processDescription' => Page::ID($page_ID)->pageStatus['description'],
	'processPercentage' => Page::ID($page_ID)->pageStatus['percentage'],

	'CSS Files' => Page::ID($page_ID)->getDownloadedQuantity('filtred', 'css-filter')."/".Page::ID($page_ID)->getDownloadedQuantity('total', 'css-filter'),



	// REAL DATA
	'final' => [
		'status' => $status,
		'processStatus' => Page::ID($page_ID)->pageStatus['status'],
		'processDescription' => Page::ID($page_ID)->pageStatus['description'],
		'processPercentage' => Page::ID($page_ID)->pageStatus['percentage'],
		'processID' => $process->getPid(),
		'queue_ID' => $queue_ID,
		'pageUrl' => Page::ID($page_ID)->cachedUrl,
		'internalized' => Page::ID($page_ID)->internalizeCount,

		'totalCss' => Page::ID($page_ID)->getDownloadedQuantity('total', 'css-filter'),
		'downloadedCss' => Page::ID($page_ID)->getDownloadedQuantity('filtred', 'css-filter'),
	]
);

echo json_encode(array(
  'data' => $data
));
