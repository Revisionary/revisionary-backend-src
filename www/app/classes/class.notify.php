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

		if ( is_string($user_ID) )
			$user_ID = intval($user_ID);

	    // Set the user ID
		self::$user_ID = $user_ID;
		return new static;

    }



    // ACTIONS:

    // Email
    public function mail(
	    string $subject,
	    string $notification
    ) {


		$recipients = "";
		if ( is_array(self::$user_ID) ) {

			foreach (self::$user_ID as $user_ID) {

				if ( is_string($user_ID) )
					$user_ID = intval($user_ID);

				// Bring the user info
				$recipients .= getUserData($user_ID)['email'].",";

			}

		} else {

			// Bring the user info
			$recipients = getUserData(self::$user_ID)['email'];

		}




		// Send the email
		return Mail::ASYNCSEND(
			$recipients,
			urlencode($subject),
			urlencode($notification)
		);

    }


}