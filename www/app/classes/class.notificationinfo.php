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

    // Get the notifications HTML
    public function getHTML() {
	    global $db;



		$notificationHTML = "";



		// Get notifications
		$notifications = User::ID()->getNotifications();
		$newNotifications = array_filter($notifications, function($value) { return $value['notification_read'] == 0; });
		$notificationsCount = count($newNotifications);


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

			<li class="'.($notificationNew ? "new" : "").'">

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


		return array(
			'count' => $notificationsCount,
			'html' => $notificationHTML
		);

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

		$allRead = $db->update("notifications n", array(
			"notification_read" => 1
		));

		return $allRead;

    }




}