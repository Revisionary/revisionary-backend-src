<?php

// BG Process Settings
ignore_user_abort(true);
set_time_limit(0);


// Get the data
$pageID = $argv[1];
$sessionID = $argv[2];


// Correct the session ID
session_id($sessionID);


// Call the system
require realpath('.').'/app/init.php';


//$_SESSION['process'] = "Internalize.php";


// Needs to be closed to allow working other PHP codes
session_write_close();


// Initiate the internalizator
$internalize = new Internalize( $pageID );




// JOBS:

// Optional - Delete the existing cache
$files_deleted = $internalize->deleteDirectory(dir."/assets/cache/sites");


// 1. Save the remote HTML
$savedHTML = $internalize->saveRemoteHTML();


// 2. Correct the urls and check the files that needs to be saved
$filtred = false;
if ($savedHTML) $filtred = $internalize->filterAndUpdateHTML($savedHTML);


// 3. Download the CSS files
$css_downloaded = false;
if ($filtred) $css_downloaded = $internalize->downloadCssFiles();


// Download the fonts
$font_downloaded = false;
if ($css_downloaded) $font_downloaded = $internalize->downloadFontFiles();


// Download the JS files ?

// Download the images ?
