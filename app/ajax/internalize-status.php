<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( post('processID') );


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() ) $status = 'running';

//$process->stop();


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

	'CSS Files' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'css')."/".Page::ID(post('pageID'))->getDownloadedQuantity('total', 'css'),
	'Font Files' => Page::ID(post('pageID'))->getDownloadedQuantity('downloaded', 'font')."/".Page::ID(post('pageID'))->getDownloadedQuantity('total', 'font'),




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
