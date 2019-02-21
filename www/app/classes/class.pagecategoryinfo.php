<?php

class Pagecategory {


	// The category ID
	public static $category_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($category_ID = null) {

	    // Set the category ID
		if ($category_ID != null) self::$category_ID = $category_ID;
		return new static;

    }



	// GETTERS:

    // Get a Device & Screeen info
    public function getInfo($columns = null, $array = false) {
	    global $db;


		// Select the category
	    $db->where("cat_ID", self::$category_ID);


		return $array ? $db->getOne("pages_categories", $columns) : $db->getValue("pages_categories", $columns);
    }



    // ACTIONS:

    // Add a new page category
    public function addNew(
	    int $project_ID
    ) {
	    global $db, $log;



	    // DB Checks !!!



		// Add a new category
		$cat_ID = $db->insert('pages_categories', array(
			"project_ID" => $project_ID
		));


		if ($cat_ID) $log->info("Page Category #$cat_ID Added: Untitled | Project #$project_ID | User #".currentUserID());


		// Return the category ID
		return $cat_ID;

	}


	// Remove a category
	public function remove() {
		global $db, $log;



	    // DB Checks !!!



		// Remove the category
		$db->where('cat_ID', self::$category_ID);
		$removed = $db->delete('pages_categories');


		if ($removed) $log->info("Page Category #".self::$category_ID." Removed: User #".currentUserID());


		return $removed;

	}


    // Rename a category
    public function rename(
	    string $text
    ) {
	    global $db, $log;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		$db->where('cat_ID', self::$category_ID);
		$updated = $db->update('pages_categories', array(
			'cat_name' => $text
		));


		if ($updated) $log->info("Page Category #".self::$category_ID." Renamed as: '$text' | User #".currentUserID());


		return $updated;

    }


    // Reorder a category
    public function reorder(
	    int $order_number
    ) {
	    global $db;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		$db->where('cat_ID', self::$category_ID);
		$updated = $db->update('pages_categories', array(
			'cat_order_number' => $order_number
		));


		return $updated;

    }

}