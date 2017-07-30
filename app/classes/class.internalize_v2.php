<?php
use PHPHtmlParser\Dom;
use Cocur\BackgroundProcess\BackgroundProcess;


class Internalize_v2 {



	// The Page ID
	public $page_ID;


	// The Queue ID
	public $queue_ID;


	// The resources list
	public $resources;


	// HTML file to download
	public $htmlToDownload = array();

	// CSS files to download
	public $cssToDownload = array();

	// JS files to download !!! Not yet
	public $jsToDownload = array();

	// Fonts to download
	public $fontsToDownload = array();

	// Images to download !!! Not yet
	public $imagesToDownload = array();



	// When initialized
	public function __construct($page_ID) {

		// Set the page ID
		$this->page_ID = $page_ID;


		// Add job to the queue
		$this->queue_ID = $this->addToQueue();

	}




	// JOBS:

	// 1. Add the job to the queue
	public function addToQueue() {
		global $db, $logger, $queue;

		$queue_ID = $queue->new_job('internalize', $this->page_ID, "Waiting other works to be done");

		if ($queue_ID)
			return $queue_ID;

		return false;
	}


	// 2. Wait for the queue
	public function waitForQueue() {
		global $db, $queue, $logger;


		// Is current job ready to be done
		$job_ready = $queue->isReady($this->queue_ID);


		// 2. Wait for the job availability in queue
		$interval = 2;
		while (!$job_ready) {

			$logger->info("Waiting $interval second(s) for the queue.");
			sleep($interval);
			$job_ready = $queue->isReady($this->queue_ID);

		}

	}


	// 3. If job is ready to get done, open the site with slimerJS
	// 3.1. Print all the loaded resources
	// 3.2. Take screenshots
	// 3.3. Close the site
	public function browserWorks() {
		global $db, $queue, $logger;


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job is starting.");


		// Log
		$logger->info("Browser job is starting.");


		// Get page and project IDs
		$pageID = $this->page_ID;
		$projectID = Page::ID($pageID)->getPageInfo('project_ID');


		// Add image names to database
		if ( Page::ID($pageID)->getPageInfo('page_pic') == null ) {

			$db->where('page_ID', $pageID);
			$db->update('pages', array(
				'page_pic' => "page.jpg" // !!! Create a random number
			), 1);

		}

		if ( Project::ID( $projectID )->getProjectInfo('project_pic') == null ) {

			$db->where('project_ID', $projectID);
			$db->update('projects', array(
				'project_pic' => "proj.jpg" // !!! Create a random number
			), 1);

		}


		$page_image = Page::ID($pageID)->pageDeviceDir."/".Page::ID($pageID)->getPageInfo('page_pic');
		$project_image = Page::ID($pageID)->projectDir."/".Project::ID( $projectID )->getProjectInfo('project_pic');

		$page_captured = file_exists($page_image);
		$project_captured = file_exists($project_image);

		// If both already captured and page is already internalized, return
		if (
			$project_captured && $page_captured &&
			file_exists( Page::ID($pageID)->pageTempFile )
		)
			return false;


		// Get info
		$url = Page::ID($pageID)->remoteUrl;
		$logDir = Page::ID($pageID)->logDir;
		$deviceID = Page::ID($pageID)->getPageInfo('device_ID');
		$width = Device::ID($deviceID)->getDeviceInfo('device_width');
		$height = Device::ID($deviceID)->getDeviceInfo('device_height');
		$page_image = $page_captured ? "done" : $page_image;
		$project_image = $project_captured ? "done" : $project_image;

		// Process directories
		$slimerjs = realpath('..')."/bin/slimerjs-0.10.3/slimerjs";
		$capturejs = dir."/app/bgprocess/capture_v2.js";

		$process_string = "$slimerjs $capturejs $url $width $height $page_image $project_image $logDir";

		$process = new BackgroundProcess($process_string);
		$process->run($logDir."/capture.log", true);


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job has started.", $process->getPid());


		// LOGS
		$logger->info("Browser jobs process string: ". $process_string);
		$logger->info("Browser jobs process ID: ". $process->getPid());


		// Wait for the resources file creation
		$resources_file = $logDir."/resources.log";
		while ( !file_exists($resources_file) ) {

			$logger->info("Waiting 2 seconds for the resources.log file");
			sleep(2);

		}


		// Wait for the resources completely written
		$resources = preg_split('/\r\n|[\r\n]/', trim(file_get_contents($resources_file)));
		$last_line = end($resources);

		while ( $last_line != "DONE" ) {

			$logger->info("Waiting 2 seconds for the resources file to complete. Last Resource: ". $last_line);
			sleep(2);
			$resources = preg_split('/\r\n|[\r\n]/', trim(file_get_contents($resources_file)));
			$last_line = end($resources);

		}
		$resources = array_unique($resources);
		array_pop($resources);
		$this->resources = $resources;



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Resources list is ready.", $process->getPid());

		$logger->info("Resources list is ready.");

	}


