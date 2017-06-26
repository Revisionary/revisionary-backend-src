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
	public function __construct() {

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
    public static function ID($userId = null) {

		if ($userId == null)
			$userId = currentUserID();

	    // Set the user ID
		self::$userId = $userId;
		return new static;

    }




	// GETTERS:

    // Get the user name
    public function getUserInfo($column = null) {
	    global $db;

	    // GET IT FROM DB...
	    $db->where("user_ID", self::$userId);
		$user = $db->getOne("users");
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