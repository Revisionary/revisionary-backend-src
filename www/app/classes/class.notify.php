<?php

class Notify {


	// The user ID
	public static $user_ID;



	// SETTERS:
	public function __construct() {

    }



	// ID Setter
    public static function ID($user_ID = null) {

		if ($user_ID == null)
			$user_ID = currentUserID();

		if ( is_array($user_ID) )
			$user_ID = array_unique($user_ID);


	    // Set the user ID
		self::$user_ID = $user_ID;
		return new static;

    }



    // ACTIONS:

    // Email notifications
    public function mail(
	    string $subject,
	    string $notification
    ) {
	    global $config;

	    if ($config['env']['name'] != "remote-dev") return true;



		$recipients = "";
		if ( is_array(self::$user_ID) ) {

			foreach (self::$user_ID as $user_ID) {

				if ( is_string($user_ID) )
					$user_ID = intval($user_ID);

				// Bring the user info
				$recipients .= getUserInfo($user_ID)['email'].",";

			}

		} elseif ( is_string(self::$user_ID) ) {

			// ID written as a string email
			$recipients = self::$user_ID;

		} else {

			// Bring the user info
			$recipients = getUserInfo(self::$user_ID)['email'];

		}




		// Send the email
		return Mail::ASYNCSEND(
			$recipients,
			urlencode($subject),
			urlencode($notification)
		);

    }



    // Web notifications
    public function web() {


	    // Send web notification to user(s)


    }


}