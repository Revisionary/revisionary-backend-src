<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( post('processID') );
$page_ID = post('page_ID');
$queue_ID = post('queue_ID');


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

		// Update the queue status as an error
		$queue->update_status($queue_ID, "error", "Process is not working.", $process->getPid());

	}


}

//$process->stop();


// CREATE THE RESPONSE
$data = array(
	'' => '',

	// JUST TO SEE
	'page_ID' => $page_ID,
	'userID' => Page::ID($page_ID)->userId,
	'queue_ID' => $queue_ID,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => Page::ID($page_ID)->pageStatus['status'],
	'processDescription' => Page::ID($page_ID)->pageStatus['description'],

	'CSS Files' => Page::ID($page_ID)->getDownloadedQuantity('downloaded', 'css')."/".Page::ID($page_ID)->getDownloadedQuantity('total', 'css'),
	'Font Files' => Page::ID($page_ID)->getDownloadedQuantity('downloaded', 'font')."/".Page::ID($page_ID)->getDownloadedQuantity('total', 'font'),




	// REAL DATA
	'final' => [
		'status' => $status,
		'processStatus' => Page::ID($page_ID)->pageStatus['status'],
		'processDescription' => Page::ID($page_ID)->pageStatus['description'],
		'processID' => $process->getPid(),
		'queue_ID' => $queue_ID,
		'pageUrl' => Page::ID($page_ID)->cachedUrl,

		'totalCss' => Page::ID($page_ID)->getDownloadedQuantity('total', 'css'),
		'downloadedCss' => Page::ID($page_ID)->getDownloadedQuantity('downloaded', 'css'),

		'totalFont' => Page::ID($page_ID)->getDownloadedQuantity('total', 'font'),
		'downloadedFont' => Page::ID($page_ID)->getDownloadedQuantity('downloaded', 'font'),
	]
);

echo json_encode(array(
  'data' => $data
));
