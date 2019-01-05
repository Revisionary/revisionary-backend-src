<?php

class Project {


	public static $project_ID;
	public static $projectInfo;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($project_ID = null) {
		global $db;


	    // Set the page ID
		if ($project_ID != null && is_numeric($project_ID)) {


			$db->where('project_ID', $project_ID);
			$projectInfo = $db->getOne("projects");

			if ( $projectInfo ) {

				self::$project_ID = $project_ID;
				self::$projectInfo = $projectInfo;
				return new static;

			}


		}


	    // For the new page
		if ($project_ID == null) {

			self::$project_ID = "new";
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get project info
    public function getInfo($column = null) {

		return $column == null ? self::$projectInfo : self::$projectInfo[$column];

    }


    // Get project directory
    public function getDir() {

		// Paths
        $projectPath = "project-".self::$project_ID;


        // Set the project directory
        return cache."/projects/".$projectPath;
    }




    // ACTIONS

    // Add a new project
    public function addNew(
    	string $project_name = '',
    	array $project_shares = array(), // Array of users that needs to be shared to
    	int $category_ID = 0, // The category_ID that new page is belong to
    	int $order_number = 0, // The order number
		string $page_url = ''
    ) {
	    global $db;



		// More DB Checks of arguments !!!



		// Add the domain name as project name if not already entered
		if ($project_name == "" && $page_url != "")
			$project_name = ucwords( str_replace('-', ' ', explode('.', parseUrl($page_url)['domain'])[0]) );



		// Add the project
		$project_ID = $db->insert('projects', array(
			"project_name" => $project_name,
			"user_ID" => currentUserID()
		));



		// SHARE - Use share API later !!!
		if ( count($project_shares) > 0 ) {

			foreach ($project_shares as $user_ID) {

				$share_ID = $db->insert('shares', array(
					"share_type" => 'project',
					"shared_object_ID" => $project_ID,
					"share_to" => $user_ID,
					"sharer_user_ID" => currentUserID()
				));

			}

		}



		// CATEGORIZE
		if ($category_ID != "0") {

			$cat_ID = $db->insert('project_cat_connect', array(
				"project_cat_project_ID" => $project_ID,
				"project_cat_ID" => $category_ID,
				"project_cat_connect_user_ID" => currentUserID()
			));

		}



		// ORDER
		if ($order_number != "0") {

			$sort_ID = $db->insert('sorting', array(
				"sort_type" => 'project',
				"sort_object_ID" => $project_ID,
				"sort_number" => $order_number,
				"sorter_user_ID" => currentUserID()
			));

		}


		return $project_ID;

	}


    // Archive a project
    public function archive() {
	    global $db;


		// Delete the old record
		$db->where('archive_type', 'project');
		$db->where('archived_object_ID', self::$project_ID);
		$db->where('archiver_user_ID', currentUserID());
		$db->delete('archives');


		// Add the new record
		$archive_ID = $db->insert('archives', array(
			"archive_type" => 'project',
			"archived_object_ID" => self::$project_ID,
			"archiver_user_ID" => currentUserID()
		));


		return $archive_ID;

    }


    // Delete a project
    public function delete() {
	    global $db;


		// Delete the old record
		$db->where('delete_type', 'project');
		$db->where('deleted_object_ID', self::$project_ID);
		$db->where('deleter_user_ID', currentUserID());
		$db->delete('deletes');


		// Add the new record
		$delete_ID = $db->insert('deletes', array(
			"delete_type" => 'project',
			"deleted_object_ID" => self::$project_ID,
			"deleter_user_ID" => currentUserID()
		));


		return $delete_ID;

    }


    // Recover a project
    public function recover() {
	    global $db;


		// Remove from archives
		$db->where('archive_type', 'project');
		$db->where('archived_object_ID', self::$project_ID);
		$db->where('archiver_user_ID', currentUserID());
		$arc_recovered = $db->delete('archives');


		// Remove from deletes
		$db->where('delete_type', 'project');
		$db->where('deleted_object_ID', self::$project_ID);
		$db->where('deleter_user_ID', currentUserID());
		$del_recovered = $db->delete('deletes');


		return !$arc_recovered && !$del_recovered ? false : true;

    }


    // Remove a project
    public function remove() {
	    global $db;


			// Get the project owner
	    	$project_user_ID = $this->getInfo('user_ID');
	    	$iamowner = $project_user_ID == currentUserID() ? true : false;



			// PAGES REMOVAL
			$db->where('project_ID', self::$project_ID);
			if (!$iamowner) $db->where('user_ID', currentUserID());
			$pages = $db->get('pages');


			// Remove all the pages
			foreach ($pages as $page)
				Page::ID($page['page_ID'])->remove();



			// CATEGORY REMOVAL
			$db->where('cat_type', self::$project_ID);
			if (!$iamowner) $db->where('cat_user_ID', currentUserID());
			$categories = $db->get('categories');

			// Remove all the categories
			foreach ($categories as $category)
				Category::ID($category['cat_ID'])->remove();



			// ARCHIVE & DELETE REMOVAL
			$this->recover();



			// SORTING REMOVAL
			$db->where('sort_type', 'project');
			$db->where('sort_object_ID', self::$project_ID);
			if (!$iamowner) $db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');



			// SHARE REMOVAL
			$db->where('share_type', 'project');
			$db->where('shared_object_ID', self::$project_ID);
			if (!$iamowner) $db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
			$db->delete('shares');




			// PROJECT REMOVAL
			$db->where('project_ID', self::$project_ID);
			if (!$iamowner) $db->where('user_ID', currentUserID());
			$project_removed = $db->delete('projects');



			// Delete the project folder
			if ($iamowner) deleteDirectory( cache."/projects/project-".request('id')."/" );


		return $project_removed;

    }


    // Rename a project
    public function rename(
	    string $text
    ) {
	    global $db;


    	$db->where('project_ID', self::$project_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my project?

		$updated = $db->update('projects', array(
			'project_name' => $text
		));

		return $updated;

    }

}