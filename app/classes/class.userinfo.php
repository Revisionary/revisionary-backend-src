<?php

class User {


	// The page ID
	public static $userId;

	// The page version
	public $userName;

	// The first name
	public $firstName;

	// The last name
	public $lastName;

	// The full name
	public $fullName;

	// The user picture name
	public $userPic;

	// The user picture URL
	public $userPicUrl;




	// SETTERS:
	public function __construct($userId = "") {

		// Set the user ID
		self::$userId = currentUserID();
		if ($userId != "")
			self::$userId = $userId;

		// Set the user name
        $this->userName = $this->getUserInfo('user_name');

		// Set the first name
        $this->firstName = $this->getUserInfo('user_first_name');

		// Set the last name
        $this->lastName = $this->getUserInfo('user_last_name');

		// Set the full name
        $this->fullName = $this->getUserInfo('user_first_name')." ".$this->getUserInfo('user_last_name');

		// Set the user picture URL
        $this->userPic = $this->getUserInfo('user_picture');

		// Set the user picture URL
        $this->userPicUrl = cache_url('user-'.self::$userId.'/'.$this->getUserInfo('user_picture'));

    }



	// ID Setter
    public static function ID($userId="") {

	    // Set the user ID
		if ( is_null( self::$userId ) ) {
			self::$userId = new self($userId);
		}
		return self::$userId;

    }




	// GETTERS:

    // Get the user name
    public function getUserInfo($column = "") {
	    global $db;

	    // GET IT FROM DB...
	    $db->where("user_ID", self::$userId);
		$user = $db->getOne("users", $column);
		if ($user)
			return $user[$column];

	    return false;
    }

    // Print picture image
    public function printPicture() {

		if ($this->userPic != "")
			return 'style="background-image: url('.$this->userPicUrl.');"';

	    return false;
    }

}