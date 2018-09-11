<?php
use Cocur\BackgroundProcess\BackgroundProcess;


class Internalize_v3 {


	// The Page ID
	public $page_ID;

	// The Queue ID
	public $queue_ID;



	// HTML file to download
	public $downloadedHTML = array();

	// CSS files to download
	public $downloadedCSS = array();

	// Fonts to download
	public $downloadedFonts = array();

	// JS files to download !!! Not yet
	public $downloadedJS = array();

	// Images to download !!! Not yet
	public $downloadedImages = array();

	public $easy_html_elements = array(
	    "A",
		"B",
		"I",
		"U",
		"EM",
		"STRONG",
		"SMALL",
		"TEXTAREA",
		"LABEL",
		"BUTTON",
		"TIME",
		"DATE",
		"ADDRESS",
		"P",
		"DIV",
		"SPAN",
		"LI",
		"H1",
		"H2",
		"H3",
		"H4",
		"H5",
		"H6",
		"IMG"
	);



	// When initialized
	public function __construct($page_ID, $queue_ID) {

		// Set the page ID
		$this->page_ID = $page_ID;
		$this->queue_ID = $queue_ID;

	}




	// JOBS:


	// 1. Wait for the queue
	public function waitForQueue() {
		global $db, $queue, $logger;


		// 1.1. Check if current job is ready to be done
		$job_ready = $queue->isReady($this->queue_ID);
		$job_status = $queue->info($this->queue_ID)['queue_status'];


		// 1.2. Wait for the job availability in queue
		$interval = 2;
		while (!$job_ready && $job_status == "waiting") {

			$logger->info("Waiting $interval second(s) for the queue.");
			sleep($interval);
			$job_ready = $queue->isReady($this->queue_ID);
			$job_status = $queue->info($this->queue_ID)['queue_status'];

		}

		return true;

	}



	// 2. 	If job is ready to get done, open the site with Chrome
	// 2.1. Download the HTML file
	// 2.2. Download the CSS files
	// 2.3. Download the fonts
	// 2.4. Print all the downloaded resources
	// 2.5. Take screenshots
	// 2.6. Close the site
	public function browserWorks() {
		global $db, $queue, $logger, $config;


		// INITIAL LOGS

		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job is starting.");

		// Log
		$logger->info("Browser job is starting.");



		// VARIABLES

		// Get page and project IDs
		$page_ID = $this->page_ID;
		$parent_page_ID = Page::ID($page_ID)->getPageInfo('parent_page_ID');
		$project_ID = Page::ID($page_ID)->getPageInfo('project_ID');


		// Screenshots and download lists
		$siteDir = Page::ID($page_ID)->pageDir;

		$htmlFile = Page::ID($page_ID)->pageFile;
		$CSSFilesList = $siteDir."/logs/css.log";
		$fontFilesList = $siteDir."/logs/font.log";

		$page_image = Page::ID($page_ID)->pageDeviceDir."/".page_image_name;
		$project_image = Page::ID($page_ID)->projectDir."/".project_image_name;



		// Are they already exist?
		$html_captured = file_exists($htmlFile);
		$CSSFiles_captured = file_exists($CSSFilesList);
		$fontFiles_captured = file_exists($fontFilesList);

		$page_captured = file_exists($page_image);
		$project_captured = file_exists($project_image);


		// If both already captured and files are already downloaded, skip this task.
		if ( $html_captured && $CSSFiles_captured && $fontFiles_captured && $page_captured && $project_captured ) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "working", "Browser job is skipped.");


			// Log
			$logger->info('HTML, downloaded CSS and fonts lists are already exist. Browser job is skipped.');


			return true;
		}


		// Get info
		$url = Page::ID($page_ID)->remoteUrl;
		$logDir = Page::ID($page_ID)->logDir;

		$deviceID = Page::ID($page_ID)->getPageInfo('device_ID');
		$width = Device::ID($deviceID)->getDeviceInfo('device_width');
		$height = Device::ID($deviceID)->getDeviceInfo('device_height');

		$htmlFile = $html_captured ? "done" : $htmlFile;
		$CSSFilesList = $CSSFiles_captured ? "done" : $CSSFilesList;
		$fontFilesList = $fontFiles_captured ? "done" : $fontFilesList;

		$page_image = $page_captured ? "done" : $page_image;
		$project_image = $project_captured ? "done" : $project_image;


