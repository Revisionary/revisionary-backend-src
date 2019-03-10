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



		//$notificationHTML = "<ul>";
		$notificationHTML = "";



		// Get only new notifications
		$newNotifications = User::ID()->getNewNotifications()['notifications'];
		$newNotificationsCount = count($newNotifications);

		// All notifications
		$notificationData = User::ID()->getNotifications($offset);
		$notifications = $notificationData['notifications'];
		$totalNotifications = $notificationData['totalCount'];



		// Merge all the notifications
		$notifications = array_unique(array_merge($newNotifications, $notifications), SORT_REGULAR);
		$notificationsCount = count($notifications);



		// If there is no notifications
		if ($notificationsCount == 0) {

			$notificationHTML .= "<li>There's nothing to mention now. <br/>Your notifications will be here.</li>";

		} else {

			//$notificationHTML .= "<li style='background-color: black; color: white;'>Notifications</li>";

		}


		// List the notifications
		foreach ($notifications as $notification) {

			$sender_ID = $notification['sender_user_ID'];
			$senderInfo = getUserInfo($sender_ID);
			$sender_full_name = $senderInfo['fullName'];
			$notificationNew = $notification['notification_read'] == 0;
			$notificationContent = $notification['notification'];


			// Object Info
			$object_ID = $notification['object_ID'];
			$object_type = $notification['object_type'];
			$object_data = ucfirst($object_type)::ID($object_ID);
			$object_name = $object_type != "device" && $object_type != "pin" ? $object_data->getInfo($object_type.'_name') : "";
			$object_link = site_url("$object_type/$object_ID");


			$notificationHTML .= '

			<li class="'.($notificationNew ? "new" : "").'" data-type="notification" data-id="'.$notification['notification_ID'].'">

				<div class="wrap xl-table xl-middle">
					<div class="col image">

						<picture class="profile-picture" '.$senderInfo['printPicture'].'> 																			<span class="has-pic">'.$senderInfo['nameAbbr'].'</span>
						</picture>

					</div>
					<div class="col content">
			';


			// NOTIFICATION TYPES
			if ($notification['notification_type'] == "text") {



				// Notification Content
				$notificationHTML .= "
							$sender_full_name ".$notification['notification']."<br/>
							<div class='date'>".timeago($notification['notification_time'])."</div>
				";



			} elseif ($notification['notification_type'] == "share") {



				$project_name = "";
				if ($object_type == "page") {

					$project_ID = $object_data->getInfo('project_ID');
					$project_name = " [".Project::ID($project_ID)->getInfo('project_name')."]";

				}


				// Notification Content
				$notificationHTML .= "

					$sender_full_name shared a <b>$object_type</b> with you:
					<span><a href='$object_link'><b>$object_name</b>$project_name</a></span><br/>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";



			} elseif ($notification['notification_type'] == "new") {




				if ($object_type == "page") {


					$project_ID = $object_data->getInfo('project_ID');
					$project_name = Project::ID($project_ID)->getInfo('project_name');


					// Notification Content
					$notificationHTML .= "

						$sender_full_name added a <b>new page</b>:
						<span><a href='$object_link'><b>$object_name</b> [$project_name]</a></span><br/>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}




				if ($object_type == "device") {


					$page_ID = $object_data->getInfo('page_ID');
					$page_data = Page::ID($page_ID);
					$page_name = $page_data->getInfo('page_name');
					$project_ID = $page_data->getInfo('project_ID');
					$project_name = Project::ID($project_ID)->getInfo('project_name');



					// Notification Content
					$notificationHTML .= "

						$sender_full_name added a <b>new screen</b>:
						<span><a href='$object_link'>$notificationContent</a> in <a href='$object_link'><b>".$page_name."[".$project_name."]</b></a></span><br/>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}



			} elseif ($notification['notification_type'] == "complete") {


				$pin_type = $object_data->getInfo('pin_type');
				$device_ID = $object_data->getInfo('device_ID');
				$page_ID = Device::ID($device_ID)->getInfo('page_ID');
				$page_data = Page::ID($page_ID);
				$page_name = $page_data->getInfo('page_name');
				$project_ID = $page_data->getInfo('project_ID');
				$project_name = Project::ID($project_ID)->getInfo('project_name');


				$object_link = site_url("revise/$device_ID");


				// Notification Content
				$notificationHTML .= "

					$sender_full_name completed a <b>$pin_type pin</b>:
					<span class='wrap xl-table xl-middle'>
						<span class='col'>
							<a href='$object_link'><pin class='small' data-pin-complete='1' data-pin-type='$pin_type'></pin></a>
						</span>
						<span class='col' style='padding-left: 4px;'>
							in <a href='$object_link'><b>".$page_name."[".$project_name."]</b></a>
						</span>
					</span><br/>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";


			}



			$notificationHTML .= '
					</div>
				</div>

			</li>

			';

		}


		// Load more link
		if ( ($offset + $notificationsCount) < $totalNotifications ) {
			$notificationHTML .= '<li class="more-notifications"><a href="#" data-offset="'.($offset + $notificationsCount).'">Load older notifications <i class="fa fa-level-down-alt"></i></a></li>';
		}


		//$notificationHTML .= "</ul>";


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