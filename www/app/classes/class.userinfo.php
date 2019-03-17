<?php

class User {


	// The user ID
	public static $user_ID;
	public static $userInfo;




	// SETTERS:
	public function __construct() {

    }



	// ID Setter
    public static function ID($user_ID = null) {
	    global $db;


		if ($user_ID == null)
			$user_ID = currentUserID();


	    // Set the user ID
		if ($user_ID != null && is_numeric($user_ID)) {


			// Bring the user level info
			$db->join("user_levels l", "l.user_level_ID = u.user_level_ID", "LEFT");
		    $db->where("u.user_ID", $user_ID);
			$userInfo = $db->getOne("users u");

			if ( $userInfo ) {

				self::$user_ID = intval($user_ID);
				self::$userInfo = $userInfo;
				return new static;

			}


		}


	    // For the email users
		if (filter_var($user_ID, FILTER_VALIDATE_EMAIL)) {

			self::$user_ID = $user_ID;
			return new static;

		}


	    // For the new user
		if ($user_ID == "new" || $user_ID == 0) {

			self::$user_ID = "new";
			self::$userInfo = false;
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get the user info
    public function getInfo($column = null) {


		// If email is given
		if ( !is_numeric(self::$user_ID) ) return false;


		return $column == null ? self::$userInfo : self::$userInfo[$column];
    }



    // Get the user data
    public function getData() {

		// Get from DB
		$userInfo = self::$userInfo;
		if (is_numeric(self::$user_ID) && !$userInfo) return false;


		// If email user
		if ( !is_numeric(self::$user_ID) ) return array(
			'userName' => "",
			'firstName' => "",
			'lastName' => "",
			'fullName' => self::$user_ID,
			'nameAbbr' => '<i class="fa fa-envelope"></i>',
			'email' => 'Not confirmed yet',
			'userPic' => "",
			'userPicUrl' => null,
			'printPicture' => "",
			'userLevelName' => "",
			'userLevelID' => ""
		);



		// Prepare the data
		$userData = array(
			'userName' => $userInfo['user_name'],
			'firstName' => $userInfo['user_first_name'],
			'lastName' => $userInfo['user_last_name'],
			'fullName' => $userInfo['user_first_name']." ".$userInfo['user_last_name'],
			'nameAbbr' => mb_substr($userInfo['user_first_name'], 0, 1).mb_substr($userInfo['user_last_name'], 0, 1),
			'email' => $userInfo['user_email'],
			'userPic' => $userInfo['user_picture'],
			'userPicUrl' => $userInfo['user_picture'] != "" ? cache_url('users/user-'.self::$user_ID.'/'.$userInfo['user_picture']) : get_gravatar($userInfo['user_email'], 250),
			'userLevelName' => $userInfo['user_level_name'],
			'userLevelID' => $userInfo['user_level_ID']
		);
		$userData['printPicture'] = 'style="background-image: url('.$userData['userPicUrl'].');"';


		return $userData;
    }



    // Get the categories of this user
    public function getCategories($type = "project", $order = "", $project_ID = null) {
		global $db;


		// Exclude other users
		if ($type == "project") {

			$db->where('cat.user_ID', currentUserID());

		}

		if ($type == "page") {

			$db->where('cat.project_ID', $project_ID);

		}


		// Default order
		if ($order == "") $db->orderBy("cat.cat_order_number", "asc");


		// Order Categories
		if ($order == "name" || $order == "date") $db->orderBy("cat.cat_name", "asc");


		$categories = $db->get($type."s_categories cat", null, '');


		// Add the uncategorized item
		array_unshift($categories , array(
			'cat_ID' => 0,
			'cat_name' => 'Uncategorized',
			'cat_order_number' => 0,
			'theData' => array()
		));


	    return $categories;
    }



    // Get notifications
    public function getNotifications($offset = 0, $limit = 10) {
		global $db;


		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->orderBy("notification_time", "DESC");
		$notifications = $db->withTotalCount()->get("notifications n", array($offset, $limit));


	    return array(
		    'notifications' => $notifications,
		    'totalCount' => $db->totalCount
	    );
    }



    // Get new notifications
    public function getNewNotifications() {
		global $db;


		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->where('con.notification_read', 0);
		$db->orderBy("notification_time", "DESC");
		$notifications = $db->withTotalCount()->get("notifications n");


	    return array(
		    'notifications' => $notifications,
		    'totalCount' => $db->totalCount
	    );
    }



	// Bring data that's mine or shared to me
    public function getMy($data_type = "projects", $catFilter = "", $order = "", $project_ID = null, $object_ID = null, $deletes_archives = false) {
		global $db, $mySharedPages;


		// Correct the data type
		$data_type = substr($data_type, 0, -1);



		// Bring the shared projects
		if ($data_type == "project") {

			$mySharedPages = $this->getMy('pages', "", "", null, null, true);
			$mySharedProjectsFromPages = array_unique(array_column($mySharedPages, 'project_ID'));

		}


		if ($data_type == "project" || $data_type == "page") {

			// Bring the shared ones
			$db->join("shares s", "p.".$data_type."_ID = s.shared_object_ID", "LEFT");
			$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
			$db->joinWhere("shares s", "s.share_type", $data_type);

		}




		// PROJECT EXCEPTIONS
		if ($data_type == "project") {



			// Bring the category connection
			$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_ID", "LEFT");


			// Bring the category info
			$db->join("projects_categories cat", "cat_connect.cat_ID = cat.cat_ID", "LEFT");
			$db->joinWhere("projects_categories cat", "cat.user_ID", self::$user_ID);



			// Bring the order info
			$db->join("projects_order o", "o.project_ID = p.project_ID", "LEFT");
			$db->joinWhere("projects_order o", "o.user_ID", currentUserID());



			// If shared pages exist
			$find_in = "";
			if ( count($mySharedProjectsFromPages) > 0 ) {

				$project_IDs = join("','", $mySharedProjectsFromPages);
				$find_in = "OR p.project_ID IN ('$project_IDs')";

			}


			$db->where('(
				p.user_ID = '.self::$user_ID.'
				OR s.share_to = '.self::$user_ID.'
				OR s.share_to = "'.self::$userInfo['user_email'].'"
				'.$find_in.'
			)');


		}


		// PAGE EXCEPTIONS
		if ($data_type == "page") {


			// Bring the category info
			$db->join("pages_categories cat", "cat.cat_ID = p.cat_ID", "LEFT");
			//$db->joinWhere("pages_categories cat", "cat.project_ID", "pr.project_ID");


			// Bring the project info
			$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");


			// Bring project share info
			$db->join("shares sp", "p.project_ID = sp.shared_object_ID", "LEFT");
			$db->joinWhere("shares sp", "sp.share_type", 'project');


			// Check access if not admin
			if (getUserInfo()['userLevelID'] != 1) {

				$db->where('(
					p.user_ID = '.self::$user_ID.'
					OR s.share_to = '.self::$user_ID.'
					OR s.share_to = "'.self::$userInfo['user_email'].'"
					OR pr.user_ID = '.self::$user_ID.'
					OR sp.share_to = '.self::$user_ID.'
					OR sp.share_to = "'.self::$userInfo['user_email'].'"
				)');

			}


			// Exclude the other project pages
			if ($project_ID) $db->where('p.project_ID', $project_ID);

		}



		// Mine and Shared Filters
		if ($catFilter == "mine")
			$db->where('p.user_ID = '.self::$user_ID);

		elseif ($catFilter == "shared")
			$db->where('p.user_ID != '.self::$user_ID);



		if (!$deletes_archives) {

			// Exclude deleted and archived
			$db->where("p.".$data_type."_deleted", ($catFilter == "deleted" ? 1 : 0));
			if ($catFilter != "deleted")
				$db->where("p.".$data_type."_archived", ($catFilter == "archived" ? 1 : 0));

		}


		// Default Sorting
		if ($order == "") $db->orderBy("order_number", "asc");
		if ($order == "") $db->orderBy("s.share_ID", "desc");
		$db->orderBy("cat.cat_name", "asc");
		$db->orderBy("p.".$data_type."_name", "asc");


		// Order Projects
		if ($order == "name") $db->orderBy("p.".$data_type."_name", "asc");
		if ($order == "date") $db->orderBy("p.".$data_type."_created", "asc");


		if ($object_ID) {

			$db->where('p.'.$data_type.'_ID', $object_ID);

			return $db->getOne(
				$data_type.'s p',
				null,
				'
					*,
					p.user_ID as user_ID,
					p.'.$data_type.'_ID as '.$data_type.'_ID,
					s.share_to as share_to,
					s.sharer_user_ID as sharer_user_ID
				'
			);

		}


		return $db->get(
			$data_type.'s p',
			null,
			'
				*,
				p.user_ID as user_ID,
				p.'.$data_type.'_ID as '.$data_type.'_ID,
				s.share_to as share_to,
				s.sharer_user_ID as sharer_user_ID
			'
		);

    }



    // Get screen data
    public function getScreenData() {
	    global $db;


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");

		$db->where('s.screen_user_ID', 1); // !!! ?

		$db->orderBy('s_cat.screen_cat_order', 'asc');
		$db->orderBy(' s.screen_order', 'asc');
		$screens = $db->get('screens s');


		// Prepare the screens data
		$screen_data = [];
		foreach ($screens as $screen) {

			if ( !isset($screen_data[$screen['screen_cat_ID']]['screens']) ) {

				$screen_data[$screen['screen_cat_ID']] = array(
					'screen_cat_icon' => $screen['screen_cat_icon'],
					'screen_cat_name' => $screen['screen_cat_name'],
					'screens' => array(),
				);

			}

			$screen_data[$screen['screen_cat_ID']]['screens'][$screen["screen_ID"]] = $screen;

		}


		return $screen_data;
    }



    // Can access?
    public function canAccess(
	    int $object_ID,
	    string $object_type
    ) {


	    // Check the object types
	    if (
		    $object_type != "project"
		    && $object_type != "page"
		    && $object_type != "device"
		    && $object_type != "pin"
	    ) return false;



	    // Capitalize for the class names
	    $object_type = ucfirst($object_type);



		if (class_exists($object_type)) {

			$typeApi = $object_type::ID($object_ID);

			if (method_exists($typeApi, 'getUsers') && $typeApi) {


				// Do the action
				$users = $typeApi->getUsers(true);


			} else return false;

		} else return false;



	    // Check whether or not user can access
	    return in_array(self::$user_ID, $users);

    }




    // ACTIONS:

    // Add new user
    public function addNew(
	    string $user_email,
	    string $user_full_name,
	    string $user_password,
	    string $user_name = null,
	    int $user_level_ID = 2
    ) {
	    global $db, $log;


		// Parse the full name
		$firstName = $user_full_name;
		$lastName = "";
		$parsedFullName = explode(' ', $user_full_name);
		if (count($parsedFullName) > 1) {
			$firstName = str_replace(' '.end($parsedFullName), '', $user_full_name);
			$lastName = end($parsedFullName);
		}


		// Create the username
		if ($user_name == null) $user_name = permalink($user_full_name);


		// Add to the CB
		$user_ID = $db->insert('users', array(
			'user_name' => $user_name,
			'user_email' => $user_email,
			'user_first_name' => $firstName,
			'user_last_name' => $lastName,
			'user_password' => password_hash($user_password, PASSWORD_DEFAULT),
			'user_IP' => get_client_ip(),
			'user_level_ID' => $user_level_ID // Free one
		));


		// If successful
		if ($user_ID) {


			// Site log
			$log->info("User #$user_ID Added: $user_name($user_full_name) | Email: $user_email | User Level ID #$user_level_ID");


			// Send a welcome email
			Notify::ID($user_ID)->mail(
				'Welcome to Revisionary App!',
				"Hi $firstName, <br><br>

				Thanks for joining us. You can now start revising websites. :) <br><br>

				<a href='".site_url()."' target='_blank'>".site_url()."</a>"
			);


			// Notify the admin
			Notify::ID(1)->mail(
				"New user registration by $user_full_name",
				"
				<b>User Information</b> <br>
				E-Mail: $user_email <br>
				Full Name: $user_full_name <br>
				Username: $user_name
				"
			);

		}


		// Return the user ID
		return $user_ID;
    }



    // Edit a page
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!!



		// Update the page
		$db->where('user_ID', self::$user_ID);
		$user_updated = $db->update('users', array($column => $new_value));


		// Site log
		if ($user_updated) $log->info("User #".self::$user_ID." Updated: '$column => $new_value'");


		return $user_updated;
    }


