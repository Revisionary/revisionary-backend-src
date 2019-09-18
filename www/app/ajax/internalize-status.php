<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( request('processID') );
$phase_ID = intval(request('phase_ID'));
$queue_ID = intval(request('queue_ID'));


// Get the page data
$phaseData = Phase::ID($phase_ID);


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() )
	$status = 'running';

// If not running
elseif ( is_numeric($queue_ID) ) {


	// Logger
	$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $phaseData->logFileName,
	    'extension' => $phaseData->logFileExtension, // changes the log file extension
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
	'phase_ID' => $phase_ID,
	'queue_ID' => $queue_ID,
	'phaseUrl' => $phaseData->cachedUrl,
	'remoteUrl' => $phaseData->remoteUrl,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => $phaseData->phaseStatus['status'],
	'processDescription' => $phaseData->phaseStatus['description'],
	'processPercentage' => $phaseData->phaseStatus['percentage'],



	// REAL DATA
	'final' => [

		'status' => $status,
		'processID' => $process->getPid(),
		'processStatus' => $phaseData->phaseStatus['status'],
		'processDescription' => $phaseData->phaseStatus['description'],
		'processPercentage' => $phaseData->phaseStatus['percentage'],

		'queue_ID' => $queue_ID,
		'phaseUrl' => $phaseData->cachedUrl,
		'remoteUrl' => $phaseData->remoteUrl,
		'internalized' => $phaseData->internalizeCount,
	]
);

echo json_encode(array(
  'data' => $data
));
