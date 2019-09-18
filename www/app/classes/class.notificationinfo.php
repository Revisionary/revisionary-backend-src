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


	    // Set the notification ID
		if (is_numeric($notification_ID)) {


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


	    // For the new notification
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
	    global $db, $log;


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

			$notificationHTML .= "<li>There's nothing to mention now. <br>Your notifications will be here.</li>";

		} else {

			//$notificationHTML .= "<li style='background-color: black; color: white;'>Notifications</li>";

		}


		// List the notifications
		$realNotificationsCount = 0;
		foreach ($notifications as $notification) {

			$notificationRow = "";

			$notificationNew = $notification['notification_read'] == 0;
			$notificationContent = $notification['notification'];


			$sender_ID = $notification['sender_user_ID'];
			$senderInfo = getUserInfo($sender_ID);

			// Skip if the user not found
			if (!$senderInfo) {

				$notificationRow = '<li class="'.($notificationNew ? "new" : "").' xl-hidden" data-error="user-not-found" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';

				continue;
			}


			$sender_full_name = $senderInfo['fullName'];


			// Object Info
			$object_ID = $notification['object_ID'];
			$object_type = $notification['object_type'];
			$object_data = ucfirst($object_type)::ID($object_ID);

			// Skip if the object not found
			if (!$object_data) {

				// Delete this notification !!! ???
				// Notification::ID( $notification['notification_ID'] )->remove();

				$notificationRow = '<li data-error="'.$object_type.'-'.$object_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';

				continue;
			}


			$object_name = $object_type != "device" && $object_type != "pin" ? $object_data->getInfo($object_type.'_name') : "";
			$object_link = site_url("$object_type/$object_ID");


			$notificationRow = '

			<li class="'.($notificationNew ? "new" : "").'" data-type="notification" data-id="'.$notification['notification_ID'].'">';


			$notificationRow .= '

				<div class="wrap xl-table xl-middle">
					<div class="col image">

						<picture class="profile-picture" '.$senderInfo['printPicture'].'> 																			<span>'.$senderInfo['nameAbbr'].'</span>
						</picture>

					</div>
					<div class="col content">
			';


			// NOTIFICATION TYPES
			if ($notification['notification_type'] == "text") {



				// Notification Content
				$notificationRow .= "
					$sender_full_name ".$notification['notification']."<br>
					<div class='date'>".timeago($notification['notification_time'])."</div>
				";



			} elseif ($notification['notification_type'] == "share") {



				$project_name = "";
				if ($object_type == "page") {

					$project_ID = $object_data->getInfo('project_ID');
					$project_name = " [".Project::ID($project_ID)->getInfo('project_name')."]";

				}


				// Notification Content
				$notificationRow .= "

					$sender_full_name shared a <b>$object_type</b> with you:
					<span><a href='$object_link'><b>$object_name</b>$project_name</a></span><br>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";



			} elseif ($notification['notification_type'] == "unshare") {



				$project_name = "";
				if ($object_type == "page") {

					$project_ID = $object_data->getInfo('project_ID');
					$project_name = " [".Project::ID($project_ID)->getInfo('project_name')."]";

				}


				// Notification Content
				$notificationRow .= "

					$sender_full_name unshared the <span><b>$object_name".$project_name."</b> $object_type</span> from you.</span><br>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";



			} elseif ($object_type == "pin") {


				$pin_type = $object_data->getInfo('pin_type');
				$pin_complete = $object_data->getInfo('pin_complete');

				$phase_ID = $object_data->getInfo('phase_ID');
				$phaseData = Phase::ID($phase_ID);

				// Skip if the phase not found
				if (!$phaseData) {

					// Delete this notification !!! ???
					// Notification::ID( $notification['notification_ID'] )->remove();

					$notificationRow = '<li data-error="phase-'.$phase_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';

					continue;
				}



				$page_ID = $phaseData->getInfo('page_ID');
				$page_data = Page::ID($page_ID);

				// Skip if the page not found
				if (!$page_data) {

					// Delete this notification !!! ???
					// Notification::ID( $notification['notification_ID'] )->remove();

					$notificationRow = '<li data-error="page-'.$page_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';

					continue;
				}

				$page_name = $page_data->getInfo('page_name');

				$project_ID = $page_data->getInfo('project_ID');
				$project_data = Project::ID($project_ID);

				// Skip if the page not found
				if (!$project_data) {

					// Delete this notification !!! ???
					// Notification::ID( $notification['notification_ID'] )->remove();

					$notificationRow = '<li data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';

					continue;
				}

				$project_name = $project_data->getInfo('project_name');


				$object_link = site_url("page/$page_ID#$object_ID");



				if ($notification['notification_type'] == "complete") {


					// Notification Content
					$notificationRow .= "

						$sender_full_name completed a <b>$pin_type pin</b>:
						<div class='wrap xl-table xl-middle'>
							<div class='col' style='width: 30px;'>
								<a href='$object_link' data-go-pin='$object_ID'><pin class='small' data-pin-complete='1' data-pin-type='$pin_type'>$notificationContent</pin></a>
							</div>
							<div class='col notif-text'>
								in <a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a>
							</div>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification['notification_type'] == "incomplete") {


					// Notification Content
					$notificationRow .= "

						$sender_full_name marked a pin <b>incomplete</b>:
						<div class='wrap xl-table xl-middle'>
							<div class='col' style='width: 30px;'>
								<a href='$object_link' data-go-pin='$object_ID'><pin class='small' data-pin-complete='0' data-pin-type='$pin_type'>$notificationContent</pin></a>
							</div>
							<div class='col notif-text'>
								in <a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a>
							</div>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification['notification_type'] == "comment") {


					// Notification Content
					$notificationRow .= "

						$sender_full_name wrote on a <a href='$object_link' data-go-pin='$object_ID'>$pin_type pin</a>:
						<span class='wrap xl-table xl-middle'>
							<a href='$object_link' data-go-pin='$object_ID'><span class='comment'>$notificationContent</span></a>
						</span><br>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification['notification_type'] == "new") { // New Pin

					$content = "";
					$style = "";
					$comment = "";

					if ( !empty($object_data->getInfo('pin_modification')) )
						$content = "<li><a href='$object_link' data-go-pin='$object_ID'> Content</a></li>";

					if ( !empty($object_data->getInfo('pin_css')) )
						$style = "<li><a href='$object_link' data-go-pin='$object_ID'> Style</a></li>";


					$comments = $object_data->comments();
					if ( count($comments) > 0 )
						$comment = "<li><a href='$object_link' data-go-pin='$object_ID'> Comment</a></li>";


					// Notification Content
					$notificationRow .= "

						$sender_full_name added a new <b>$pin_type pin</b>:
						<div class='wrap xl-table xl-middle'>
							<div class='col' style='width: 30px;'>
								<a href='$object_link' data-go-pin='$object_ID'><pin class='small' data-pin-complete='$pin_complete' data-pin-type='$pin_type'>$notificationContent</pin></a>
							</div>
							<div class='col notif-text'>
								in <a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a><br>
								<ul>
									$content
									$style
									$comment
								</ul>
							</div>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}



			} elseif ($notification['notification_type'] == "new") {




				if ($object_type == "page") {


					$project_ID = $object_data->getInfo('project_ID');
					$project_data = Project::ID($project_ID);

					// Skip if the page not found
					if (!$project_data) {
	
						// Delete this notification !!! ???
						// Notification::ID( $notification['notification_ID'] )->remove();
	
						$notificationRow = '<li data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';
	
						continue;
					}
					$project_name = $project_data->getInfo('project_name');


					// Notification Content
					$notificationRow .= "

						$sender_full_name added a <b>new page</b>:
						<span><a href='$object_link'><b>$object_name</b> [$project_name]</a></span><br>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}




				if ($object_type == "device") {


					$page_ID = $object_data->getInfo('page_ID');
					$page_data = Page::ID($page_ID);

					// Skip if the page not found
					if (!$page_data) {
	
						// Delete this notification !!! ???
						// Notification::ID( $notification['notification_ID'] )->remove();
	
						$notificationRow = '<li data-error="page-'.$page_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';
	
						continue;
					}

					$page_name = $page_data->getInfo('page_name');
					$project_ID = $page_data->getInfo('project_ID');
					$project_data = Project::ID($project_ID);

					// Skip if the page not found
					if (!$project_data) {
	
						// Delete this notification !!! ???
						// Notification::ID( $notification['notification_ID'] )->remove();
	
						$notificationRow = '<li data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' xl-hidden" data-type="notification" data-id="'.$notification['notification_ID'].'"></li>';
	
						continue;
					}

					$project_name = $project_data->getInfo('project_name');


					$title = "$sender_full_name added a <b>new screen</b>:";
					$content = "<span><a href='$object_link'>$notificationContent</a> in <a href='$object_link'><b>".$page_name."[".$project_name."]</b></a></span>";

					if ($notificationContent == "new phase") {

						$title = "$sender_full_name created a <b>new phase</b>";
						$content = "<span>for <a href='$object_link'><b>".$page_name."[".$project_name."]</b></a></span>";

					}



					// Notification Content
					$notificationRow .= "

						$title
						$content<br>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}



			}



			$notificationRow .= '
					</div>
				</div>

			</li>

			';

		
			$realNotificationsCount++;
		
		
		}


		$notificationHTML .= $notificationRow;


		// Load more link
		if ( ($offset + $notificationsCount) < $totalNotifications ) {
			$notificationHTML .= '<li class="more-notifications"><a href="#" data-offset="'.($offset + $notificationsCount).'">Load older notifications <i class="fa fa-level-down-alt"></i></a></li>';
		}





		// If there is no notifications
		if ($realNotificationsCount == 0) {

			$notificationHTML .= "<li>There's nothing to mention now. <br>Your notifications will be here.</li>";

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


	// Remove a notification
	public function remove() {
		global $db, $log;



	    // DB Checks !!!



		// Single Notification
	    if ( is_integer(self::$notification_ID) || is_numeric(self::$notification_ID) ) {


			// Remove the notification
			$db->where('notification_ID', intval(self::$notification_ID));
			$removed = $db->delete('notifications');

			if ($removed) $log->info("Notification #".self::$notification_ID." Removed: User #".currentUserID());


			return $removed;

		}


		if ( is_array(self::$notification_ID) && count(self::$notification_ID) > 0) {


			foreach (self::$notification_ID as $notification_ID) {


				// Remove the notification
				$db->where('notification_ID', self::$notification_ID);
				$removed = $db->delete('notifications');

				if ($removed) $log->info("Notification #".self::$notification_ID." Removed: User #".currentUserID());

			}


			return $removed;
		}


		return false;

	}




}