    // Reorder
    public function reorder(
	    array $orderData
    ) {
	    global $db;




		$status = "initated";
		foreach($orderData as $data) {



			// Security Check !!! Needs more: ID, Cat ID check from DB. Order number check?
			if (
				(
					$data['type'] != "projectcategory" &&
					$data['type'] != "pagecategory" &&
					$data['type'] != "project" &&
					$data['type'] != "page"
				) ||
				!is_numeric( intval($data['ID']) ) ||
				!is_numeric( intval($data['catID']) ) ||
				!is_numeric( intval($data['order']) )
			) {
				return false;
			}



			// Pass on category 0
			if ($data['type'] == "projectcategory" && intval($data['ID']) == 0) continue;
			if ($data['type'] == "pagecategory" && intval($data['ID']) == 0) continue;




			// DB Checks !!! If exists...




			if ($data['type'] == "project") {



				// ORDERING:

				// Delete the old record
				$db->where('project_ID', $data['ID']);
				$db->where('user_ID', currentUserID());
				$db->delete('projects_order');


				// Add the new record
				$ordered = $db->insert('projects_order', array(
					"order_number" => $data['order'],
					"project_ID" => $data['ID'],
					"user_ID" => currentUserID()
				));
				if ($ordered) $status = "ordering-successful";



				// CATEGORIZING:

				// Delete the old connection
				$db->where('project_ID', $data['ID']);
				$db->where('user_ID', currentUserID());
				$db->delete('project_cat_connect');


				if ($data['catID'] != 0) {


					// Add the new record
					$categorized = $db->insert('project_cat_connect', array(
						"cat_ID" => $data['catID'],
						"project_ID" => $data['ID'],
						"user_ID" => currentUserID()
					));
					if ($categorized) $status = "categorizing-successful";


				}



			}




			if ($data['type'] == "page") {



				// ORDERING:
				$ordered = Page::ID($data['ID'])->edit('order_number', $data['order']);
				if ($ordered) $status = "ordering-successful";



				// CATEGORIZING
				$categorized = Page::ID($data['ID'])->edit('cat_ID', ($data['catID'] == 0 ? null : $data['catID']));
				if ($categorized) $status = "categorizing-successful";



			}


			// CATEGORY ORDER
			if ($data['type'] == "projectcategory" || $data['type'] == "pagecategory") {


				$cat_ordered = $data['type']::ID($data['ID'])->reorder($data['order']);
				if ($cat_ordered) $status = "cat-ordering-successful";


			}



		} // Loop


		return $status == "ordering-successful" || $status == "categorizing-successful" || $status == "cat-ordering-successful";

    }


