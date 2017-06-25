<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// IS STARTED?
if( post('processID') != '' ) {

	// Set the process ID to check
	$process = BackgroundProcess::createFromPID( post('processID') );

} else {

	// Initiate Internalizator
	$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.post('pageID').' '.session_id());
	$process->run();

}


// STATUS CHECK
if ( $process->isRunning() ) {

	$status = 'running';

} else {

	$status = 'not-running';

}




// CREATE THE RESPONSE
$data = array(
	'' => '',



	// JUST TO SEE
	'pageID' => post('pageID'),
	'userID' => Page::ID(post('pageID'))->userId,

	'status' => $status,
	'processID' => $process->getPid(),
	'processStatus' => Page::ID(post('pageID'))->pageStatus['status'],
	'processDescription' => Page::ID(post('pageID'))->pageStatus['description'],

	'totalCss' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'css')."/".Page::ID(post('pageID'))->getDownloadedQuantity('total', 'css'),
	'totalFont' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'font')."/".Page::ID(post('pageID'))->getDownloadedQuantity('total', 'font'),




	// REAL DATA
	'final' => [
		'status' => $status,
		'processStatus' => Page::ID(post('pageID'))->pageStatus['status'],
		'processDescription' => Page::ID(post('pageID'))->pageStatus['description'],
		'processID' => $process->getPid(),
		'pageUrl' => Page::ID(post('pageID'))->cachedUrl,

		'totalCss' => Page::ID(post('pageID'))->getDownloadedQuantity('total', 'css'),
		'downloadedCss' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'css'),

		'totalFont' => Page::ID(post('pageID'))->getDownloadedQuantity('total', 'font'),
		'downloadedFont' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'font'),
	]
);

echo json_encode(array(
  'data' => $data
));
