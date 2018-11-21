<?php

class Device {


	// The device ID
	public static $device_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($device_ID = null) {

	    // Set the device ID
		if ($device_ID != null) self::$device_ID = $device_ID;
		return new static;

    }



	// GETTERS:

    // Get a device info
    public function getInfo($columns = null, $array = false) {
	    global $db;

		$db->join("device_categories c", "c.device_cat_ID = d.device_cat_ID", "LEFT");
	    $db->where("d.device_ID", self::$device_ID);

		return $array ? $db->getOne("devices d", $columns) : $db->getValue("devices d", $columns);
    }



    // ACTIONS:

    // Add a new device
    public function addNew(
	    int $device_ID,
	    int $parent_page_ID = null,
    	string $page_url = null,
    	string $page_name = null,
    	int $project_ID = null, // The project_ID that new page is belong to
    	int $page_width = null,
    	int $page_height = null,
    	bool $start_downloading = true
    ) {
	    global $db, $logger;



	    // DB Checks !!! (Page exists?, Device exists?, etc.)



		// Get the parent page info
		if ($parent_page_ID != null) {

		    $parentPageInfo = Page::ID($parent_page_ID)->getInfo(null, true);

			if ($page_name == null) $page_name = $parentPageInfo['page_name'];
			if ($page_url == null) $page_url = $parentPageInfo['page_url'];
			if ($project_ID == null) $project_ID = $parentPageInfo['project_ID'];

		}



		// START ADDING

		// Add the new page with the device
		$page_ID = $db->insert('pages', array(
			"device_ID" => $device_ID,
			"parent_page_ID" => $parent_page_ID,

			"page_url" => $page_url,
			"page_name" => $page_name,
			"project_ID" => $project_ID,

			"page_width" => $page_width,
			"page_height" => $page_height,

			"user_ID" => currentUserID()
		));

		// Add its initial version
		$version_ID = $db->insert('versions', array(
			"page_ID" => $page_ID,
			"user_ID" => currentUserID()
		));



		// Retrieve the new page data
		$pageData = Page::ID($page_ID);



		// START DOWNLOADING
		if ($start_downloading) {


			// ADD TO QUEUE

			// Remove the existing and wrong files
			if ( file_exists($pageData->pageDir) )
				deleteDirectory($pageData->pageDir);


			// Re-Create the log folder if not exists
			if ( !file_exists($pageData->logDir) )
				mkdir($pageData->logDir, 0755, true);
			@chmod($pageData->logDir, 0755);


			// Logger
			$logger = new Katzgrau\KLogger\Logger($pageData->logDir, Psr\Log\LogLevel::DEBUG, array(
				'filename' => $pageData->logFileName,
			    'extension' => 'log', // changes the log file extension
			));


			// Add a new job to the queue
			$queue = new Queue();
			$queue_results = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.", session_id());

		}

		return $page_ID;

	}

}