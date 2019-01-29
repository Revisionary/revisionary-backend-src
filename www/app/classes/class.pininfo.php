<?php

class Pin {


	// The pin ID
	public static $pin_ID;
	public static $pinInfo;



	// SETTERS:

	public function __construct() {

    }



	// ID Setter
    public static function ID($pin_ID = null) {
	    global $db;


	    // Set the pin ID
		if ($pin_ID != null && is_numeric($pin_ID)) {


			// Bring the user level info
		    $db->where("pin_ID", $pin_ID);
			$pinInfo = $db->getOne("pins");

			if ( $pinInfo ) {

				self::$pin_ID = $pin_ID;
				self::$pinInfo = $pinInfo;
				return new static;

			}


		}


	    // For the new pin
		if ($pin_ID == "new" || $pin_ID == 0) {

			self::$pin_ID = "new";
			return new static;

		}

		return false;

    }



	// GETTERS:

    // Get pin info
    public function getInfo($column = null) {

		return $column == null ? self::$pinInfo : self::$pinInfo[$column];

    }




    // ACTIONS

    // Add a new pin
    public function addNew(
	    int $pin_device_ID,
    	string $pin_type = 'standard',
    	bool $pin_private = false,
    	float $pin_x = 50,
    	float $pin_y = 50,
    	int $pin_element_index = null,
	    string $pin_modification_type = null
    ) {
	    global $db;


    	// Security check !!!
		if ($pin_type != "standard" && $pin_type != "live") return false;



		// More DB Checks of arguments !!!



		// Add the pin
		$pin_ID = $db->insert('pins', array(
			"user_ID" => currentUserID(),
			"device_ID" => $pin_device_ID,
			"pin_type" => $pin_type,
			"pin_private" => $pin_private,
			"pin_x" => $pin_x,
			"pin_y" => $pin_y,
			"pin_element_index" => $pin_element_index,
			"pin_modification_type" => $pin_modification_type
		));


		// Notify the users
		if ($pin_ID) {

			$users = Pin::ID($pin_ID)->getUsers();
			foreach ($users as $user_ID) {

				Notify::ID( intval($user_ID) )->mail(
					getUserInfo()['fullName']." added a new $pin_type pin",
					getUserInfo()['fullName']."(".getUserInfo()['userName'].") added a new $pin_type pin: <br>
					".site_url("revise/$pin_device_ID#$pin_ID")
				);

			}

		}


		// Update the page modification date
		if ($pin_ID) {
			$page_ID = Device::ID($pin_device_ID)->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}



		return $pin_ID;

	}


    // Relocate a pin
    public function reLocate(
    	float $pin_x = 50,
    	float $pin_y = 50
    ) {
	    global $db;



		// More DB Checks of arguments !!!



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_locations = array(
			'pin_x' => $pin_x,
			'pin_y' => $pin_y
		);
		$pin_updated = $db->update('pins', $pin_locations);

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // Delete a pin
    public function remove() {
	    global $db;



		// More DB Checks of arguments !!! (This user can delete?)



		// Delete the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_deleted = $db->delete('pins');

		// Update the page modification date
		if ($pin_deleted) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_deleted;

	}


    // Complete a pin
    public function complete() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 1));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // inComplete a pin
    public function inComplete() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 0));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));


			// Notify the page owners about the pin completion !!!

		}


		return $pin_updated;

	}


    // Convert a pin
    public function convert(
	    string $pin_type,
	    string $pin_private
    ) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		$current_pin_type = $this->getInfo('pin_type');

		// Update the pin
		$db->where('pin_ID', self::$pin_ID);

		// Don't convert the other's pin private status
		if ($pin_private == "1") $db->where('user_ID', currentUserID());

		$pin_data = array(
			'pin_type' => $pin_type,
			'pin_private' => $pin_private,
		);

		// If the new type is standard, reset the modifications
		if ($pin_type == 'standard') {
			$pin_data['pin_modification'] = null;
			$pin_data['pin_modification_type'] = null;
		}

		$pin_updated = $db->update('pins', $pin_data);


		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // Modify a pin
    public function modify($modification) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_modification' => $modification));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // Update CSS
    public function updateCSS($css) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_css' => $css));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // Get the comments
    public function comments() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Get the comments
		$db->join("users u", "c.user_ID = u.user_ID", "LEFT");
		$db->orderBy('c.comment_added', 'ASC');
		$db->where('pin_ID', self::$pin_ID);
		$comments = $db->get('pin_comments c', null, "c.comment_ID, c.comment_modified, c.pin_comment, c.comment_added, c.comment_modified, u.user_first_name, u.user_ID, u.user_last_name, u.user_picture");

		return $comments;

	}


    // Add a new comment
    public function addComment(
    	string $message
	) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Add the comment
		$comment_ID = $db->insert('pin_comments', array(
			"pin_comment" => $message,
			"pin_ID" => self::$pin_ID,
			"user_ID" => currentUserID()
		));


		// Notify the users
		if ($comment_ID) {

			$users = $this->getUsers();

			foreach ($users as $user_ID) {


				Notify::ID( intval($user_ID) )->mail(
					getUserInfo()['fullName']." posted a comment",
					getUserInfo()['fullName']."(".getUserInfo()['userName'].") wrote: <br>
					\"$message\" <br><br>

					<b>Page Link:</b> ".site_url('revise/'.$this->getInfo('device_ID')."#".self::$pin_ID)
				);


			}


		}




		return $comment_ID;

	}


	// Get page users
	public function getUsers() {
		global $db;


		$pin_ID = self::$pin_ID;
		$device_ID = $this->getInfo('device_ID');
		$page_ID = Device::ID( $device_ID )->getInfo('page_ID');
		$pageData = Page::ID( $page_ID );
		$project_ID = $pageData->getInfo('project_ID');
		$projectData = Project::ID($project_ID);


		$users = array();


		// Get the page users
		$users = array_merge($users, $pageData->getUsers());


		// Get the project users
		$users = array_merge($users, $projectData->getUsers());


		// Remove duplicates
		$users = array_unique($users);


		// Exclude myself
		if ( ($user_key = array_search(currentUserID(), $users)) !== false ) {
		    unset($users[$user_key]);
		}


		return $users;

	}


    // Delete a comment
    public function deleteComment(
    	int $comment_ID
	) {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Delete the comment
		$db->where('pin_ID', self::$pin_ID);
		$db->where('comment_ID', $comment_ID);
		$db->where('user_ID', currentUserID());
		$comment_deleted = $db->delete('pin_comments');

		return $comment_deleted;

	}

}