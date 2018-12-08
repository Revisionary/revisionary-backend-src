<?php

class UserAccess {


	// The user ID
	public static $user_ID;




	// SETTERS:
	public function __construct() {

    }



	// ID Setter
    public static function ID($user_ID = null) {

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

	    $db->where("user_ID", self::$user_ID);

		return $array ? $db->getOne("users", $columns) : $db->getValue("users", $columns);
    }



    // Get the user data
    public function getData() {

		// Get from DB
		$userInfo = $this->getInfo(null, true);
		if (!$userInfo) return false;


		// Prepare the data
		$userData = array(
			'userName' => $userInfo['user_name'],
			'firstName' => $userInfo['user_first_name'],
			'lastName' => $userInfo['user_last_name'],
			'fullName' => $userInfo['user_first_name']." ".$userInfo['user_last_name'],
			'nameAbbr' => mb_substr($userInfo['user_first_name'], 0, 1).mb_substr($userInfo['user_last_name'], 0, 1),
			'email' => $userInfo['user_email'],
			'userPic' => $userInfo['user_picture'],
			'userPicUrl' => $userInfo['user_picture'] != "" ? cache_url('user-'.self::$user_ID.'/'.$userInfo['user_picture']) : asset_url('icons/follower-f.svg')
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
		$db->joinWhere("shares s", "s.share_to", self::$user_ID);
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


			// Bring the devices
			$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");


			// Bring the device category info
			$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");


			$db->where('(
				p.user_ID = '.self::$user_ID.'
				OR s.share_to = '.self::$user_ID.'
				OR pr.user_ID = '.self::$user_ID.'
				OR sp.share_to = '.self::$user_ID.'
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



    // Get device data
    public function getDeviceData() {
	    global $db;


		// Bring the device category info
		$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

		$db->where('d.device_user_ID', 1); // !!! ?

		$db->orderBy('d_cat.device_cat_order', 'asc');
		$db->orderBy(' d.device_order', 'asc');
		$devices = $db->get('devices d');


		// Prepare the devices data
		$device_data = [];
		foreach ($devices as $device) {

			if ( !isset($device_data[$device['device_cat_ID']]['devices']) ) {

				$device_data[$device['device_cat_ID']] = array(
					'device_cat_icon' => $device['device_cat_icon'],
					'device_cat_name' => $device['device_cat_name'],
					'devices' => array(),
				);

			}

			$device_data[$device['device_cat_ID']]['devices'][$device["device_ID"]] = $device;

		}


		return $device_data;
    }


}