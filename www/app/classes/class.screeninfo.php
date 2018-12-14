<?php

class Screen {


	// The screen ID
	public static $screen_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($screen_ID = null) {

	    // Set the screen ID
		if ($screen_ID != null) self::$screen_ID = $screen_ID;
		return new static;

    }



	// GETTERS:

    // Get a screen info
    public function getInfo($columns = null, $array = false) {
	    global $db;

		$db->join("screen_categories c", "c.screen_cat_ID = s.screen_cat_ID", "LEFT");
	    $db->where("s.screen_ID", self::$screen_ID);

		return $array ? $db->getOne("screens s", $columns) : $db->getValue("screens s", $columns);
    }



    // ACTIONS:

    // Add a new screen
    public function addNew(
	    int $screen_ID,
	    int $parent_page_ID = null,
    	string $page_url = null,
    	string $page_name = null,
    	int $project_ID = null, // The project_ID that new page is belong to
    	int $page_width = null,
    	int $page_height = null,
    	bool $start_downloading = false
    ) {
	    global $db, $logger;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		// Get the parent page info
		if ($parent_page_ID != null) {

		    $parentPageInfo = Page::ID($parent_page_ID)->getInfo(null, true);

			if ($page_name == null) $page_name = $parentPageInfo['page_name'];
			if ($page_url == null) $page_url = $parentPageInfo['page_url'];
			if ($project_ID == null) $project_ID = $parentPageInfo['project_ID'];

		}



		// START ADDING

		// Add the new page with the screen
		$page_ID = $db->insert('pages', array(
			"screen_ID" => $screen_ID,
			"parent_page_ID" => $parent_page_ID,

			"page_url" => $page_url,
			"page_name" => $page_name,
			"project_ID" => $project_ID,

			"page_width" => $page_width,
			"page_height" => $page_height,

			"user_ID" => currentUserID()
		));



		return $page_ID;

	}

}