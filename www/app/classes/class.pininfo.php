<?php

class Pin {


	// The pin ID
	public static $pin_ID;
	public static $pinInfo;



	// SETTERS:

	public function __construct() {

    }



	// ID Setter
    public static function ID($pin_ID = null) {
	    global $db;


	    // Set the pin ID
		if ($pin_ID != null && is_numeric($pin_ID)) {


			// Bring the user level info
		    $db->where("pin_ID", $pin_ID);
			$pinInfo = $db->getOne("pins");

			if ( $pinInfo ) {

				self::$pin_ID = $pin_ID;
				self::$pinInfo = $pinInfo;
				return new static;

			}


		}


	    // For the new pin
		if ($pin_ID == "new" || $pin_ID == 0) {

			self::$pin_ID = "new";
			return new static;

		}

		return false;

    }



	// GETTERS:

    // Get pin info
    public function getInfo($column = null) {

		return $column == null ? self::$pinInfo : self::$pinInfo[$column];

    }


	// Get page users
	public function getUsers($include_me = false) {
		global $db;


		$pin_ID = self::$pin_ID;
		$device_ID = $this->getInfo('device_ID');
		$page_ID = Device::ID( $device_ID )->getInfo('page_ID');
		$pageData = Page::ID( $page_ID );


		// Get the page users
		$users = $pageData->getUsers($include_me);


		return $users;

	}




    // ACTIONS

