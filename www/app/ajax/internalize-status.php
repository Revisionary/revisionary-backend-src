<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( request('processID') );
$version_ID = request('version_ID');
$queue_ID = request('queue_ID');


// Get the page data
$versionData = Version::ID($version_ID);


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() )
	$status = 'running';

// If not running
elseif ( is_numeric($queue_ID) ) {


	// Logger
	$logger = new Katzgrau\KLogger\Logger($versionData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $versionData->logFileName,
	    'extension' => $versionData->logFileExtension, // changes the log file extension
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
	'version_ID' => $version_ID,
	'queue_ID' => $queue_ID,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => $versionData->versionStatus['status'],
	'processDescription' => $versionData->versionStatus['description'],
	'processPercentage' => $versionData->versionStatus['percentage'],



	// REAL DATA
	'final' => [

		'status' => $status,
		'processID' => $process->getPid(),
		'processStatus' => $versionData->versionStatus['status'],
		'processDescription' => $versionData->versionStatus['description'],
		'processPercentage' => $versionData->versionStatus['percentage'],

		'queue_ID' => $queue_ID,
		'versionUrl' => $versionData->cachedUrl,
		'internalized' => $versionData->internalizeCount,
	]
);

echo json_encode(array(
  'data' => $data
));
