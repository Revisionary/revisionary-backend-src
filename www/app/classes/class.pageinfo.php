<?php

class Page {


	// The page ID
	public static $page_ID;

	// The selected page version
	public static $setPageVersion;

	// The page version
	public $pageVersion;

	// The page device
	public $pageDevice;

	// The project ID
	public $project_ID;

	// The remote URL
	public $remoteUrl;

	// Current user ID
	public $user_ID;

	// Internalization Count
	public $internalizeCount;


	// Project Directory
	public $projectDir;

	// Page Directory
	public $pageDir;

	// Page Device Directory
	public $pageDeviceDir;

	// Page Url
	public $pageUri;

	// Page File Name
	public $pageFileName = "index.html";

	// Page File
	public $pageFile;

	// Page File URL
	public $cachedUrl;

	// Page Image Path
	public $pageImagePath;

	// Log File
	public $logDir;

	// Log File Name
	public $logFileName;

	// Log File
	public $logFile;

	// Page Status
	public $pageStatus;

	// Page Temporary File
	public $pageTempFile;

	// Page Character Set
	public $pageCharSet = "";


	// Paths
	public $userPath;
	public $projectPath;
	public $pagePath;
	public $devicePath;
	public $versionPath;
	public $fullPath;



	// Debug
	public $debug = false;




	// SETTERS:

	public function __construct() {

		$pageInfo = $this->getInfo(null, true);

		// Set the project ID
        $this->project_ID = $pageInfo['project_ID'];

        // Set the version number
        if (self::$setPageVersion == null) $this->pageVersion = $this->getPageVersion();
        else $this->pageVersion = self::$setPageVersion;

        // Set the device
        $this->pageDevice = $pageInfo['device_ID'];

		// Set the remote url
        $this->remoteUrl = $pageInfo['page_url'];

        // Set the user ID
        $this->user_ID = $pageInfo['user_ID'];

        // Set the internalization count
		$this->internalizeCount = $pageInfo['page_internalized'];


		// Paths
        $userPath = $this->userPath = "user-".$this->user_ID;
        $projectPath = $this->projectPath = "project-".$this->project_ID;
        $pagePath = $this->pagePath = "page-".($pageInfo['parent_page_ID'] != null ? $pageInfo['parent_page_ID'] : self::$page_ID);
        $devicePath = $this->devicePath = "device-".$this->pageDevice;
        $versionPath = $this->versionPath = $this->pageVersion;

		$fullPath = $this->fullPath = $userPath."/".$projectPath."/".$pagePath."/".$devicePath."/".$versionPath."/";


        // Set the project directory
        $this->projectDir = dir."/assets/cache/".$userPath."/".$projectPath;

        // Set the page device directory
        $this->pageDeviceDir = $this->projectDir."/".$pagePath."/".$devicePath;

        // Set the page cache directory
        $this->pageDir = $this->pageDeviceDir."/".$versionPath;

        // Set the page cache directory URL
        $this->pageUri = cache_url($fullPath, (substr($this->remoteUrl, 0, 8) == "https://" ? true : false));

        // Set the page cache file
        $this->pageFile = $this->pageDir."/".$this->pageFileName;

        // Set the page image file path
        $this->pageImagePath = $this->pageDeviceDir."/".$pageInfo['page_pic'];

        // Set the log file
        $this->logDir = $this->pageDir."/logs";

        // Set the log file name
        $this->logFileName = "internalize-process-php";

        // Set the log file
        $this->logFile = $this->logDir."/".$this->logFileName.".log";

        // Set the page cache URL
        $this->cachedUrl = $this->pageUri.$this->pageFileName;



        // Set the page status
        $this->pageStatus = $this->getPageStatus();


    }


	// ID Setter
    public static function ID($page_ID = null, $setVersion = null) {

	    // Set the page ID
		if ($page_ID != null) self::$page_ID = $page_ID;

		// Set the version number
		if ($setVersion != null) self::$setPageVersion = "v".$setVersion;

		return new static;

    }




	// GETTERS:

	// Get page info
    public function getInfo($columns = null, $array = false) {
	    global $db;

	    $db->where('page_ID', self::$page_ID);

	    return $array ? $db->getOne("pages", $columns) : $db->getValue("pages", $columns);
    }


    // Get the page version
    public function getPageVersion() {
	    global $db;

		$db->where('user_ID', $this->getInfo('user_ID'));
		$db->where('page_ID', self::$page_ID);

		// Show the final one
		$db->orderBy('version_number');

	    $pageVersion = $db->getValue('versions', 'version_number');

		if ($pageVersion)
			return "v".$pageVersion;

	    return "v0.1";

    }


