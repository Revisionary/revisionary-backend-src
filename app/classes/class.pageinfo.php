<?php

class Page {


	// The page ID
	public static $pageId;

	// The page version
	public $pageVersion;

	// The page device
	public $pageDevice;

	// The project ID
	public $projectId;

	// The remote URL
	public $remoteUrl;

	// Current user ID
	public $userId;


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

	// Log File
	public $logFile;

	// Page Status
	public $pageStatus;

	// Page Temporary File
	public $pageTempFile;

	// Page Character Set
	public $pageCharSet = "";


	// Debug
	public $debug = false;




	// SETTERS:

	public function __construct() {

		// Set the project ID
        $this->projectId = $this->getPageInfo('project_ID');

        // Set the version number
        $this->pageVersion = $this->getPageVersion();

        // Set the device
        $this->pageDevice = $this->getPageInfo('device_ID');

		// Set the remote url
        $this->remoteUrl = $this->getPageInfo('page_url');

        // Set the user ID
        $this->userId = $this->getPageInfo('user_ID');


		// Paths
        $userPath = "user-".$this->userId;
        $projectPath = "project-".$this->projectId;
        $pagePath = "page-".($this->getPageInfo('parent_page_ID') != null ? $this->getPageInfo('parent_page_ID') : self::$pageId);
        $devicePath = "device-".$this->pageDevice;
        $versionPath = $this->pageVersion;

		$fullPath = $userPath."/".$projectPath."/".$pagePath."/".$devicePath."/".$versionPath."/";


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
        $this->pageImagePath = $userPath."/".$projectPath."/".$pagePath."/".$devicePath."/".$this->getPageInfo('page_pic');

        // Set the log file
        $this->logDir = $this->pageDir."/logs";

        // Set the log file
        $this->logFile = $this->logDir."/process.log";

        // Set the page cache file
        $this->pageTempFile = $this->pageDir."/".$this->pageFileName;

        // Set the page cache URL
        $this->cachedUrl = $this->pageUri.$this->pageFileName;



        // Set the page status
        $this->pageStatus = $this->getPageStatus();


    }


	// ID Setter
    public static function ID($pageId) {

	    // Set the page ID
		self::$pageId = $pageId;
		return new static;

    }




	// GETTERS:

	// Get page info
    public function getPageInfo($column) {
	    global $db;

	    $db->where('page_ID', self::$pageId);
	    $page = $db->getOne('pages', $column);
		if ($page)
			return $page[$column];

	    return false;
    }


    // Get the page version !!!
    public function getPageVersion() {
	    global $db;


		$db->where('version_user_ID', $this->getPageInfo('user_ID'));
		$db->where('version_page_ID', self::$pageId);

		// Show the final one
		$db->orderBy('version_number');

	    $pageVersion = $db->getValue('versions', 'version_number');

		if ($pageVersion)
			return "v".$pageVersion;

	    return "v0.1";

    }


    // Get the page download status
    public function getPageStatus() {

		$process_status = [
			"status" => "downloading",
			"description" => "Page is downloading"
		];



		if (file_exists($this->logDir."/_html.log"))
			$process_status = [
				"status" => "downloading-html",
				"description" => "HTML is downloading"
			];

		if (file_exists($this->logDir."/__html.log"))
			$process_status = [
				"status" => "download-error-html",
				"description" => "HTML couldn't downloaded"
			];

		if (file_exists($this->logDir."/html.log"))
			$process_status = [
				"status" => "downloaded-html",
				"description" => "Starting to download CSS files"
			];



		if (file_exists($this->logDir."/_css.log"))
			$process_status = [
				"status" => "downloading-css",
				"description" => "CSS files are downloading"
			];

		if (file_exists($this->logDir."/__css.log"))
			$process_status = [
				"status" => "download-error-css",
				"description" => "CSS files couldn't downloaded"
			];

		if (file_exists($this->logDir."/css.log"))
			$process_status = [
				"status" => "downloaded-css",
				"description" => "Starting to download fonts"
			];



		if (file_exists($this->logDir."/_font.log"))
			$process_status = [
				"status" => "downloading-fonts",
				"description" => "Fonts are downloading"
			];

		if (file_exists($this->logDir."/__font.log"))
			$process_status = [
				"status" => "download-error-fonts",
				"description" => "Fonts could't downloaded"
			];

		if (file_exists($this->logDir."/font.log"))
			$process_status = [
				"status" => "downloaded-font",
				"description" => "Fonts are downloaded"
			];



		if (
			file_exists($this->logDir."/html.log") &&
			file_exists($this->logDir."/css.log") &&
			file_exists($this->logDir."/font.log")
		)
			$process_status = [
				"status" => "ready",
				"description" => "Ready! Starting"
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

		if ($type == "downloaded")
			return substr_count($content, 'Downloaded');


		preg_match('#\{TOTAL:(?<total>.*?)\}#', $content, $match);
		if ( isset($match['total']) )
			return $match['total'];

		return "";

    }

}