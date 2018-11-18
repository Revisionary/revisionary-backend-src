<?php

class Project {


	// The project ID
	public static $project_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($project_ID = null) {

	    // Set the project ID
		if ($project_ID != null) self::$project_ID = $project_ID;
		return new static;

    }




	// GETTERS:

    // Get project info
    public function getInfo($columns = null, $array = false) {
	    global $db;

	    $db->where('project_ID', self::$project_ID);

	    return $array ? $db->getOne("projects", $columns) : $db->getValue("projects", $columns);
    }


    // Get project directory
    public function getDir() {

		// Paths
        $userPath = "user-".$this->getInfo('user_ID');
        $projectPath = "project-".self::$project_ID;


        // Set the project directory
        return dir."/assets/cache/".$userPath."/".$projectPath;
    }




    // ACTIONS

    // Add a new project
    public function addNew(
    	string $project_name,
    	int $category_ID = 0, // The category_ID that new page is belong to
    	int $order_number = 0, // The order number
    	array $project_shares = array() // Array of users that needs to be shared to
    ) {
	    global $db;


    	// Security check !!!
		if ($project_name == "") return false;



		// More DB Checks of arguments !!!



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

}