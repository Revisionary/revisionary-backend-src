<?php

class Device {


	// The screen ID
	public static $device_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($device_ID = null) {

	    // Set the screen ID
		if ($device_ID != null) self::$device_ID = $device_ID;
		return new static;

    }



	// GETTERS:

    // Get a Device & Screeen info
    public function getInfo($columns = null, $array = false) {
	    global $db;


		// Bring the screens
		$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Select the device
	    $db->where("d.device_ID", self::$device_ID);


		return $array ? $db->getOne("devices d", $columns) : $db->getValue("devices d", $columns);
    }


	// Get the devices belong to specified page IDs
    public function getDevices($page_IDs) {
		global $db;


		// If no page ID specified
		if (!is_array($page_IDs) || count($page_IDs) == 0) return array();


		// Bring the screens
		$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Devices only for my pages
		$db->where("page_ID", $page_IDs, 'IN');


		// Order by device ID
		$db->orderBy('d.device_ID', 'ASC');


		return $db->get('devices d');
    }


    // Get device image
    public function getImage() {

	    $page_ID = $this->getInfo('page_ID');

	    $image_dir = Page::ID($page_ID)->getDir()."/screenshots/device-".self::$device_ID.".jpg";

		return $image_dir;

    }



    // ACTIONS:

    // Add a new device
    public function addNew(
	    int $page_ID,
	    array $screen_IDs = array(4),
    	int $device_width = null,
    	int $device_height = null,
    	bool $fromPage = false
    ) {
	    global $db;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



	    // If device width and height specified, make the screen ID 11 (custom)
	    if ($device_width != null && $device_height != null && count($screen_IDs) == 0)
	    	$screen_IDs = array(11);



		// START ADDING
		$first_device_ID = null;
		$screenCount = 0;
		foreach ($screen_IDs as $screen_ID) { $screenCount++;

			$screen_ID = intval($screen_ID);

			// Add the new page with the screen
			$device_ID = $db->insert('devices', array(
				"page_ID" => $page_ID,
				"screen_ID" => $screen_ID,
				"device_width" => $screen_ID == 11 ? $device_width : null,
				"device_height" => $screen_ID == 11 ? $device_height : null
			));

			// Stop it if error occurs
			if (!$device_ID) return false;

			// Register the first device ID
			if ($screenCount == 1) $first_device_ID = $device_ID;


		}



		// Notify the users
		if ($first_device_ID && !$fromPage) {

			$screen_ID = intval(reset($screen_IDs));
			$screenInfo = Screen::ID($screen_ID)->getInfo();
			$screen_width = $screen_ID == 11 ? $device_width : $screenInfo['screen_width'];
			$screen_height = $screen_ID == 11 ? $device_height : $screenInfo['screen_height'];

			$pageData = Page::ID($page_ID);
			$users = $pageData->getUsers();
			foreach ($users as $user_ID) {

				Notify::ID( intval($user_ID) )->mail(
					getUserInfo()['fullName']." added a new screen on ".$pageData->getInfo('page_name')." page",
					getUserInfo()['fullName']."(".getUserInfo()['userName'].") added a new screen: ".$screenInfo['screen_cat_name']."(".$screen_width."x".$screen_height.") <br><br>
					<b>Page URL</b>: ".site_url("revise/$first_device_ID")
				);

			}

		}



		// Return the first device ID
		return $first_device_ID;

	}


    // Remove a device
    public function remove() {
	    global $db;


	    $page_ID = $this->getInfo('page_ID');
	    $pageDir = Page::ID($page_ID)->getDir();
    	$screenshot_file = "$pageDir/screenshots/device-".self::$device_ID.".jpg";


		// Remove screenshot if exists
		if ( file_exists($screenshot_file) ) unlink($screenshot_file);


		// Remove the device
		$db->where('device_ID', self::$device_ID);
		return $db->delete('devices');

    }

}