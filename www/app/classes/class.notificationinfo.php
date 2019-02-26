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


	    // List of notifications
		if ( is_array($notification_ID) && count($notification_ID) > 0) {

			self::$notification_ID = $notification_ID;
			return new static;

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

    // Get the notifications HTML
    public function getHTML($offset = 0) {
	    global $db;



		$notificationHTML = "";



		// Get notifications
		$notificationData = User::ID()->getNotifications($offset);
		$notifications = $notificationData['notifications'];
		$newNotifications = array_filter($notifications, function($value) { return $value['notification_read'] == 0; });
		$newNotificationsCount = count($newNotifications);
		$totalNotifications = $notificationData['totalCount'];


		// If there is no notifications
		if (count($notifications) == 0) {

			$notificationHTML = "<li>There's nothing to mention now. <br/>Your notifications will be here.</li>";

		}


		// List the notifications
		foreach ($notifications as $notification) {

			$sender_ID = $notification['sender_user_ID'];
			$senderInfo = getUserInfo($sender_ID);
			$notificationNew = $notification['notification_read'] == 0;


			$notificationHTML .= '

			<li class="'.($notificationNew ? "new" : "").'" data-type="notification" data-id="'.$notification['notification_ID'].'">

				<div class="wrap xl-table xl-middle">
					<div class="col image">

						<picture class="profile-picture" '.$senderInfo['printPicture'].'> 																										<span class="has-pic">'.$senderInfo['nameAbbr'].'</span>
						</picture>

					</div>
					<div class="col content">

						'.$senderInfo['fullName'].' '.$notification['notification'].'<br/>
						<div class="date">'.timeago($notification['notification_time']).'</div>

					</div>
				</div>

			</li>

			';

		}


		return $notificationHTML;

    }


    // Get count
    public function getCount() {
	    global $db;

		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', currentUserID());
		$db->where('con.notification_read', 0);

		$count = $db->getValue("notifications n", "count(n.notification_ID)");

		return $count;

    }


    // Read all
    public function readAll() {
	    global $db;

		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', currentUserID());
		$db->where('con.notification_read', 0);

		if ( is_array(self::$notification_ID) ) $db->where('n.notification_ID', self::$notification_ID, "IN");

		$allRead = $db->update("notifications n", array(
			"notification_read" => 1
		));

		return $allRead;

    }




}