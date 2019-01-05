<?php

class User {


	// The user ID
	public static $user_ID;




	// SETTERS:
	public function __construct() {

    }



	// ID Setter
    public static function ID($user_ID = null) { // UPDATE HERE

		if ($user_ID == null)
			$user_ID = currentUserID();



	    // Set the user ID
		self::$user_ID = $user_ID;
		return new static;

    }




	// GETTERS:

    // Get the user info
    public function getInfo($columns = null, $array = false) {
	    global $db;


		// If email is given
		if ( !is_numeric(self::$user_ID) ) return false;


	    $db->where("user_ID", self::$user_ID);

		return $array ? $db->getOne("users", $columns) : $db->getValue("users", $columns);
    }



    // Get the user data
    public function getData() {

		// Get from DB
		$userInfo = $this->getInfo(null, true);
		if (is_numeric(self::$user_ID) && !$userInfo) return false;


		// Prepare the data
		$userData = array(
			'userName' => $userInfo['user_name'],
			'firstName' => $userInfo['user_first_name'],
			'lastName' => $userInfo['user_last_name'],
			'fullName' => !is_numeric(self::$user_ID) ? self::$user_ID : $userInfo['user_first_name']." ".$userInfo['user_last_name'],
			'nameAbbr' => !is_numeric(self::$user_ID) ? '<i class="fa fa-envelope"></i>' : mb_substr($userInfo['user_first_name'], 0, 1).mb_substr($userInfo['user_last_name'], 0, 1),
			'email' => !is_numeric(self::$user_ID) ? 'Not confirmed yet' : $userInfo['user_email'],
			'userPic' => $userInfo['user_picture'],
			'userPicUrl' => $userInfo['user_picture'] != "" ? cache_url('users/user-'.self::$user_ID.'/'.$userInfo['user_picture']) : null
		);
		$userData['printPicture'] = $userInfo['user_picture'] != "" ? 'style="background-image: url('.$userData['userPicUrl'].');"' : false;


		return $userData;
    }



