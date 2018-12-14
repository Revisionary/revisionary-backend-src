<?php

class Page {


	// The page ID
	public static $page_ID;

	public $project_ID;
	public $remoteUrl;



	// SETTERS:

	public function __construct() {

		$pageInfo = $this->getInfo('*', true);

		$this->project_ID = $pageInfo['project_ID'];
		//$this->remoteUrl = $pageInfo['project_ID'];

    }


	// ID Setter
    public static function ID($page_ID = null) {

	    // Set the page ID
		if ($page_ID != null) self::$page_ID = $page_ID;
		return new static;

    }




	// GETTERS:

    // Get page info
    public function getInfo($columns = null, $array = false) {
	    global $db;

	    $db->where('page_ID', self::$page_ID);

	    return $array ? $db->getOne("pages", $columns) : $db->getValue("pages", $columns);
    }


    // Get page directory
    public function getDir() {

		// Paths
        $projectPath = Project::ID($this->project_ID)->getDir();
        $pagePath = "page-".self::$page_ID;


        // Set the page directory
        return "$projectPath/$pagePath";
    }




    // ACTIONS

    // Add a new page
    public function addNew(
    	int $project_ID = 0, // The project_ID that new page is belong to
    	string $page_url,
    	string $page_name = '',
    	array $page_shares = array(), // Array of users that needs to be shared to
    	int $category_ID = 0, // The category_ID that new page is belong to
    	int $order_number = 0 // The order number
    ) {
	    global $db;



		// More DB Checks of arguments !!!



		// Create a project
		if ($project_ID == 0) {

			$project_ID = Project::ID()->addNew(
				$project_name,
				$project_shares,
				$project_category_ID,
				$project_order_number
			);

		}



		// If no name added, try finding page name from URL
		if ($page_name == '') {
			$pathes = explode('/', trim(parse_url($page_url)['path'], '/'));
			$page_name = ucwords(str_replace('-', ' ', end($pathes)));
		}

		if ( $page_name == '' && is_array($pathes) && (count($pathes) == 0 || count($pathes) == 1) ) {
			$page_name = 'Home';
		}

		if ($page_name == '')
			$page_name = 'Untitled';



		// Add the page
		$page_ID = $db->insert('pages', array(
			"page_url" => $page_url,
			"project_ID" => $project_ID,
			"page_name" => $page_name,
			"user_ID" => currentUserID()
		));



		// SHARE - Use share API later !!!
		if ( count($page_shares) > 0 ) {

			foreach ($page_shares as $user_ID) {

				$share_ID = $db->insert('shares', array(
					"share_type" => 'page',
					"shared_object_ID" => $page_ID,
					"share_to" => $user_ID,
					"sharer_user_ID" => currentUserID()
				));

			}

		}



		// CATEGORIZE
		if ($category_ID != "0") {

			$cat_ID = $db->insert('page_cat_connect', array(
				"page_cat_page_ID" => $page_ID,
				"page_cat_ID" => $category_ID,
				"page_cat_connect_user_ID" => currentUserID()
			));

		}



		// ORDER
		if ($order_number != "0") {

			$sort_ID = $db->insert('sorting', array(
				"sort_type" => 'page',
				"sort_object_ID" => $page_ID,
				"sort_number" => $order_number,
				"sorter_user_ID" => currentUserID()
			));

		}


		return $page_ID;

	}


    // Archive a page
    public function archive() {
	    global $db;


		// Delete the old record
		$db->where('archive_type', 'page');
		$db->where('archived_object_ID', self::$page_ID);
		$db->where('archiver_user_ID', currentUserID());
		$db->delete('archives');


		// Add the new record
		$archive_ID = $db->insert('archives', array(
			"archive_type" => 'page',
			"archived_object_ID" => self::$page_ID,
			"archiver_user_ID" => currentUserID()
		));


		return $archive_ID;

    }


    // Delete a page
    public function delete() {
	    global $db;


		// Delete the old record
		$db->where('delete_type', 'page');
		$db->where('deleted_object_ID', self::$page_ID);
		$db->where('deleter_user_ID', currentUserID());
		$db->delete('deletes');


		// Add the new record
		$delete_ID = $db->insert('deletes', array(
			"delete_type" => 'page',
			"deleted_object_ID" => self::$page_ID,
			"deleter_user_ID" => currentUserID()
		));


		return $delete_ID;

    }


    // Recover a page
    public function recover() {
	    global $db;


		// Remove from archives
		$db->where('archive_type', 'page');
		$db->where('archived_object_ID', self::$page_ID);
		$db->where('archiver_user_ID', currentUserID());
		$arc_recovered = $db->delete('archives');


		// Remove from deletes
		$db->where('delete_type', 'page');
		$db->where('deleted_object_ID', self::$page_ID);
		$db->where('deleter_user_ID', currentUserID());
		$del_recovered = $db->delete('deletes');


		return !$arc_recovered && !$del_recovered ? false : true;

    }


    // Remove a page
    public function remove() {
	    global $db;


	    	$pageInfo = $this->getInfo('user_ID, project_ID', true);


			// Get the page info
	    	$page_user_ID = $pageInfo['user_ID'];
	    	$project_ID = $pageInfo['project_ID'];
	    	$iamowner = $page_user_ID == currentUserID() ? true : false;



			// ARCHIVE & DELETE REMOVAL
			$this->recover();



			// SORTING REMOVAL
			$db->where('sort_type', 'page');
			$db->where('sort_object_ID', self::$page_ID);
			if (!$iamowner) $db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');



			// SHARE REMOVAL
			$db->where('share_type', 'page');
			$db->where('shared_object_ID', self::$page_ID);
			if (!$iamowner) $db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
			$db->delete('shares');



			// PAGE REMOVAL
			$db->where('page_ID', self::$page_ID);
			if (!$iamowner) $db->where('user_ID', currentUserID());
			$page_removed = $db->delete('pages');



			// Delete the page folder
			if ($iamowner) deleteDirectory( cache."/projects/project-$project_ID/page-".self::$page_ID."/" );


		return $page_removed;

    }


    // Rename a page
    public function rename(
	    string $text
    ) {
	    global $db;


    	$db->where('page_ID', self::$page_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my page?

		$updated = $db->update('pages', array(
			'page_name' => $text
		));

		return $updated;

    }

}