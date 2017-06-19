<?php

class Page {


	// The page ID
	public static $pageId;

	// The page version
	public $pageVersion;

	// The project ID
	public $projectId;

	// The remote URL
	public $remoteUrl;

	// Current user ID
	public $userId;


	// Page Directory
	public $pageDir;

	// Page Url
	public $pageUri;

	// Page File Name
	public $pageFileName = "index.html";

	// Page File
	public $pageFile;

	// Page File
	public $cachedUrl;

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

	public function __construct($pageId) {


		// Set the page ID
		self::$pageId = $pageId;

		// Set the project ID
        $this->projectId = $this->getProjectId();

        // Set the version number
        $this->pageVersion = $this->getPageVersion();

		// Set the remote url
        $this->remoteUrl = $this->getRemoteUrl();

        // Set the user ID
        $this->userId = currentUserID();

        // Set the page cache directory
        $this->pageDir = dir."/assets/cache/sites/".$this->userId."/".$this->projectId."/".self::$pageId."/".$this->pageVersion."/";

        // Set the page cache directory URL
        $this->pageUri = cache_url("sites/".$this->userId."/".$this->projectId."/".self::$pageId."/".$this->pageVersion."/");
        if ( substr($this->remoteUrl, 0, 8) == "https://")
        	$this->pageUri = cache_url("sites/".$this->userId."/".$this->projectId."/".self::$pageId."/".$this->pageVersion."/", true); // Force SSL

        // Set the page cache file
        $this->pageFile = $this->pageDir.$this->pageFileName;

        // Set the log file
        $this->logDir = $this->pageDir."logs/";

        // Set the log file
        $this->logFile = $this->logDir."process.log";

        // Set the page cache file
        $this->pageTempFile = $this->pageDir.$this->pageFileName;

        // Set the page cache URL
        $this->cachedUrl = $this->pageUri.$this->pageFileName;

        // Set the page status
        $this->pageStatus = $this->getPageStatus();


    }


	// ID Setter
    public static function ID($pageId) {

	    // Set the page ID
		if ( is_null( self::$pageId ) ) {
			self::$pageId = new self($pageId);
		}
		return self::$pageId;

    }




	// GETTERS:

    // Get the Project ID
    public function getProjectId() {

	    // GET IT FROM DB...
	    $projectId = "twelve12";

	    return $projectId;
    }


    // Get the page version
    public function getPageVersion() {

	    // GET IT FROM DB...
	    $pageVersion = "v0.1";

	    return $pageVersion;
    }


    // Get the remote url from the Page ID
    public function getRemoteUrl() {

	    // GET IT FROM DB...
	    //$remoteUrl = "http://www.cuneyt-tas.com/kitaplar.php";
	    //$remoteUrl = "http://www.bilaltas.net";
	    //$remoteUrl = "http://www.cuneyt-tas.com";
	    //$remoteUrl = "http://dev.cuneyt-tas.com";
	    //$remoteUrl = "https://www.twelve12.com";
	    //$remoteUrl = "https://www.google.com";
	    //$remoteUrl = "https://www.godaddy.com/";
	    //$remoteUrl = "http://www.kariyer.net/";
	    $remoteUrl = "http://do.ready-for-feedback.com/twelve-12/gelato-cone/";

	    if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
	    	$remoteUrl = $_GET['new_url'];


	    return $remoteUrl;
    }


    // Get the page download status
    public function getPageStatus() {

		$process_status = [
			"status" => "downloading",
			"description" => "Page is downloading"
		];



		if (file_exists($this->logDir."_html.log"))
			$process_status = [
				"status" => "downloading-html",
				"description" => "HTML is downloading"
			];

		if (file_exists($this->logDir."__html.log"))
			$process_status = [
				"status" => "download-error-html",
				"description" => "HTML couldn't downloaded"
			];

		if (file_exists($this->logDir."html.log"))
			$process_status = [
				"status" => "downloaded-html",
				"description" => "Starting to download CSS files"
			];



		if (file_exists($this->logDir."_css.log"))
			$process_status = [
				"status" => "downloading-css",
				"description" => "CSS files are downloading"
			];

		if (file_exists($this->logDir."__css.log"))
			$process_status = [
				"status" => "download-error-css",
				"description" => "CSS files couldn't downloaded"
			];

		if (file_exists($this->logDir."css.log"))
			$process_status = [
				"status" => "downloaded-css",
				"description" => "Starting to download fonts"
			];



		if (file_exists($this->logDir."_font.log"))
			$process_status = [
				"status" => "downloading-fonts",
				"description" => "Fonts are downloading"
			];

		if (file_exists($this->logDir."__font.log"))
			$process_status = [
				"status" => "download-error-fonts",
				"description" => "Fonts could't downloaded"
			];

		if (file_exists($this->logDir."font.log"))
			$process_status = [
				"status" => "downloaded-font",
				"description" => "Fonts are downloaded"
			];



		if (
			file_exists($this->logDir."html.log") &&
			file_exists($this->logDir."css.log") &&
			file_exists($this->logDir."font.log")
		)
			$process_status = [
				"status" => "ready",
				"description" => "Ready! Starting"
			];


		return $process_status;

    }


	// Get the current download process
    public function getDownloadedQuantity($type = "total", $fileType = "css") {

		$downloading = $this->logDir."_".$fileType.".log";
		$downloaded = $this->logDir.$fileType.".log";
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