    // Get the categories of this user
    public function getCategories($type = "project", $order = "", $project_ID = null) {
		global $db;


		// Exclude other category types
		$db->where('cat_type', ($type == "page" ? $project_ID : $type));

		// Exclude other users
		$db->where('cat_user_ID', self::$user_ID);


		// Bring the order info
		$db->join("sorting oc", "cat.cat_ID = oc.sort_object_ID", "LEFT");
		$db->joinWhere("sorting oc", "oc.sort_type", "category");
		$db->joinWhere("sorting oc", "oc.sorter_user_ID", self::$user_ID);


		// Default order
		if ($order == "") $db->orderBy("oc.sort_number", "asc");


		// Order Categories
		if ($order == "name") $db->orderBy("cat_name", "asc");
		if ($order == "date") $db->orderBy("cat_name", "asc");


		$categories = $db->get('categories cat', null, '');


		// Add the uncategorized item
		array_unshift($categories , array(
			'cat_ID' => 0,
			'cat_name' => 'Uncategorized',
			'sort_number' => 0,
			'theData' => array()
		));


	    return $categories;
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


		// Bring the shared ones
		$db->join("shares s", "p.".$data_type."_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".getUserInfo()['email']."')");
		$db->joinWhere("shares s", "s.share_type", $data_type);


		// Bring the category connection
		$db->join($data_type."_cat_connect cat_connect", "p.".$data_type."_ID = cat_connect.".$data_type."_cat_".$data_type."_ID", "LEFT");
		$db->joinWhere($data_type."_cat_connect cat_connect", "cat_connect.".$data_type."_cat_connect_user_ID", self::$user_ID);


		// Bring the category info
		$db->join("categories cat", "cat_connect.".$data_type."_cat_ID = cat.cat_ID", "LEFT");
		$db->joinWhere("categories cat", "cat.cat_user_ID", self::$user_ID);


		// Bring the archive info
		$db->join("archives arc", "arc.archived_object_ID = p.".$data_type."_ID", "LEFT");
		$db->joinWhere("archives arc", "arc.archiver_user_ID", self::$user_ID);
		$db->joinWhere("archives arc", "arc.archive_type", $data_type);


		// Bring the delete info
		$db->join("deletes del", "del.deleted_object_ID = p.".$data_type."_ID", "LEFT");
		$db->joinWhere("deletes del", "del.deleter_user_ID", self::$user_ID);
		$db->joinWhere("deletes del", "del.delete_type", $data_type);



		// PROJECT EXCEPTIONS
		if ($data_type == "project") {


			// If shared pages exist
			$find_in = "";
			if ( count($mySharedProjectsFromPages) > 0 ) {

				$project_IDs = join("','", $mySharedProjectsFromPages);
				$find_in = "OR p.project_ID IN ('$project_IDs')";

			}


			$db->where('(
				p.user_ID = '.self::$user_ID.'
				OR s.share_to = '.self::$user_ID.'
				OR s.share_to = "'.getUserInfo()['email'].'"
				'.$find_in.'
			)');


		}


		// PAGE EXCEPTIONS
		if ($data_type == "page") {


			// Bring the project info
			$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");


			// Bring project share info
			$db->join("shares sp", "p.project_ID = sp.shared_object_ID", "LEFT");
			$db->joinWhere("shares sp", "sp.share_type", 'project');


			$db->where('(
				p.user_ID = '.self::$user_ID.'
				OR s.share_to = '.self::$user_ID.'
				OR s.share_to = "'.getUserInfo()['email'].'"
				OR pr.user_ID = '.self::$user_ID.'
				OR sp.share_to = '.self::$user_ID.'
				OR sp.share_to = "'.getUserInfo()['email'].'"
			)');


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
			$db->where('del.deleted_object_ID IS '.($catFilter == "deleted" ? 'NOT' : '').' NULL');
			if ($catFilter != "deleted")
				$db->where('arc.archived_object_ID IS '.($catFilter == "archived" ? 'NOT' : '').' NULL');

		}


		// Bring the category order info
		$db->join("sorting oc", "cat.cat_ID = oc.sort_object_ID", "LEFT");
		$db->joinWhere("sorting oc", "oc.sort_type", "category");
		$db->joinWhere("sorting oc", "oc.sorter_user_ID", self::$user_ID);


		// Bring the order info
		$db->join("sorting o", "p.".$data_type."_ID = o.sort_object_ID", "LEFT");
		$db->joinWhere("sorting o", "o.sort_type", $data_type);
		$db->joinWhere("sorting o", "o.sorter_user_ID", self::$user_ID);


		// Default Sorting
		if ($order == "") $db->orderBy("o.sort_number", "asc");
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
					oc.sort_ID as cat_sort_ID,
					oc.sort_type as cat_sort_type,
					oc.sort_object_ID as cat_sort_object_ID,
					oc.sort_number as cat_sort_number,
					oc.sorter_user_ID as cat_sorter_user_ID
				'
			);

		}


		return $db->get(
			$data_type.'s p',
			null,
			'
				*,
				p.user_ID as user_ID,
				oc.sort_ID as cat_sort_ID,
				oc.sort_type as cat_sort_type,
				oc.sort_object_ID as cat_sort_object_ID,
				oc.sort_number as cat_sort_number,
				oc.sorter_user_ID as cat_sorter_user_ID
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




    // ACTIONS:

    // Add new user
    public function addNew(
	    string $user_email,
	    string $user_full_name,
	    string $user_password,
	    string $user_name = null,
	    int $user_level_ID = 2
    ) {
	    global $db;


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
			'user_level_ID' => $user_level_ID // Free one
		));


		// Send a welcome email
		Notify::ID($user_ID)->mail(
			'Welcome to Revisionary App!',
			"Hi $firstName, <br><br> Thanks for joining us. You can now start revising websites. :)"
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


		// Return the user ID
		return $user_ID;
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
					$data['type'] != "category" &&
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
			if ($data['type'] == "category" && intval($data['ID']) == 0) continue;



			// DB Checks !!! If exists...



			// Delete the old record
			$db->where('sort_type', $data['type']);
			$db->where('sort_object_ID', $data['ID']);
			$db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');


			// Add the new record
			$dbData = Array (
				"sort_type" => $data['type'],
				"sort_object_ID" => $data['ID'],
				"sort_number" => $data['order'],
				"sorter_user_ID" => currentUserID()
			);
			$sort_ID = $db->insert('sorting', $dbData);

			if ($sort_ID) {
				$status = "ordering-successful";


				if ($data['type'] == "page" || $data['type'] == "project") {

					// Delete the old record
					$db->where($data['type'].'_cat_'.$data['type'].'_ID', $data['ID']);
					$db->where($data['type'].'_cat_connect_user_ID', currentUserID());
					$db->delete($data['type'].'_cat_connect');


					// Add the new record
					$id_connect = $db->insert($data['type'].'_cat_connect', array(
						$data['type']."_cat_".$data['type']."_ID" => $data['ID'],
						$data['type']."_cat_ID" => $data['catID'],
						$data['type']."_cat_connect_user_ID" => currentUserID()
					));
					if ($id_connect) $status = "category-successful";


				}

			}




		} // Loop


		return $status == "ordering-successful" || $status == "category-successful";

    }


    // Unshare
    public function unshare(
	    string $share_type,
	    int $shared_object_ID
    ) {
	    global $db;


	    // Check the ownership
	    $object_user_ID = ucfirst($share_type)::ID($shared_object_ID)->getInfo('user_ID');
	    $iamowner = $object_user_ID == currentUserID() ? true : false;


		// Remove share from DB
		$db->where('share_type', $share_type);
		$db->where('shared_object_ID', $shared_object_ID);
		$db->where('share_to', self::$user_ID);

		if (!$iamowner) $db->where('sharer_user_ID', currentUserID());

		return $db->delete('shares');

    }


}