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




// Initialize internalizator
$internalize = new Internalize_v2($page_ID);

$queue_ID = $job_ready = $browser_done = $files_detected = $html_downloaded = $html_filtred = $css_downloaded = $fonts_downloaded = false;






// JOBS:

// 1. Add the job to the queue
$queue_ID = $internalize->addToQueue();


// 2. Wait for the queue
if ($queue_ID) $job_ready = $internalize->waitForQueue();


// 3. If job is ready to get done, open the site with slimerJS
// 3.1. Print all the loaded resources
// 3.2. Take screenshots
// 3.3. Close the site
// 3.4. Parse the resources file
if ($job_ready) $browser_done = $internalize->browserWorks();


// 5. Detect files to download
if ($browser_done) $files_detected = $internalize->detectFilesToDownload();


// 6. Download HTML
if ($files_detected) $html_downloaded = $internalize->downloadHtml();

// 6.1. HTML absolute url filter
if ($html_downloaded) $html_filtred = $internalize->filterAndUpdateHTML();


// 7. Download CSS files
// 7.1. CSS absolute url filter
if ($html_filtred) $css_downloaded = $internalize->downloadCssFiles();


// 8. Download font files
if ($css_downloaded) $fonts_downloaded = $internalize->downloadFontFiles();


// 9. Update HTML with the downloaded CSS files, and font files?
if ($fonts_downloaded) $html_updated = $internalize->updateHtmlWithNewCss();


// 10. Update CSS with the downloaded fonts



// 11. Complete the job!


















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