	// 5. Parse and detect files to download
	public function detectFilesToDownload() {
		global $db, $logger, $queue;


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Started parsing the resources.");

		// Log
		$logger->info("Start parsing the resources.", $this->resources);

		$count = 0;
		foreach ($this->resources as $resource) {

			$resource = explode(' -> ', $resource);
			$content_type = trim($resource[0]);
			$resource_url = trim($resource[1]);

			$url = new \Purl\Url($resource_url);
			$path_parts = pathinfo($url->path);
			$extension = isset($path_parts['extension']) ? strtolower($path_parts['extension']) : "";


			// Register the HTML file
			if ($count == 0) {


				// If redirected?
				if ( $resource_url != Page::ID($this->page_ID)->remoteUrl ) {

					$old_url = Page::ID($this->page_ID)->remoteUrl;

					// Update the remote URL on database
					$data = array(
						'page_url' => $resource_url
					);
					$db->where ('page_ID', $this->page_ID);
					if ( $db->update('pages', $data) )
					    $logger->info("Page URL has updated. Old URL: $old_url", $data);
					else
					    $logger->error("Page URL couldn't be updated. ".$db->getLastError()." - Old URL: $old_url", $data);
				}

				// Add to the list
				$this->htmlToDownload[] = $resource_url;
				$logger->info("HTML page added to the download queue: $resource_url");


			} elseif (
				$content_type == "text/css" ||
				$extension == "css"
			) {


				// Add to the list
				$this->cssToDownload[] = $resource_url;
				$logger->info("CSS file added to the download queue: $resource_url");


			} elseif (
				strpos($content_type, 'javascript') !== false ||
				$content_type == "application/javascript" ||
				$extension == "js"
			) {


				// Add to the list
				$this->jsToDownload[] = $resource_url;
				$logger->info("JS file added to the download queue: $resource_url");


			} elseif (
				strpos($content_type, 'font') !== false ||
				$content_type == "font/otf" ||
				$content_type == "font/ttf" ||
				$content_type == "font/woff" ||
				$content_type == "font/woff2" ||
				$content_type == "image/svg+xml" ||
				$content_type == "application/x-font-ttf" ||
				$content_type == "application/x-font-truetype" ||
				$content_type == "application/x-font-opentype" ||
				$content_type == "application/font-woff" ||
				$content_type == "application/font-woff2" ||
				$content_type == "application/octet-stream" ||
				$content_type == "application/vnd.ms-fontobject" ||
				$content_type == "application/font-sfnt" ||
				$extension == "ttf" ||
				$extension == "otf" ||
				$extension == "woff" ||
				$extension == "woff2" ||
				$extension == "svg" ||
				$extension == "eot"
			) {


				// Add to the list
				$this->fontsToDownload[] = $resource_url;
				$logger->info("Font file added to the download queue: $resource_url");


			} elseif (
				strpos($content_type, 'image/') !== false ||
				$extension == "jpg" ||
				$extension == "jpeg" ||
				$extension == "png" ||
				$extension == "gif"
			) {


				// Add to the list
				$this->imagesToDownload[] = $resource_url;
				$logger->info("Image file added to the download queue: $resource_url");


			} else {


				// Add to the list
				$logger->info("Couldn't added to any list: $resource_url");


			}



			$count++;
		}


	}




}