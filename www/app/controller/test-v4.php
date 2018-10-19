<?php
use Cocur\BackgroundProcess\BackgroundProcess;

//header("content-type: image/png");
//echo file_get_contents('http://chrome:3000/screenshot/http://tr.bilaltas.net/');

header("content-type: application/json");
echo file_get_contents('http://chrome:3000/internalize/https://www.twelve12.com/?fullPage=false&width=1440&height=900&sitedir=/var/www/html/assets/cache/');









/*
$page_ID = 199;
$project_ID = Page::ID($page_ID)->getPageInfo('project_ID');


$process_ID = get('pid');


if ( $process_ID != '' ) {


	// Initiate Internalizator
	if ($process_ID == 'yes') {


		// Remove the existing and wrong files
		if ( file_exists(Page::ID($page_ID)->pageDir) )
			deleteDirectory(Page::ID($page_ID)->pageDir);


		// Logger
		$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
			'filename' => Page::ID($page_ID)->logFileName,
		    'extension' => 'log', // changes the log file extension
		));


		// Add a new job to the queue
		$queue = new Queue();
		$queue_ID = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.");


		$process = new Cocur\BackgroundProcess\BackgroundProcess('php '.dir.'/app/bgprocess/internalize_v4.php '.$page_ID.' '.session_id().' '.$queue_ID);
		$process->run(Page::ID($page_ID)->logDir."/internalize-tasks-php.log", true);
		$process_ID = $process->getPid();

		header('Location: /test-v4?pid='.$process_ID);

	} else $process = BackgroundProcess::createFromPID( $process_ID );


	//while ( $process->isRunning() ) {
	//	sleep(1);
	//}

	if ( $process->isRunning() ) echo "<a href='/test-v4?pid=".$process_ID."'>$process_ID WORKING...</a>";
	else echo "DONE! <a href='/test-v4?pid=yes'>NEW?</a>";

} else {

	echo "READY!";

}
*/