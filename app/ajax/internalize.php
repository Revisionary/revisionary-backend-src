<?php


sleep(11);

require realpath('.').'/app/init.php';

//$internalize = new Internalize( get('pageID') );
$internalize = new Internalize( 679812 )


	// JOBS:

	// Optional - Delete the existing cache
	//$internalize->deleteDirectory(dir."/assets/cache/sites");


	// 1. Save the remote HTML
	$savedHTML = $internalize->saveRemoteHTML();

	// 2. Correct the urls and check the files that needs to be saved
	$filtred = false;
	if ($savedHTML) $filtred = $internalize->filterAndUpdateHTML($savedHTML);

	// Download the CSS files
	$css_downloaded = false;
	if ($filtred) {

		foreach ($internalize->cssToDownload as $fileName => $url) {
			$css_downloaded = $internalize->download_remote_file($url, $fileName, "css");
		}

	}

	// Download the fonts
	$font_downloaded = false;
	if ($css_downloaded) {

		foreach ($internalize->fontsToDownload as $fileName => $url) {
			$font_downloaded = $internalize->download_remote_file($url, $fileName, "fonts");
		}

	}

	// Download the JS files ?

	// Download the images ?
