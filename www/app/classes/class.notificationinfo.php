<?php

class Notification {


	// The pin ID
	public static $notification_ID;
	public static $notificationInfo;



	// SETTERS:

	public function __construct() {

    }



	// ID Setter
    public static function ID($notification_ID = null) {
	    global $db;


	    // Set the pin ID
		if ($notification_ID != null && is_numeric($notification_ID)) {


		    $db->where("notification_ID", $notification_ID);
			$notificationInfo = $db->getOne("notifications");

			if ( $notificationInfo ) {

				self::$notification_ID = $notification_ID;
				self::$notificationInfo = $notificationInfo;
				return new static;

			}


		}


	    // For the new pin
		if ($notification_ID == "new" || $notification_ID == 0) {

			self::$notification_ID = "new";
			return new static;

		}

		return false;

    }



	// GETTERS:

    // Get pin info
    public function getInfo($column = null) {

		return $column == null ? self::$notificationInfo : self::$notificationInfo[$column];

    }




    // ACTIONS

}