    // Unshare
    public function unshare(
	    string $share_type,
	    int $shared_object_ID
    ) {
	    global $db, $log;


	    // Check the ownership
	    $objectData = ucfirst($share_type)::ID($shared_object_ID);
	    $object_user_ID = $objectData->getInfo('user_ID');
	    $objectName = $objectData->getInfo($share_type.'_name');
	    $iamowner = $object_user_ID == currentUserID();
	    $iamshared = self::$user_ID == currentUserID();


		// Remove share from DB
		$db->where('share_type', $share_type);
		$db->where('shared_object_ID', $shared_object_ID);
		$db->where('share_to', self::$user_ID);

		if (!$iamowner && !$iamshared) $db->where('sharer_user_ID', currentUserID());


		$unshared = $db->delete('shares');


		// Site log
		if ($unshared) {


			// Notify User via web notification
			if ( is_integer(self::$user_ID) )
				Notify::ID(self::$user_ID)->web("unshare", $share_type, $shared_object_ID);


			// Notify User via email notification
			Notify::ID(self::$user_ID)->mail(
				getUserInfo()['fullName']." unshared the \"$objectName\" $share_type from you.",

				"Hello, ".
				getUserInfo()['fullName']." unshared the \"$objectName\" $share_type from you on Revisionary App."
			);


			$log->info("User #".self::$user_ID." Unshared: $share_type #$shared_object_ID | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");

		}



		return $unshared;

    }


