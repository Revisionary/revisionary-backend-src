<?php
use PHPHtmlParser\Dom;
use Cocur\BackgroundProcess\BackgroundProcess;


class Internalize_v2 {



	// The Page ID
	public $page_ID;


	// The Queue ID
	public $queue_ID;



	public function __construct($page_ID) {

		// Set the page ID
		$this->page_ID = $page_ID;


		// Add job to the queue
		$this->queue_ID = $this->addToQueue();

	}



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


	}


	// 4. Wait for the resources file
	public function waitForResources() {

		// ...

	}


	// 5. Parse and detect files to download
	public function detectFilesToDownload() {

		// Wait for the browser job is done


	}




}