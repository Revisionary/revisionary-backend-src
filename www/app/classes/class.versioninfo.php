<?php

class Version {


	public static $version_ID;
	public static $versionInfo;

	public $remoteUrl;
	public $cachedUrl;

	public $versionDir;
	public $versionFileName = "index.html";
	public $versionFile;
	public $versionUri;

	public $logDir;
	public $logFileName = "internalize-process-php";
	public $logFileExtension = "log";
	public $logFile;

	public $internalizeCount;
	public $versionStatus;



	// SETTERS:

	public function __construct() {


		$page_ID = self::$versionInfo['page_ID'];
		$pageData = Page::ID($page_ID);


		$this->remoteUrl = $pageData->getInfo('page_url');
		$this->internalizeCount = self::$versionInfo['version_internalized'];


		// Version directory
		$this->versionDir = $this->getDir();
        $this->versionFile = $this->versionDir."/".$this->versionFileName;


        // Paths
		$fullPath = $this->versionDir."/";
		$fullPath = str_replace(cache, "", $fullPath);


        // Set the version cache directory URL
        $this->versionUri = cache_url($fullPath, (substr($this->remoteUrl, 0, 8) == "https://" ? true : false));
        $this->cachedUrl = $this->versionUri.$this->versionFileName;


		// Log directory
		$this->logDir = $this->versionDir."/logs";
        $this->logFile = $this->logDir."/".$this->logFileName.".".$this->logFileExtension;



        // Set the version status
        $this->versionStatus = $this->getVersionStatus();



    }


