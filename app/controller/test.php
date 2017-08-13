<pre>
<?php

$page_ID = 27;


deleteDirectory(Page::ID($page_ID)->projectDir);


// Logger
$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
	'filename' => Page::ID($page_ID)->logFileName,
    'extension' => 'log', // changes the log file extension
));

// Queue
$queue = new Queue();




// Initialize internalizator
$internalize = new Internalize_v2($page_ID);

$queue_ID = $job_ready = $browser_done = $files_detected = $html_downloaded = $html_filtred = $css_downloaded = $fonts_downloaded = false;






// JOBS:

// 1. Add the job to the queue
$queue_ID = $internalize->addToQueue();


// 2. Wait for the queue
if ($queue_ID) $job_ready = $internalize->waitForQueue();


// 3. 	If job is ready to get done, open the site with Chrome
// 3.1. Print all the loaded resources
// 3.2. Take screenshots
// 3.3. Close the site
if ($job_ready) $browser_done = $internalize->browserWorks();


// 4. Parse and detect files to download
if ($browser_done) $files_detected = $internalize->detectFilesToDownload();


// 5. Download HTML
if ($files_detected) $html_downloaded = $internalize->downloadHtml();


// 6. HTML absolute URL filter to correct downloaded URLs
if ($html_downloaded) $html_filtred = $internalize->filterAndUpdateHTML();


// 7.   Download the CSS files
// 7.1. CSS absolute URL filter to correct downloaded URLs
// 7.1. Detect fonts and correct with downloaded ones
if ($html_filtred) $css_downloaded = $internalize->downloadCssFiles();


// 8. Download the font files
if ($css_downloaded) $fonts_downloaded = $internalize->downloadFontFiles();


// 9. Complete the job!
if ($fonts_downloaded) $iframeLink = $internalize->completeTheJob();






echo $iframeLink;















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