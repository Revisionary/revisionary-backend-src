<?php

class Pin {


	// The pin ID
	public static $pin_ID;

	// The pin name
	public $pin_type;

	// Current user ID
	public $user_ID;



	// SETTERS:

	public function __construct() {

		// Set the pin name
        $this->pin_type = $this->getPinInfo('pin_type');

        // Set the user ID
        $this->user_ID = $this->getPinInfo('user_ID');

    }


	// ID Setter
    public static function ID($pin_ID = null) {

	    // Set the pin ID
		if ($pin_ID != null) self::$pin_ID = $pin_ID;
		return new static;

    }




	// GETTERS:

    // Get pin info
    public function getPinInfo($column) {
	    global $db;

	    $db->where('pin_ID', self::$pin_ID);
	    $pin = $db->getOne('pins', $column);
		if ($pin)
			return $pin[$column];

	    return false;
    }




    // ACTIONS

    // Add a new pin
    public function addNew(
	    int $pin_version_ID,
    	string $pin_type = 'standard',
    	bool $pin_private = false,
    	int $pin_x = 50,
    	int $pin_y = 50,
    	int $pin_element_index = null,
	    int $pin_user_ID = null
    ) {
	    global $db;


    	// Security check !!!
		if ($pin_type != "standard" && $pin_type != "live") return false;



		// More DB Checks of arguments !!!



		// Add the pin
		$pin_ID = $db->insert('pins', array(
			"user_ID" => $pin_user_ID || currentUserID(),
			"version_ID" => $pin_version_ID,
			"pin_type" => $pin_type,
			"pin_private" => $pin_private,
			"pin_x" => $pin_x,
			"pin_y" => $pin_y,
			"pin_element_index" => $pin_element_index
		));



		return $pin_ID;

	}


    // Relocate a pin
    public function reLocate(
    	int $pin_x = 50,
    	int $pin_y = 50
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


		return $pin_updated;

	}


    // Delete a pin
    public function remove() {
	    global $db;



		// More DB Checks of arguments !!! (This user can delete?)



		// Delete the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_deleted = $db->delete('pins');


		return $pin_deleted;

	}


    // Complete a pin
    public function complete() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 1));


		return $pin_updated;

	}


    // inComplete a pin
    public function inComplete() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 0));


		return $pin_updated;

	}

}