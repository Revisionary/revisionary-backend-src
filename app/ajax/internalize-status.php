<?php


$ready = '';

// Initiate Internalizator
$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.post('pageID').' Internalization-Work');


// IS STARTED?
if( post('processID') != '' ) {

	// Set the process ID to check
	$process->setProcessId( post('processID') );

} else {

	// Run the process and send the process ID
	$process->start();

}


// STATUS CHECK
if ( $process->status() ) {

	//$process->stop();

	$status = 'running';


} else{

	$status = 'not-running';

}




// CREATE THE RESPONSE
$data = array(
	'' => '',



	// JUST TO SEE
	'pageID' => post('pageID'),

	'status' => $status,
	'processID' => $process->getProcessId(),
	'processStatus' => Page::ID(post('pageID'))->pageStatus['status'],
	'processDescription' => Page::ID(post('pageID'))->pageStatus['description'],

	'totalCss' => Page::ID(post('pageID'))->getDownloadedQuantity('total', 'css'),
	'downloadedCss' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'css'),

	'totalFont' => Page::ID(post('pageID'))->getDownloadedQuantity('total', 'font'),
	'downloadedFont' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'font'),





	// REAL DATA
	'final' => [
		'status' => $status,
		'processStatus' => Page::ID(post('pageID'))->pageStatus['status'],
		'processDescription' => Page::ID(post('pageID'))->pageStatus['description'],
		'processID' => $process->getProcessId(),
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