    // Change Share Access
    public function changeshareaccess(
	    string $share_type,
	    int $shared_object_ID,
	    int $new_shared_object_ID = null
    ) {
	    global $db, $log;


	    // Check the ownership
	    $objectInfo = ucfirst($share_type)::ID($shared_object_ID)->getInfo();
	    $object_user_ID = $objectInfo['user_ID'];
	    $iamowner = $object_user_ID == currentUserID() ? true : false;



		// Remove share from DB
		$db->where('share_type', $share_type);
		$db->where('shared_object_ID', $shared_object_ID);
		$db->where('share_to', self::$user_ID);
		if (!$iamowner) $db->where('sharer_user_ID', currentUserID());



		// Page to project
	    if ($share_type == "page") {

		    // Find the project info
			$new_shared_object_ID = $objectInfo['project_ID'];
			$new_share_type = "project";

	    }


		// Project to page
	    if ($share_type == "project") {

		    // Find the project info
			$new_share_type = "page";

	    }


	    $changed = $db->update('shares', array(
			'share_type' => $new_share_type,
			'shared_object_ID' => $new_shared_object_ID
		));


		// Site log
		if ($changed) $log->info("User #".self::$user_ID." Share Access Changed: '$share_type => $new_share_type' | '#$shared_object_ID => #$new_shared_object_ID' | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");



		return $changed;

    }


    // Make owner
    public function makeownerof(
	    string $data_type,
	    int $object_ID
    ) {
		global $db, $log;


		// Is object exist
		$object = $data_type::ID($object_ID);
		if (!$object) return false;


		// Remove me from the shares
		$db->where('share_type', $data_type);
		$db->where('shared_object_ID', $object_ID);
		$db->where('share_to', self::$user_ID);
		$deleted = $db->delete('shares');


		// Make me owner
		$made_owner = false;
		if ($deleted) $made_owner = $object->changeownership(self::$user_ID);


		// Site log
		if ($made_owner) $log->info("User #".self::$user_ID." has became owner of: ".ucfirst($data_type)." #$object_ID | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");



		return $made_owner;
    }


}