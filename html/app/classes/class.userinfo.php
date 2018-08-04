<?php

class User {


	// The page ID
	public static $user_ID;

	// The page version
	public $userName;

	// The first name
	public $firstName;

	// The last name
	public $lastName;

	// The full name
	public $fullName;

	// The email
	public $email;

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

		// Set the email
        $this->email = $this->getUserInfo('user_email');

		// Set the user picture URL
        $this->userPic = $this->getUserInfo('user_picture');

		// Set the user picture URL
        $this->userPicUrl = $this->getUserInfo('user_picture') != "" ? cache_url('user-'.self::$user_ID.'/'.$this->getUserInfo('user_picture')) : "none.png";

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

    // Get the user name
    public function getUserInfo($column = null) {
	    global $db;

	    // GET IT FROM DB...
	    $db->where("user_ID", self::$user_ID);
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