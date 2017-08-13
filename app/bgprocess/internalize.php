<?php

// BG Process Settings
ignore_user_abort(true);
set_time_limit(0);


// Get the data
$page_ID = $argv[1];
$sessionID = $argv[2];
$project_ID = $argv[3];


// Correct the session ID
session_id($sessionID);


// Call the system
require realpath('.').'/app/init.php';


// Needs to be closed to allow working other PHP codes
session_write_close();





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
$need_to_wait = true;






// JOBS:


// 0. Initial checks of existing files

// If folder is already exist
if (
	file_exists(Page::ID($page_ID)->pageDir) // Folder is exist
) {

	$page_image = Page::ID($page_ID)->pageDeviceDir."/".Page::ID($page_ID)->getPageInfo('page_pic');
	$project_image = Page::ID($page_ID)->projectDir."/".Project::ID( $project_ID )->getProjectInfo('project_pic');


	// Check if the HTML file properly downloaded
	if (
		file_exists(Page::ID($page_ID)->pageFile) && // HTML is downloaded?
		file_exists(Page::ID($page_ID)->logDir."/resources.log") && // Resources ready?
		file_exists( $page_image ) && // Page image ready?
		file_exists( $project_image ) && // // Project image ready?
		!file_exists( Page::ID($page_ID)->logDir."/__html.log" ) && // No error on HTML download
		!file_exists( Page::ID($page_ID)->logDir."/__css.log" ) && // No error on CSS download
		!file_exists( Page::ID($page_ID)->logDir."/__filter.log" ) && // No error on filtering
		!file_exists( Page::ID($page_ID)->logDir."/__font.log" ) // No error on font download
	) {

		$need_to_wait = false;

	} else {


		// DELETE THE CACHE
		if ( file_exists(Page::ID($page_ID)->pageDir) )
			deleteDirectory(Page::ID($page_ID)->pageDir);


		// Create the log folder if not exists
		if ( !file_exists(Page::ID($page_ID)->logDir) )
			mkdir(Page::ID($page_ID)->logDir, 0755, true);
		@chmod(Page::ID($page_ID)->logDir, 0755);


	}




}





// 1. Add the job to the queue
if ($need_to_wait) $queue_ID = $internalize->addToQueue();


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