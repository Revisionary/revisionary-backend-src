<?php

class Category {


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


		return $array ? $db->getOne("categories", $columns) : $db->getValue("categories", $columns);
    }



    // ACTIONS:

    // Add a new project category
    public function projectNew() {
	    global $db, $log;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		// Add a new category
		$cat_ID = $db->insert('categories', array(
			"cat_type" => 'project',
			"cat_name" => 'Untitled',
			"cat_user_ID" => currentUserID()
		));


		if ($cat_ID) $log->info("Project Category #$cat_ID Added: Untitled | User #".currentUserID());


		// Return the category ID
		return $cat_ID;

	}


    // Add a new page category
    public function pageNew(
	    int $project_ID
    ) {
	    global $db, $log;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		// Add a new category
		$cat_ID = $db->insert('categories', array(
			"cat_type" => $project_ID,
			"cat_name" => 'Untitled',
			"cat_user_ID" => currentUserID()
		));


		if ($cat_ID) $log->info("Page Category #$cat_ID Added: Untitled | Project #$project_ID | User #".currentUserID());


		// Return the category ID
		return $cat_ID;

	}


	// Remove a category
	public function remove() {
		global $db, $log;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		// Remove from sorting
		$db->where('sort_type', 'category');
		$db->where('sort_object_ID', self::$category_ID);
		$db->where('sorter_user_ID', currentUserID());
		$db->delete('sorting');


		// Remove the category
		$db->where('cat_ID', self::$category_ID);
		$db->where('cat_user_ID', currentUserID());
		$removed = $db->delete('categories');


		if ($removed) $log->info("Category #".self::$category_ID." Removed: User #".currentUserID());


		return $removed;

	}


    // Rename a category
    public function rename(
	    string $text
    ) {
	    global $db, $log;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



		$db->where('cat_ID', self::$category_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my category?

		$updated = $db->update('categories', array(
			'cat_name' => $text
		));


		if ($updated) $log->info("Category #".self::$category_ID." Renamed as: '$text' | User #".currentUserID());


		return $updated;

    }

}