/*
		// Process directories - SlimerJS - Firefox !!!
		$slimerjs = bindir."/slimerjs-0.10.3/slimerjs";
		$capturejs = dir."/app/bgprocess/firefox.js";

		$process_string = "$slimerjs $capturejs $url $width $height $page_image $project_image $logDir";
*/


		// Process directories - NodeJS - Chrome
		$nodejs = dir."/vendor/bin/node";
		$scriptFile = dir."/app/bgprocess/chrome_v3.js";

		$process_string = "$nodejs $scriptFile ";
		$process_string .= "--url=$url ";
		$process_string .= "--viewportWidth=$width ";
		$process_string .= "--viewportHeight=$height ";
		$process_string .= "--htmlFile=$htmlFile ";
		$process_string .= "--CSSFilesList=$CSSFilesList ";
		$process_string .= "--fontFilesList=$fontFilesList ";
		$process_string .= "--pageScreenshot=$page_image ";
		$process_string .= "--projectScreenshot=$project_image ";
		$process_string .= "--siteDir=$siteDir ";
		$process_string .= "--logDir=$logDir ";
		$process_string .= "--delay=1000"; // Screenshot Delay


		// Do the process
		$process = new BackgroundProcess($process_string);
		$process->run($logDir."/browser-process-js.log", true);


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job has started.");


		// LOGS
		$logger->info("Browser jobs process string: ". $process_string);
		$logger->info("Browser jobs process ID: ". $process->getPid());



		// Wait for the downloaded files lists
		$timeout = 50; // seconds - Temporarily 50 !!!
		$eta = 0;
		$out_of_time = false;
		while (
			//$process->isRunning() && // Even though process looks done, wait for the important files !!!
			$queue->info($this->queue_ID)['queue_status'] == "working" &&
			(
				!file_exists($htmlFile) ||
				!file_exists($siteDir."/logs/css.log") ||
				!file_exists($siteDir."/logs/font.log")
			)
		) {

			$waitfor = 2; // seconds

			$logger->info("Waiting $waitfor seconds for the process to be complete");
			sleep($waitfor);

			$eta = $eta + $waitfor;

			if ($eta >= $timeout) {

				$out_of_time = true;
				break;

			}
		}


		// Timeout error
		if ($out_of_time) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "Downloaded files lists timeout.");


			// Log
			$logger->error("Downloaded files lists timeout.");


			// Stop the process
			if ( $process->isRunning() ) $process->stop();

			return false;

		}



		// Process Check
		if ( !$process->isRunning() ) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "working", "Browser job is done.");


			// Log
			$logger->info("Browser job is done.");

		}


		// RE-CHECK FILES

		// HTML file check
		if (!file_exists($htmlFile)) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "HTML file is not exist.");


			// Log
			$logger->error("HTML file is not exist.");


			return false;
		}


		// CSS list file check
		if (!file_exists($CSSFilesList)) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "Downloaded CSS file list is not exist.");


			// Log
			$logger->error("Downloaded CSS file list is not exist.");


			return false;
		}


		// Fonts list file check
		if (!file_exists($fontFilesList)) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "Downloaded font file list is not exist.");


			// Log
			$logger->error("Downloaded font file list is not exist.");


			return false;
		}



		// Parse the downloaded CSS list
		$downloaded_css = preg_split('/\r\n|[\r\n]/', trim(file_get_contents($CSSFilesList)));
		$downloaded_css = array_filter(array_unique($downloaded_css)); // Clean
		$this->downloadedCSS = $downloaded_css;


		// Parse the downloaded fonts list
		$downloaded_font = preg_split('/\r\n|[\r\n]/', trim(file_get_contents($fontFilesList)));
		$downloaded_font = array_filter(array_unique($downloaded_font)); // Clean
		$this->downloadedFonts = $downloaded_font;



		// Log
		$logger->debug("Downloaded CSS files:", $this->downloadedCSS);
		$logger->debug("Downloaded Font files:", $this->downloadedFonts);



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Downloaded files lists are ready.");

		// Log
		$logger->info("Downloaded files lists are ready.");


		return true;

	}


	// 3. Parse and detect downloaded files !!!
	public function detectDownloadedFiles() {
		global $db, $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Started parsing the downloaded files list.");

		// Log
		$logger->info("Started parsing the downloaded files lists.");



		// PARSE THE DOWNLOADED FILES

		// Update the real list
		$this->downloadedCSS = $this->parseResources($this->downloadedCSS);
		$this->downloadedFonts = $this->parseResources($this->downloadedFonts);



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Parsing resources finished.");

		// Log
		$logger->info("Parsing resources finished.");


		return true;
	}


	// 4. HTML absolute URL filter to correct downloaded URLs
	public function filterAndUpdateHTML() {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "HTML Filtering started.");


		// Log
		$logger->info("HTML Filter started.");


		// Do nothing if there is no HTML file
		if ( !file_exists( Page::ID($this->page_ID)->pageFile ) ) {

			// Log
			$logger->error("HTML file is not exist.");

			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "HTML couldn't be filtered. (No file)");

			return false;

		}


		// GET HTML CONTENT
		// Get the HTML from the downloaded file
		$html = file_get_contents(Page::ID($this->page_ID)->pageFile);



		// Specific Log
		file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Started \r\n", FILE_APPEND);



		// Add Necessary Spaces - done for a bug
		function placeNeccessarySpaces($contents) {
			$quotes = 0; $flag = false;
			$newContents = '';
			for($i = 0; $i < strlen($contents); $i++){
			    $newContents .= $contents[$i];
			    if(is_array($contents) && $contents[$i] == '"') $quotes++;
			    if($quotes%2 == 0){
			        if(is_array($contents) && $contents[$i+1] !== ' ' && $flag == true) {
			            $newContents .= ' ';
			            $flag = false;
			        }
			    }
			    else $flag = true;
			}
			return $newContents;
		}
		$html = placeNeccessarySpaces($html);



		// INCLUDE THE BASE
		$countHead = 0;
		$html = preg_replace_callback(
	        '/<head([\>]|[\s][^<]*?\>)/i',
	        function ($urls) {
		        global $countHead;
		        $countHead++;

		        $head_tag = $urls[0];

		        if ( $countHead == 1 ) {

			        // Specific Log
					file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Base Added: '".Page::ID($this->page_ID)->remoteUrl."' \r\n", FILE_APPEND);

					$new_base = "<base href='".Page::ID($this->page_ID)->remoteUrl."'>";

			        return $head_tag.$new_base;

		        }

		        return $head_tag;

	        },
	        $html
	    );

	    // If no <head> tag, add it after the <html> tag
	    if ($countHead == 0) {

			$countHtml = 0;
			$html = preg_replace_callback(
		        '/<html([\>]|[\s][^<]*?\>)/i',
		        function ($urls) {
			        global $countHtml;
			        $countHtml++;

			        $html_tag = $urls[0];

			        if ( $countHtml == 1 ) {

				        // Specific Log
						file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Base Added: '".Page::ID($this->page_ID)->remoteUrl."' \r\n", FILE_APPEND);

						$new_base = "<base href='".Page::ID($this->page_ID)->remoteUrl."'>";

						return $html_tag.$new_base;

			        }

			        return $html_tag;

		        },
		        $html
		    );

	    }


		// INTERNALIZE CSS FILES
		$html = preg_replace_callback(
	        '/<(?<tagname>link)\s+[^<]*?(?<attr>href)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/is',
	        function ($urls) {


		        // Found parts
		        $full_tag = $urls[0];
		        $attribute = $urls['attr'];
		        $the_url = isset($urls['value2']) ? $urls['value2'] : $urls['value'];


		        // Absoluted URL
		        $new_url = url_to_absolute(Page::ID($this->page_ID)->remoteUrl, $the_url);


				// If it has host, but no protocol (without http or https)
		        if (parseUrl($the_url)['host'] != "" )
		        	$new_url = url_to_absolute(parseUrl($the_url)['full_host'], $the_url);


		        // If not on our server, don't touch it
		        if (parseUrl($the_url)['domain'] != "" && parseUrl($the_url)['domain'] != parseUrl(Page::ID($this->page_ID)->remoteUrl)['domain'] )
		        	$new_url = $the_url;



				// Find in downloads
		        $css_resource_key = array_search($new_url, array_column($this->downloadedCSS, 'url'));


		        // If file is from the remote url, and already downloaded
		        if (
		        	parseUrl($new_url)['domain'] == parseUrl(Page::ID($this->page_ID)->remoteUrl)['domain'] &&
		        	$css_resource_key !== false &&
		        	(	// Check if this is really stylesheet calling tag
		        		strpos($full_tag, 'rel="stylesheet"') !== false ||
		        		strpos($full_tag, "rel='stylesheet'") !== false ||
		        		strpos($full_tag, "rel=stylesheet") !== false
		        	)

		        ) {

			        // Downloaded file name
					$css_file_name = $this->downloadedCSS[$css_resource_key]['new_file_name'];


			        // Change the URL again with the downloaded file
			        $new_url = Page::ID($this->page_ID)->pageUri."css/".$css_file_name;



			        // Specific Log
					file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - CSS Internalized: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);

				}



		        // Update the HTML element
	            $new_full_tag = str_replace(
	            	"$attribute='$the_url", // with single quote
	            	"$attribute='$new_url",
	            	$full_tag
	            );

	            $new_full_tag = str_replace(
	            	"$attribute=\"$the_url", // with double quotes
	            	"$attribute=\"$new_url",
	            	$new_full_tag
	            );



		        // Found URL Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Found URL: '".print_r( $urls, true)."' \r\n", FILE_APPEND);


		        // Specific Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Absoluted: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Absoluted HTML: '".$full_tag."' -> '".$new_full_tag."' \r\n", FILE_APPEND);


	            return $new_full_tag;
	        },
	        $html
	    );



	    // FILTER IN PAGE STYLES
		$html = preg_replace_callback(
	        '/(?<tag><style+[^<]*?>)(?<content>[^<>]++)<\/style>/i',
	        function ($style) {

		        // Specific Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Inpage Style Filtred \r\n", FILE_APPEND);

		        return $style['tag'].$this->filter_css($style['content'])."</style>";

	        },
	        $html
	    );


	    // FILTER INLINE STYLES
		$html = preg_replace_callback(
	        '/<(?:[a-z0-9]*)\s+[^<]*?(?:style)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) {

		        $the_css = isset($urls['value2']) ? $urls['value2'] : $urls['value'];
		        $filtred_css = $this->filter_css($the_css);

		        // Specific Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Inline Style Filtred: '".$the_css."' -> '".$filtred_css."' \r\n", FILE_APPEND);


	            return str_replace(
	            	$the_css,
	            	$filtred_css,
	            	$urls[0]
	            );
	        },
	        $html
	    );


	    // PUT THE ELEMENT INDEXES
	    $countElement = 0;
		$html = preg_replace_callback(
	        '/<body[^<]*?>|(?!^)\G(.*?)(?<tag><(?<tagname>[a-z1-9]+[1-9]?)\s?[^<]*?>)(?=.*<\/body>)/si',
	        function ($matches) {
		        global $countElement;
		        $html_element = $matches[0];


				// If it couldn't be found correctly
		        if (
		        	!isset($matches['tag']) ||
		        	!isset($matches['tagname'])
		        ) return $html_element;


		        $tag 	  = $matches['tag'];
		        $tag_name = $matches['tagname'];


		        // If it isn't easily editable
		        if ( !in_array(strtoupper($tag_name), $this->easy_html_elements) ) {

					// Specific Log
					file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - SKIPPED TAGNAME TO INDEX: \r\n
					".$tag_name." \r\n
					".$tag."' \r\n \r\n", FILE_APPEND);


			        return $html_element;
			    }


		        $countElement++;


				// If ends with '/>'...
				if ( substr($tag, -2 ) == '/>' ) $new_tag = str_replace('/>', ' data-revisionary-index="'.$countElement.'" />', $tag);

				// If ends with only '>'...
				elseif ( substr($tag, -1 ) == '>' ) $new_tag = str_replace('>', ' data-revisionary-index="'.$countElement.'">', $tag);


				// Specific Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - ELEMENT TO INDEX: \r\n
				".$tag." \r\n
				".$new_tag."' \r\n \r\n", FILE_APPEND);


		        $new_html_element = str_replace($tag, $new_tag, $html_element);


		        return $new_html_element;
	        },
	        $html
		);



		// SAVING:

		// Save the file if not exists
		if ( file_exists( Page::ID($this->page_ID)->pageFile ) )
			$updated = file_put_contents( Page::ID($this->page_ID)->pageFile, $html, FILE_TEXT);


		// Specific Log
		file_put_contents( Page::ID($this->page_ID)->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Finished".(!$updated ? " <b>WITH ERRORS</b>":'')." \r\n", FILE_APPEND);
		rename(Page::ID($this->page_ID)->logDir."/_html-filter.log", Page::ID($this->page_ID)->logDir.(!$updated ? '/__' : '/')."html-filter.log");


		if ($updated) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "working", "HTML Filtred.");


			$logger->info("HTML Filtred.");
			return true;

		}

		// Update the queue status
		$queue->update_status($this->queue_ID, "error", "HTML couldn't be filtred.");
		$logger->error("HTML couldn't be filtred.");
		return false;

	}


	// 5. Filter CSS files
	// 5.1. Absolute URL filter to correct downloaded URLs
	// 5.2. Detect fonts and correct with downloaded ones
	public function filterAndUpdateCSSfiles() {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "CSS filtering started.");

		// Init Log
		$logger->info("CSS filtering started.");

		// Init Log
		$logger->debug("Total CSS: ". count($this->downloadedCSS), $this->downloadedCSS);



		// Specific Log
		file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Started {TOTAL:".count($this->downloadedCSS)."} \r\n", FILE_APPEND);




		$count = 0;
		$css_filtered_has_error = false;


		// Do nothing if there is no CSS file
		if ( count($this->downloadedCSS) > 0 ) {


			// Filter them
			foreach ($this->downloadedCSS as $info) {

				$fileName = $info['new_file_name'];
				$css_url = $info['url'];
				$fileUri = Page::ID($this->page_ID)->pageDir."/css/".$fileName;


				// Get the old CSS
				$old_css = file_get_contents($fileUri);


				// Filter
				$filteredCSS = $this->filter_css($old_css, $css_url);


				// Save the new CSS
				$css_filtered = file_put_contents($fileUri, $filteredCSS);


				// Specific Log
				file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] -".(!$css_filtered ? " <b>NOT</b>":'')." Filtered: '".$css_url."' -> '".$fileName."' \r\n", FILE_APPEND);

				if (!$css_filtered) $css_filtered_has_error = true;

				$count++;
			}


		}



		// Specific Log
		file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Finished".($css_filtered_has_error ? " <b>WITH ERRORS</b>":'')." \r\n", FILE_APPEND);
		rename(Page::ID($this->page_ID)->logDir."/_css-filter.log", Page::ID($this->page_ID)->logDir."/".($css_filtered_has_error ? "__" : "")."css-filter.log");


		// Return true if no error
		if (!$css_filtered_has_error) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "working", "$count CSS filtering finished.");

			$logger->info("$count CSS filtering finished");
			return true;
		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "$count CSS filtering finished with error(s).");


		$logger->error("$count CSS filtering finished with error(s).");
		return false;

	}


	// 6. Complete the job!
	public function completeTheJob() {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "done", "Internalization is complete.");


		// Increase the internalization count
		$newInternalizeCount = Page::ID($this->page_ID)->internalizeCount + 1;
		Page::ID($this->page_ID)->edit('page_internalized', $newInternalizeCount);


		// Init Log
		$logger->info("Internalization #$newInternalizeCount is complete.");


		return Page::ID($this->page_ID)->cachedUrl;
	}



	// FILTERS:

	// FILTER CSS
	function filter_css($css, $url = "") {
		global $logger;


		if (empty($url))
			$url = Page::ID($this->page_ID)->remoteUrl;


		// Internalize Fonts - No Need for now !!!
		//$css = $this->detectFonts($css, $url);


		// Log
		$logger->info('CSS filtering started: '.$url);


		// All url()s
		$count = 0;
		$css = preg_replace_callback(
	        '/url\s*\(\s*[\\\'"]?(?<url>[^\\\'")]+)[\\\'"]?\s*\)/',
	        function ($css_urls) use($url) {
        		global $logger, $count;

				$url_found = $css_urls['url'];

        		$relative_url = $url_found;
        		$absolute_url = url_to_absolute($url, $url_found);
				$new_url = $absolute_url;


        		// Absolution Logs
				$logger->debug('Full URL absoluted in CSS: ', $css_urls);
				$logger->info('URL absoluted in CSS: '.$relative_url.' -> '.$new_url);
		        file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Absoluted: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				$parsed_url = parseUrl($absolute_url);
				$parsed_path = pathinfo($parsed_url['path']);
		        $file_name = $parsed_path['filename'];
		        $file_hash = isset($parsed_url['hash']) ? $parsed_url['hash'] : "";
		        $file_extension_with_hash = isset($parsed_path['extension']) ? $parsed_path['extension'] : "";
		        $file_extension = str_replace('#'.$file_hash, '', $file_extension_with_hash);
		        $file_name_hashed = $file_name.".".$file_extension_with_hash;


				// If not valid URL
				if (
					$file_name == "" ||
					$file_extension == ""
				) {

					$logger->info('Invalid URL skipped in CSS: '.$url_found);

					return "url('".$url_found."')";
				}


				// If not same domain URL
				if (
					$parsed_url['domain'] != parseUrl(Page::ID($this->page_ID)->remoteUrl)['domain']
				) {

					$logger->info('Different domain URL skipped in CSS: '.$absolute_url);

					return "url('".$url_found."')";
				}



		        // Find CSS in downloads
		        $css_resource_key = array_search($absolute_url, array_column($this->downloadedCSS, 'url'));


		        // Find Font in downloads
		        $font_resource_key = array_search($absolute_url, array_column($this->downloadedFonts, 'url'));



				if (
					$font_resource_key !== false &&
					(
						$file_extension == "ttf" ||
						$file_extension == "otf" ||
						$file_extension == "woff" ||
						$file_extension == "woff2" ||
						$file_extension == "svg" ||
						$file_extension == "eot"
					)
				) {

					// Downloaded file name
					$font_file_name = $this->downloadedFonts[$font_resource_key]['new_file_name'];

					$new_url = Page::ID($this->page_ID)->pageUri."fonts/".$file_name_hashed;


					// Font Logs
					$logger->info('Font Detected: '.$relative_url.' -> '.$new_url);
			        file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Font Detected: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				} elseif ( $css_resource_key !== false ) {

					// Downloaded file name
					$downloaded_css = $this->downloadedCSS[$css_resource_key]['new_file_name'];

					$new_url = Page::ID($this->page_ID)->pageUri."css/".$downloaded_css;


					// CSS Import Logs
					$logger->info('Imported CSS Detected: '.$relative_url.' -> '.$new_url);
			        file_put_contents( Page::ID($this->page_ID)->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Imported CSS Detected: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				}

				$count++;

	            return "url('".$new_url."')";
	        },
	        $css
	    );



		// Log
		$logger->info('CSS filtering finished: '.$url);


		return $css;

	}


	// FILTER JS - NOT YET !!!
	function filter_js() {

	}


	// RESOURCE LOG PARSER
	function parseResources($listOfSource = array()) {
		global $logger;

		$logger->debug("Initial list: ", $listOfSource);


		$parsedDownloadList = array();
		foreach ($listOfSource as $resource) {

			$resource = trim($resource);
			$resource_split = explode(' -> ', $resource);

			$new_file_name = trim($resource_split[0]);
			$resource_url = trim($resource_split[1]);


			// Prepared data
			$parsed_resource = array(
				'new_file_name' => $new_file_name,
				'url' 			=> $resource_url
			);


			// Add to the list
			$parsedDownloadList[] = $parsed_resource;
			$logger->info("Downloaded file parsed: ", $parsed_resource);

		}


		// Return the updated list
		$logger->info("New parsed list: ", $parsedDownloadList);
		return $parsedDownloadList;

	}

}