    // Add a new pin
    public function addNew(
	    int $pin_device_ID,
    	string $pin_type = 'standard',
    	bool $pin_private = false,
    	float $pin_x = 50,
    	float $pin_y = 50,
    	int $pin_element_index = null,
	    string $pin_modification_type = null,
	    string $imgDataUrl = ""
    ) {
	    global $db, $log;


    	// Security check !!!
		if ($pin_type != "standard" && $pin_type != "live") return false;



		// More DB Checks of arguments !!!



		// Add the pin
		$pin_ID = $db->insert('pins', array(
			"user_ID" => currentUserID(),
			"device_ID" => $pin_device_ID,
			"pin_type" => $pin_type,
			"pin_private" => $pin_private,
			"pin_x" => $pin_x,
			"pin_y" => $pin_y,
			"pin_element_index" => $pin_element_index,
			"pin_modification_type" => $pin_modification_type
		));


		// Notify the users
		if ($pin_ID) {

			$pinData = Pin::ID($pin_ID);
			$device_ID = $pinData->getInfo('device_ID');
			$page_ID = Device::ID( $device_ID )->getInfo('page_ID');
			$pageData = Page::ID( $page_ID );
			$project_ID = $pageData->getInfo('project_ID');
			$projectData = Project::ID( $project_ID );

		}


		// Update the page modification date
		if ($pin_ID) {
			$page_ID = Device::ID($pin_device_ID)->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($pin_ID) $log->info(ucfirst($pin_type)." Pin #$pin_ID Added to: '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #$device_ID | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_ID;

	}


    // Relocate a pin
    public function reLocate(
    	float $pin_x = 50,
    	float $pin_y = 50
    ) {
	    global $db;



		// More DB Checks of arguments !!!



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_locations = array(
			'pin_x' => $pin_x,
			'pin_y' => $pin_y
		);
		$pin_updated = $db->update('pins', $pin_locations);

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			Page::ID($page_ID)->edit('page_modified', date('Y-m-d H:i:s'));
		}


		return $pin_updated;

	}


    // Delete a pin
    public function remove() {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can delete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );


		// Delete the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_deleted = $db->delete('pins');

		// Update the page modification date
		if ($pin_deleted) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($pin_deleted) $log->info("$pin_type Pin #".self::$pin_ID." Removed from: '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_deleted;

	}


    // Complete a pin
    public function complete(string $imgDataUrl = "") {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );


		// Element screenshot
		$image = "";
		if ($imgDataUrl != "") {
			$image = "<img src='$imgDataUrl' style='border: 2px dashed red'><br><br>";
		}


		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 1));

		// If successful
		if ($pin_updated) {


			// Update the page modification date
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Completed: '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


/*
			// Notify the users
			$users = $this->getUsers();
			Notify::ID($users)->mail(
				getUserInfo()['fullName']." completed a pin task on '".$pageData->getInfo('page_name')."' page",
				"$image".getUserInfo()['fullName']."(".getUserInfo()['userName'].") completed a pin task on '".$pageData->getInfo('page_name')."' page: ".site_url('revise/'.$this->getInfo('device_ID')."#".self::$pin_ID)
			);
*/

		}


		return $pin_updated;

	}


    // inComplete a pin
    public function inComplete(string $imgDataUrl = "") {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );


		// Element screenshot
		$image = "";
		if ($imgDataUrl != "") {
			$image = "<img src='$imgDataUrl' style='border: 2px dashed red'><br><br>";
		}


		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 0));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Notify the users
		if ($pin_updated) {

			$users = $this->getUsers();
			Notify::ID($users)->mail(
				getUserInfo()['fullName']." marked a pin task as not completed on '".$pageData->getInfo('page_name')."' page",
				"$image".getUserInfo()['fullName']."(".getUserInfo()['userName'].") marked a pin task as not completed on '".$pageData->getInfo('page_name')."' page: ".site_url('revise/'.$this->getInfo('device_ID')."#".self::$pin_ID)
			);


		}


		// Site log
		if ($pin_updated) $log->info("$pin_type Pin #".self::$pin_ID." Incompleted: '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_updated;

	}


    // Convert a pin
    public function convert(
	    string $pin_type,
	    string $pin_private
    ) {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$current_pin_type = $this->getInfo('pin_type');
		$current_pin_private = $this->getInfo('pin_private');

		// Update the pin
		$db->where('pin_ID', self::$pin_ID);

		// Don't convert the other's pin private status
		if ($pin_private == "1") $db->where('user_ID', currentUserID());

		$pin_data = array(
			'pin_type' => $pin_type,
			'pin_private' => $pin_private,
		);

		// If the new type is standard, reset the modifications
		if ($pin_type == 'standard') {
			$pin_data['pin_modification'] = null;
			$pin_data['pin_modification_type'] = null;
		}

		$pin_updated = $db->update('pins', $pin_data);


		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($pin_updated) $log->info(ucfirst($current_pin_type)." Pin #".self::$pin_ID." Converted to ".ucfirst($pin_type)." ".($pin_private == "1" ? "(Private)" : "(Public)" )." -Before: ".ucfirst($current_pin_type)." ".($current_pin_private == "1" ? "(Private)" : "(Public)" )."-: '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_updated;

	}


    // Modify a pin
    public function modify($modification) {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_modification' => $modification));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($pin_updated) $log->info("$pin_type Pin #".self::$pin_ID." Modified: '$modification' | '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_updated;

	}


    // Update CSS
    public function updateCSS($css) {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_css' => $css));

		// Update the page modification date
		if ($pin_updated) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($pin_updated) $log->info("$pin_type Pin #".self::$pin_ID." CSS Updated: '$css' | '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		return $pin_updated;

	}


    // Get the comments
    public function comments() {
	    global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Get the comments
		$db->join("users u", "c.user_ID = u.user_ID", "LEFT");
		$db->orderBy('c.comment_added', 'ASC');
		$db->where('pin_ID', self::$pin_ID);
		$comments = $db->get('pin_comments c', null, "c.comment_ID, c.comment_modified, c.pin_comment, c.comment_added, c.comment_modified, u.user_first_name, u.user_ID, u.user_last_name, u.user_picture");

		return $comments;

	}


    // Add a new comment
    public function addComment(
    	string $message,
    	string $newPin = "no",
    	string $imgDataUrl = ""
	) {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );


		// Element screenshot
		$image = "";
		if ($imgDataUrl != "") {
			$image = "<img src='$imgDataUrl' style='border: 2px dashed red'><br><br>";
		}



		// Add the comment
		$comment_ID = $db->insert('pin_comments', array(
			"pin_comment" => $message,
			"pin_ID" => self::$pin_ID,
			"user_ID" => currentUserID()
		));


		// Update the page modification date
		if ($comment_ID) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($comment_ID) $log->info("$pin_type Pin #".self::$pin_ID." Comment Added: '$message' | '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());



		return $comment_ID;

	}


    // Delete a comment
    public function deleteComment(
    	int $comment_ID
	) {
	    global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Delete the comment
		$db->where('pin_ID', self::$pin_ID);
		$db->where('comment_ID', $comment_ID);
		$db->where('user_ID', currentUserID());
		$comment_deleted = $db->delete('pin_comments');


		// Update the page modification date
		if ($comment_deleted) {
			$page_ID = Device::ID( $this->getInfo('device_ID') )->getInfo('page_ID');
			$pageData = Page::ID($page_ID);
			$pageData->edit('page_modified', date('Y-m-d H:i:s'));
		}


		// Site log
		if ($comment_deleted) $log->info("$pin_type Pin #".self::$pin_ID." Comment Deleted: Comment #$comment_ID | '".$pageData->getInfo('page_name')."' Page #$page_ID | Device #".$this->getInfo('device_ID')." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());



		return $comment_deleted;

	}


	// New pin notification
	public function newNotification(
		int $pin_number,
		string $before_screenshot,
		string $after_screenshot
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		if ( $this->getInfo('user_ID') != currentUserID() ) return true;




		// Prepare the message
		$template = $this->emailTemplate($pin_number, $before_screenshot, $after_screenshot, getUserInfo()['fullName']." added a new ".$this->getInfo('pin_type')." pin", "View Pin");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);



		return true;

	}


	// New comment notification
	public function newCommentNotification(
		int $pin_number,
		string $before_screenshot,
		string $after_screenshot
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		//if ( $this->getInfo('user_ID') != currentUserID() ) return true;



		// Prepare the message
		$template = $this->emailTemplate($pin_number, $before_screenshot, $after_screenshot, getUserInfo()['fullName']." wrote a new comment", "View Comments");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);




		return true;

	}


	// Complete notification
	public function completeNotification(
		int $pin_number,
		string $before_screenshot,
		string $after_screenshot
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		//if ( $this->getInfo('user_ID') != currentUserID() ) return true;



		// Prepare the message
		$template = $this->emailTemplate($pin_number, $before_screenshot, $after_screenshot, getUserInfo()['fullName']." completed a pin task", "View Pin");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);



		return true;

	}




	// TEMPLATES
	private function emailTemplate(
		int $pin_number,
		string $before_screenshot,
		string $after_screenshot,
		string $pin_message,
		string $button_text
	) {


		// Pin info
		$pin_ID = self::$pin_ID;
		$pin_type = $this->getInfo('pin_type');
		$pin_complete = $this->getInfo('pin_complete') === 1;


		// Draw the pin
		$pinShadow = $pin_type == "live" ? "0px 0px 18px 1px #149440" : "0px 0px 10px 1px rgba(255, 255, 255, 0.5)";
		$pinShape = "


			<div style='

				position: relative;
			    display: inline-block;
			    border-radius: 50%;
			    letter-spacing: 0;
			    text-align: center;

			    background-color: black;
			    color: white;

			    width: 45px;
			    height: 45px;
			    line-height: 45px;
			    font-size: 14px;

			    box-shadow: $pinShadow;

			'>$pin_number</div>


		";

		if ($pin_complete) $pinShape = "

			<style>
				.pin:hover > div {
					opacity: 0;
				}
			</style>


			<div class='pin' style='

				position: relative;
			    display: inline-block;
			    border-radius: 50%;
			    letter-spacing: 0;
			    text-align: center;

			    background-color: white;
			    color: black;
			    border: 1px solid black;

			    width: 45px;
			    height: 45px;
			    line-height: 45px;
			    font-size: 14px;

			    box-shadow: $pinShadow;

			'><div style='

				    font-weight: 900;
				    font-size: 17px;
				    display: flex;
				    justify-content: center;
				    align-items: center;
				    position: absolute;
				    width: inherit;
				    height: inherit;
				    text-align: center;
				    background-color: white;
				    border-radius: 50%;
				    box-sizing: border-box;
				    transition: 500ms;

				'><img src='".asset_url('icons/icon-check.svg')."' alt='Completed' style='max-width: 18px; height: auto;' /></div>$pin_number</div>


		";


		// Page/Project info
		$device_ID = $this->getInfo('device_ID');
		$page_ID = Device::ID( $device_ID )->getInfo('page_ID');
		$pageData = Page::ID( $page_ID );
		$project_ID = $pageData->getInfo('project_ID');
		$projectData = Project::ID( $project_ID );


		// After element screenshot
		$afterImage = $afterTitle = "";
		if ($after_screenshot != "") {
			$afterTitle = "<h3 style='margin-bottom: 5px'>After</h3>";
			$afterImage = "<img src='$after_screenshot' style='border: 2px dashed red; max-width: 100%;'><br><br>";
		}


		// Before element screenshot
		$beforeImage = $beforeTitle = "";
		if ($before_screenshot != "") {
			$beforeTitle = "<h3 style='margin-bottom: 5px'>Before</h3>";
			$beforeImage = "<img src='$before_screenshot' style='border: 2px dashed red; max-width: 100%;'><br><br>";
		}

		if ($before_screenshot == $after_screenshot || ($before_screenshot == "" && $after_screenshot != "") ) {

			$beforeTitle = $beforeImage = "";
			$afterTitle = "<h3 style='margin-bottom: 5px'>Screenshot</h3>";

		}

		$screenshots = "

			<tr>
				<td colspan='2'>

					<br>
					$afterTitle
					$afterImage

					$beforeTitle
					$beforeImage

				</td>
			</tr>

		";


		// Modification info
		$modification_type = $this->getInfo('pin_modification_type');
		$modification = $this->getInfo('pin_modification');


		// CSS changes info
		$css = $this->getInfo('pin_css');


		// Comments info
		$comments = array_reverse( $this->comments() );

		// Print comments if exists
		$commentsList = "No comments";
		$comment_count = 0;
		if ( count($comments) > 0 ) {

			$commentsList = "<table>";

			$previous_user_ID = 0;
			foreach ($comments as $comment) { $comment_count++;

				$different_user = $previous_user_ID != $comment['user_ID'];

				$has_photo = $comment['user_picture'] != null;
				$avatar = "
					<div style='

					    display: inline-block;
					    border: 1px solid black;

					    background-color: white;
					    color: black;

					    width: 30px;
					    height: 30px;
					    line-height: 30px;
					    font-size: 12px;
					    letter-spacing: 0;
					    text-align: center;

					    background-repeat: no-repeat;
					    background-size: cover;
					    background-position: 50% 50%;
					    background-image: ".($has_photo ? "url(".cache_url('users/user-'.$comment['user_ID'].'/'.$comment['user_picture']).")" : "none").";

					'>".($has_photo ? "" : mb_substr($comment['user_first_name'], 0, 1).mb_substr($comment['user_last_name'], 0, 1))."</div>
				";

				$user_full_name = $different_user ? "<b>".$comment['user_first_name']." ".$comment['user_last_name']."</b><br>" : "";
				$user_pic = $different_user ? $avatar : "";

				$commentsList .= "
				<tr>
					<td width='40'>
						".($different_user && $comment_count > 1 ? "<br><br>" : "")."

						$user_pic

					</td>";
					$commentsList .= "
					<td>
						".($different_user && $comment_count > 1 ? "<br><br>" : "")."

						$user_full_name
						".$comment['pin_comment']." <span style='padding-left: 5px; padding-right: 5px; font-size: 10px; line-height: 20px; opacity: 0.4;'>".timeago($comment['comment_modified'])."</span>

					</td>
				</tr>";

				$previous_user_ID = $comment['user_ID'];

			}

			$commentsList .= "</table>";

		}




		return [ 'message' => "

		<table width='100%' style='max-width:600px; margin: 20px 0;'>

			<tr>
				<td colspan='2'>

					<h1>
						<a href='".site_url("revise/$device_ID#$pin_ID")."' style='text-decoration: none;'>".$projectData->getInfo('project_name')." - ".$pageData->getInfo('page_name')."</a>
					</h1>

				</td>
			</tr>

			<tr>
				<td width='50'>$pinShape</td>
				<td>

					$pin_message. <br>
					<a href='".site_url("revise/$device_ID#$pin_ID")."'><b>View Comments</b></a>

				</td>
			</tr>


			<tr>
				<td colspan='2'>

					<br>
					<h2>Comments ".($comment_count > 0 && 2 == 3 ? "<span style='display: inline-block; font-size: 10px; color: white; padding: 0 2px; background-color: red; border-radius: 3px;'>NEW</span>" : "")."</h2>
					$commentsList

				</td>
			</tr>


			<tr>
				<td colspan='2'>

					<br>
					<h2>Modifications</h2>
					<ul>
						".($modification_type == "image" && $modification != null ? "<li>Image updated</li>" : "")."
						".($modification_type == "html" && $modification != null ? "<li>Some content changes</li>" : "")."
						".($css != null ? "<li>Some view changes</li>" : "")."
						".($modification == null && $css == null ? "<li>No changes</li>" : "")."
					</ul>


				</td>
			</tr>

			$screenshots

		</table>


		", 'subject' => "$pin_message on ".$pageData->getInfo('page_name')."[".$projectData->getInfo('project_name')."] page"];

	}

}