	// ID Setter
    public static function ID($version_ID = null) {
	    global $db;


	    // Set the version ID
		if ($version_ID != null && is_numeric($version_ID)) {


			$db->where('version_ID', $version_ID);
			$versionInfo = $db->getOne("versions");

			if ( $versionInfo ) {

				self::$version_ID = $version_ID;
				self::$versionInfo = $versionInfo;
				return new static;

			}


		}


	    // For the new version
		if ($version_ID == null) {

			self::$version_ID = "new";
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get version info
    public function getInfo($column = null) {

	    return $column == null ? self::$versionInfo : self::$versionInfo[$column];

    }


    // Get the version download status
    public function getVersionStatus($static = false) {


		// 0% - WAITING FOR THE QUEUE
		$process_status = [
			"status" => "waiting",
			"description" => "Waiting for the queue",
			"percentage" => 0
		];


		if (!file_exists($this->logDir))
			$process_status = [
				"status" => "Ready to Download",
				"description" => "Version needs to be downloaded",
				"percentage" => 0
			];


		if ($static) {

			// DAMAGED VERSIONS
			if (
				!file_exists($this->versionFile) ||
				!file_exists($this->logDir."/html-filter.log") ||
				!file_exists($this->logDir."/css-filter.log")
			)
				$process_status = [
					"status" => "download-needed",
					"description" => "Download needed",
					"percentage" => 0
				];

		}


		// 10% - VERSION IS DOWNLOADING
		if (
			file_exists($this->logDir."/browser.log")
		)
			$process_status = [
				"status" => "downloading-version",
				"description" => "Downloading the version",
				"percentage" => 25
			];


		// 25% - VERSION IS DOWNLOADED
		if (
			file_exists($this->versionFile)
		)
			$process_status = [
				"status" => "downloaded-version",
				"description" => "Version is downloaded",
				"percentage" => 25
			];


		// 50% - UPDATING THE VERSION
		if (file_exists($this->logDir."/_html-filter.log"))
			$process_status = [
				"status" => "updating-html",
				"description" => "Updating the version",
				"percentage" => 50
			];


		// 70% - VERSION UPDATED
		if (file_exists($this->logDir."/html-filter.log"))
			$process_status = [
				"status" => "updated-html",
				"description" => "Version updated",
				"percentage" => 70
			];

		// 0% - HTML Filter Error
		if (file_exists($this->logDir."/__html-filter.log"))
			$process_status = [
				"status" => "updating-html-error",
				"description" => "The version couldn't be updated",
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
			file_exists($this->versionFile) &&
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


    // Get version directory
    public function getDir() {

		// Paths
        $pagePath = Page::ID(self::$versionInfo['page_ID'])->getDir();
        $versionPath = "version-".self::$version_ID;


        // Set the version directory
        return "$pagePath/$versionPath";
    }


	// Get version users
	public function getUsers($include_me = false) {
		global $db;


		$page_ID = self::$versionInfo['page_ID'];
		$pageData = Page::ID($page_ID);


		$users = array();


		// Get the project users
		$users = array_merge($users, $pageData->getUsers($include_me));


		// Remove duplicates
		$users = array_unique($users);


		// Exclude myself
		if ( !$include_me && ($user_key = array_search(currentUserID(), $users)) !== false ) {
		    unset($users[$user_key]);
		}


		return $users;

	}




    // ACTIONS

    // Add a new version
    public function addNew(
    	int $page_ID, // The page_ID that new version is belong to
    	string $version_name = null
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Add the version
		$version_ID = $db->insert('versions', array(
			"version_name" => $version_name,
			"page_ID" => $page_ID
		));



/*
		// If version added
		if ($version_ID) {


			$version_link = site_url('version/'.$version_ID);
			$page_name = " [".Page::ID($page_ID)->getInfo('page_name')."]";


			// Get the users to notify
			$users = Version::ID($version_ID)->getUsers();


			// Web notification
			Notify::ID($users)->web("new", "version", $version_ID);


			// Email notification
			Notify::ID($users)->mail(
				getUserInfo()['fullName']." added a new version: ".$version_name.$page_name,
				getUserInfo()['fullName']." added a new version: ".$version_name.$page_name." <br>
				<b>Version URL</b>: $version_url <br><br>
				<a href='$version_link' target='_blank'>$version_link</a>"
			);


			// Site log
			$log->info("Version #$version_ID Added: $version_name($version_url) | User #".currentUserID());


		}
*/


		return $version_ID;

	}



    // Edit a version
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Update the version
		$db->where('version_ID', self::$version_ID);
		$version_updated = $db->update('versions', array($column => $new_value));


		// Site log
		if ($version_updated) $log->info("Version #".self::$version_ID." Updated: '$column => $new_value' | Page #".$this->getInfo('page_ID')." | User #".currentUserID());


		return $version_updated;
    }



    // Remove a version
    public function remove() {
	    global $db, $log;



		// More DB Checks of arguments !!! (Do I have access to this version?!)



		// Get the page info
    	$page_ID = self::$versionInfo['page_ID'];
    	$pageData = Page::ID($page_ID);
    	$pageInfo = $pageData->getInfo();


    	// Get the project info
    	$project_ID = $pageInfo['project_ID'];
    	$projectData = Project::ID($project_ID);
    	$projectInfo = $projectData->getInfo();



    	if ( !User::ID()->canAccess( self::$version_ID, 'version') ) return false;



		// VERSION REMOVAL
		$db->where('version_ID', self::$version_ID);
		$version_removed = $db->delete('versions');



		// Delete the version folder
		deleteDirectory( cache."/projects/project-$project_ID/version-".self::$version_ID."/" );



		// Delete the notifications if exists
		$db->where('object_type', 'version');
		$db->where('object_ID', self::$version_ID);
		$db->delete('notifications');



		// Site log
		if ($version_removed) $log->info("Version #".self::$version_ID." Removed: '".$this->getInfo('version_name')."' | Project Name: ".$projectData->getInfo('project_name')." | Project #".$projectData->getInfo('project_ID')." | Page Name: ".$pageData->getInfo('page_name')." | Page #".$this->getInfo('page_ID')."User #".currentUserID());


		return $version_removed;

    }



    // Rename a version
    public function rename(
	    string $text
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		$current_version_name = $this->getInfo('version_name');


    	$db->where('version_ID', self::$version_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my version?

		$version_renamed = $db->update('versions', array(
			'version_name' => $text
		));


		// Site log
		if ($version_renamed) $log->info("Version #".self::$version_ID." Renamed: '$current_version_name => $text' | Page #".$this->getInfo('page_ID')." | User #".currentUserID());



		return $version_renamed;

    }

}