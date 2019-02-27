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
		if ($project_ID == null || $project_ID == "new") {

			self::$project_ID = "new";
			return new static;

		}


	    // Autodetect project ID when adding new
		if ($project_ID == "autodetect") {

			self::$project_ID = "autodetect";
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


	// Get page users
	public function getUsers($include_me = false) {
		global $db;


		$users = array();


		// Get the project owner
		$users[] = $this->getInfo('user_ID');

		// Get the shared people of the project
		$db->where('share_type', 'project');
		$db->where('shared_object_ID', self::$project_ID);
		$db->where("share_to REGEXP '^[0-9]+$'");
		$shared_IDs = array_column($db->get('shares', null, 'share_to'), 'share_to');
		$users = array_merge($users, $shared_IDs);


		// Remove duplicates
		$users = array_unique($users);


		// Exclude myself
		if ( !$include_me && ($user_key = array_search(currentUserID(), $users)) !== false ) {
		    unset($users[$user_key]);
		}


		return $users;

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
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Add the domain name as project name if not already entered
		if ($project_name == "" && $page_url != "") {

			$parsedURL = parseUrl($page_url);

			$project_name = ucwords( str_replace('-', ' ', explode('.', $parsedURL['domain'])[0]) );

			if ( $parsedURL['subdomain'] != "" && $parsedURL['subdomain'] != "www" && $project_name != "" ) {

				$project_name = ucfirst($parsedURL['subdomain'])." $project_name";

			}

		}



		// If still empty
		if ($project_name == "") $project_name = "Untitled";



		// Auto detect the existing project_ID if the URL entered
		if (self::$project_ID == "autodetect") {

			// Page domain
			$page_domain = parseUrl($page_url)['full_host'];

			$db->where('user_ID', currentUserID());
			$db->where('page_url', "$page_domain%", 'like');
			$pages_match = $db->get('pages', null, 'page_url, project_ID');
			$possible_project_IDs = array_unique(array_column($pages_match, 'project_ID'));


			// Make it project id if the result has 1 record
			if ( count($possible_project_IDs) == 1 ) return reset($possible_project_IDs);
			//die_to_print( reset($possible_project_IDs) );

		}



		// Add the project
		$project_ID = $db->insert('projects', array(
			"project_name" => $project_name,
			"user_ID" => currentUserID()
		));



		// If the project added
		if ($project_ID) {



			// Get the already shared users
			$users = $this->getUsers();



			// SHARE - Use share API later !!!
			if ( count($project_shares) > 0 ) {

				foreach ($project_shares as $user_ID) {


					// Don't add the user to the shares if already shared
					if ( in_array($user_ID, $users) ) continue;


					// Share
					$share_ID = $db->insert('shares', array(
						"share_type" => 'project',
						"shared_object_ID" => $project_ID,
						"share_to" => $user_ID,
						"sharer_user_ID" => currentUserID()
					));


					// Email notification
					Notify::ID($user_ID)->mail(
						getUserInfo()['fullName']." shared the \"$project_name\" project with you.",

						"Hello, ".getUserInfo()['fullName']."(".getUserInfo()['userName'].") shared the \"$project_name\" project with you from Revisionary App. Here is the link to access this project: <br>

						<a href='".site_url('project/'.$project_ID)."' target='_blank'>".site_url('project/'.$project_ID)."</a>"
					);

				}

			}



			// Site log
			$log->info("Project #$project_ID Added: $project_name($page_url) | User #".currentUserID());



			// CATEGORIZE
			if ($category_ID != "0") {

				$cat_ID = $db->insert('project_cat_connect', array(
					"cat_ID" => $category_ID,
					"project_ID" => $project_ID,
					"user_ID" => currentUserID()
				));

			}



			// ORDER
			if ($order_number != "0") {

				$order_ID = $db->insert('projects_order', array(
					"order_number" => $order_number,
					"project_ID" => $project_ID,
					"user_ID" => currentUserID()
				));

			}



		}


		return $project_ID;

	}



    // Edit a project
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Get the project owner
    	$project_user_ID = $this->getInfo('user_ID');
    	$iamowner = $project_user_ID == currentUserID();
    	$iamadmin = getUserInfo()['userLevelID'] == 1;


    	// Return if not admin or owner
    	if (!$iamadmin && !$iamowner) return false;



		// Update the page
		$db->where('project_ID', self::$project_ID);
		$project_updated = $db->update('projects', array($column => $new_value));


		// Site log
		if ($project_updated) $log->info("Project #".self::$project_ID." Updated: '$column => $new_value' | User #".currentUserID());


		return $project_updated;
    }


    // Archive a project
    public function archive() {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Get the project owner
    	$project_user_ID = $this->getInfo('user_ID');
    	$iamowner = $project_user_ID == currentUserID();
    	$iamadmin = getUserInfo()['userLevelID'] == 1;


    	// Return if not admin or owner
    	if (!$iamadmin && !$iamowner) return false;



		$archived = $this->edit("project_archived", 1);


		// Site log
		if ($archived) $log->info("Project #".self::$project_ID." Archived: '".$this->getInfo('project_name')."' | User #".currentUserID());


		return $archived;

    }


    // Delete a project
    public function delete() {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Get the project owner
    	$project_user_ID = $this->getInfo('user_ID');
    	$iamowner = $project_user_ID == currentUserID();
    	$iamadmin = getUserInfo()['userLevelID'] == 1;


    	// Return if not admin or owner
    	if (!$iamadmin && !$iamowner) return false;



		$deleted = $this->edit("project_deleted", 1);


		// Site log
		if ($deleted) $log->info("Project #".self::$project_ID." Deleted: '".$this->getInfo('project_name')."' | User #".currentUserID());


		return $deleted;

    }


    // Recover a project
    public function recover() {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Get the project owner
    	$project_user_ID = $this->getInfo('user_ID');
    	$iamowner = $project_user_ID == currentUserID();
    	$iamadmin = getUserInfo()['userLevelID'] == 1;


    	// Return if not admin or owner
    	if (!$iamadmin && !$iamowner) return false;



		// Remove from archives
		$arc_recovered = $this->edit("project_archived", 0);


		// Remove from deletes
		$del_recovered = $this->edit("project_deleted", 0);



		if ($arc_recovered && $del_recovered) {


			// Site log
			$log->info("Project #".self::$project_ID." Recovered: '".$this->getInfo('project_name')."' | User #".currentUserID());


			return true;
		}


		return false;

    }


    // Remove a project
    public function remove() {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Get the project owner
    	$project_user_ID = $this->getInfo('user_ID');
    	$iamowner = $project_user_ID == currentUserID();
    	$iamadmin = getUserInfo()['userLevelID'] == 1;


    	// Return if not admin or owner
    	if (!$iamadmin && !$iamowner) return false;




		// SHARE REMOVAL
		$db->where('share_type', 'project');
		$db->where('shared_object_ID', self::$project_ID);
		if (!$iamowner && !$iamadmin)
			$db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
		$db->delete('shares');




		// PROJECT REMOVAL
		$db->where('project_ID', self::$project_ID);
		$project_removed = $db->delete('projects');



		// Delete the project folder
		deleteDirectory( cache."/projects/project-".request('id')."/" );


		// Site log
		if ($project_removed) $log->info("Project #".self::$project_ID." Removed: '".$this->getInfo('project_name')."' | User #".currentUserID());


		return $project_removed;

    }


    // Rename a project
    public function rename(
	    string $text
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		$current_project_name = $this->getInfo('project_name');


    	$db->where('project_ID', self::$project_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my project?

		$project_renamed = $db->update('projects', array(
			'project_name' => $text
		));


		// Site log
		if ($project_renamed) $log->info("Project #".self::$project_ID." Renamed: '$current_project_name => $text' | User #".currentUserID());



		return $project_renamed;

    }


    // Change owner
    public function changeownership(
	    int $user_ID
    ) {
		global $db, $log;



		// More DB Checks of arguments !!!



		$old_owner_ID = self::$projectInfo['user_ID'];


		// Share to old owner
		$db->insert('shares', array(

			"share_to" => $old_owner_ID,
			"share_type" => 'project',
			"shared_object_ID" => self::$project_ID,
			"sharer_user_ID" => $user_ID

		));


		$db->where('project_ID', self::$project_ID);
		$db->where('user_ID', currentUserID());
		$ownership_changed = $db->update('projects', array(
			'user_ID' => $user_ID
		));


		// Site log
		if ($ownership_changed) $log->info("Project #".self::$project_ID." Ownership Changed: '$old_owner_ID => $user_ID' | User #".currentUserID());


		return $ownership_changed;

    }

}