    // Get the page download status
    public function getPageStatus($static = false) {


		// 0% - WAITING FOR THE QUEUE
		$process_status = [
			"status" => "waiting",
			"description" => "Waiting for the queue",
			"percentage" => 0
		];


		if (!file_exists($this->logDir))
			$process_status = [
				"status" => "Ready to Download",
				"description" => "Page needs to be downloaded",
				"percentage" => 0
			];


		if ($static) {

			// DAMAGED PAGES
			if (
				!file_exists($this->pageFile) ||
				!file_exists($this->logDir."/html-filter.log") ||
				!file_exists($this->logDir."/css-filter.log")
			)
				$process_status = [
					"status" => "download-needed",
					"description" => "Download needed",
					"percentage" => 0
				];

		}


		// 10% - PAGE IS DOWNLOADING
		if (
			file_exists($this->logDir."/browser.log")
		)
			$process_status = [
				"status" => "downloading-page",
				"description" => "Downloading the page",
				"percentage" => 25
			];


		// 25% - PAGE IS DOWNLOADED
		if (
			file_exists($this->pageFile)
		)
			$process_status = [
				"status" => "downloaded-page",
				"description" => "Page is downloaded",
				"percentage" => 25
			];


		// 50% - UPDATING THE PAGE
		if (file_exists($this->logDir."/_html-filter.log"))
			$process_status = [
				"status" => "updating-html",
				"description" => "Updating the page",
				"percentage" => 50
			];


		// 70% - PAGE UPDATED
		if (file_exists($this->logDir."/html-filter.log"))
			$process_status = [
				"status" => "updated-html",
				"description" => "Page updated",
				"percentage" => 70
			];

		// 0% - HTML Filter Error
		if (file_exists($this->logDir."/__html-filter.log"))
			$process_status = [
				"status" => "updating-html-error",
				"description" => "The page couldn't be updated",
				"percentage" => 0
			];


		// 75% - FIXING THE STYLES
		if (file_exists($this->logDir."/_css-filter.log"))
			$process_status = [
				"status" => "updating-css",
				"description" => "Fixing the styles",
				"percentage" => 75
			];


		// 95% - STYLES ARE PERFECTED
		if (file_exists($this->logDir."/css-filter.log"))
			$process_status = [
				"status" => "updated-css",
				"description" => "Styles are perfected",
				"percentage" => 95
			];

		// 0% - CSS Filter Error
		if (file_exists($this->logDir."/__css-filter.log"))
			$process_status = [
				"status" => "updating-css-error",
				"description" => "The styles couldn't be fixed",
				"percentage" => 0
			];



		// 100% - READY
		if (
			file_exists($this->pageFile) &&
			file_exists($this->logDir."/html-filter.log") &&
			file_exists($this->logDir."/css-filter.log")
		)
			$process_status = [
				"status" => "ready",
				"description" => "Ready! Loading the site",
				"percentage" => 100
			];


		return $process_status;

    }


	// Get the current download process
    public function getDownloadedQuantity($type = "total", $fileType = "css") {

		$downloading = $this->logDir."/_".$fileType.".log";
		$downloaded = $this->logDir."/".$fileType.".log";
		$file = file_exists($downloaded) ? $downloaded : $downloading;
		$content = "";

		if ( file_exists($file) )
			$content = file_get_contents($file);

		if ($type == "filtred")
			return substr_count($content, 'Filtred');


		preg_match('#\{TOTAL:(?<total>.*?)\}#', $content, $match);
		if ( isset($match['total']) )
			return $match['total'];

		return "";

    }



    // ACTIONS

    // Add a new page
    public function addNew(
    	string $page_url,
    	string $page_name,
    	int $project_ID, // The project_ID that new page is belong to
    	int $category_ID = 0, // The category_ID that new page is belong to
    	int $order_number = 0, // The order number
    	array $devices = array(4), // Array of device_IDs
    	array $page_shares = array(), // Array of users that needs to be shared to
    	int $page_width = null,
    	int $page_height = null,
    	bool $start_downloading = true
    ) {
	    global $db, $logger;


		// Security check !!!
		if ( $page_url == "" || $page_name == "" ) return false;



		// More DB Checks of arguments !!!



		// START ADDING
		$parent_page_ID = null;
		$device_count = 0;
		foreach ($devices as $device_ID) {


			// If the page has no custom screen size
			if ($device_ID != 11)
				$page_width = $page_height = null;


			// Add the device
			$page_ID = Device::ID()->addNew(
				$device_ID,
				$parent_page_ID,
				$page_url,
				$page_name,
				$project_ID,
				$page_width,
				$page_height,
				$start_downloading && $parent_page_ID == null
			);


			// After adding first page, record the parent_page_ID as the first page added
			if ( $device_count == 0 )
				$parent_page_ID = $page_ID;


			// Increase the device count
			$device_count++;



			// SHARE - Add the page share to only parent page - USE SHARE API LATER !!!
			if ( count($page_shares) > 0 && $device_count == 1 ) {

				// Add each people that need to be shared
				foreach ($page_shares as $share_to) {

					$share_ID = $db->insert('shares', array(
						"share_type" => 'page',
						"shared_object_ID" => $page_ID,
						"share_to" => $share_to,
						"sharer_user_ID" => currentUserID()
					));

				}

			}



			// CATEGORIZE
			if ($category_ID != "0") {

				// Add category record to the "page_cat_connect" table
				$cat_id = $db->insert('page_cat_connect', array(
					"page_cat_page_ID" => $page_ID,
					"page_cat_ID" => $category_ID,
					"page_cat_connect_user_ID" => currentUserID()
				));

			}



			// ORDER
			if ($order_number != "0") {

				// Add order number to the "sorting" table
				$cat_id = $db->insert('sorting', array(
					"sort_type" => 'page',
					"sort_object_ID" => $page_ID,
					"sort_number" => $order_number,
					"sorter_user_ID" => currentUserID()
				));

			}



		} // The device loop


		return $parent_page_ID;

    }



    // Edit a page !!!
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the page
		$db->where('page_ID', self::$page_ID);
		$page_updated = $db->update('pages', array($column => $new_value));


		return $page_updated;
    }



    // Archive a page !!!
    public function archive() {

    }



    // Delete a page !!!
    public function delete() {

    }



    // Remove a page !!!
    public function remove() {

    }



    // Share to someone !!!
    public function share() {

    }

}