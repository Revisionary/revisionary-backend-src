<pre>
<?php

$page_ID = 27;




// Logger
$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
	'filename' => Page::ID($page_ID)->logFileName,
    'extension' => 'log', // changes the log file extension
));

// Queue
$queue = new Queue();






// JOBS:

// 1. Add the job to the queue
$internalize = new Internalize_v2($page_ID);


// 2. Wait for the queue
$internalize->waitForQueue();


// 3. If job is ready to get done, open the site with slimerJS
// 3.1. Print all the loaded resources
// 3.2. Take screenshots
// 3.3. Close the site
// 3.4. Parse the resources file
$internalize->browserWorks();


// 5. Detect files to download
$internalize->detectFilesToDownload();


// 6. Download HTML
$internalize->downloadHtml();

// 6.1. HTML absolute url filter
$internalize->filterAndUpdateHTML();


// 7. Download CSS files
$internalize->downloadCssFiles();

// 7.1. CSS absolute url filter

// 7.2. Update HTML with the downloaded CSS files


// 8. Download font files

// 8.1. Update CSS with the downloaded fonts


// 9. Complete the job!


















/*
$queue = new Queue();


//$queue->new_job('internalize', 555, "TESTTTT");


if ($db->getLastErrno() === 0)
    echo 'Succesfull';
else
    echo 'Error: '. $db->getLastError();


//print_r( $queue->works() );
print_r( $queue->works() );
*/

?>
</pre>