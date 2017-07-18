<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["js_nonce"] )
	return;


// ORDERING
if (request('action') == "reorder") {

	$orderData = $_POST['orderData'];

	foreach($orderData as $data) {

		// Security Check !!! Needs more: ID, Cat ID check from DB. Order number check?
		if (
			(
				$data['type'] != "category" &&
				$data['type'] != "project" &&
				$data['type'] != "page"
			) ||
			!is_numeric( intval($data['ID']) ) ||
			!is_numeric( intval($data['catID']) ) ||
			!is_numeric( intval($data['order']) )
		) {
			$status = "fail";
			break;
		}


		// DB Checks !!! If exists...


		// If no problem, DB Update
		if ($status != 'fail') {

			// Delete the old record
			$db->where('sort_type', $data['type']);
			$db->where('sort_object_ID', $data['ID']);
			$db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');


			// Add the new record
			$dbData = Array (
				"sort_type" => $data['type'],
				"sort_object_ID" => $data['ID'],
				"sort_number" => $data['order'],
				"sorter_user_ID" => currentUserID()
			);
			$id = $db->insert('sorting', $dbData);
			if ($id) $status = "ordering-successful";

		}

		if ($status == "ordering-successful") {

			if ($data['type'] == "page" || $data['type'] == "project") {

				// Delete the old record
				$db->where($data['type'].'_cat_'.$data['type'].'_ID', $data['ID']);
				$db->where($data['type'].'_cat_connect_user_ID', currentUserID());
				$db->delete($data['type'].'_cat_connect');


				// Add the new record
				$dbData = Array (
					$data['type']."_cat_".$data['type']."_ID" => $data['ID'],
					$data['type']."_cat_ID" => $data['catID'],
					$data['type']."_cat_connect_user_ID" => currentUserID()
				);
				$id_connect = $db->insert($data['type'].'_cat_connect', $dbData);
				if ($id_connect) $status = "category-successful";


			}

		}


	} // Loop


	if ($status == "ordering-successful" || $status == "category-successful") $status = "successful";


} // Re-Order


// ARCHIVE
if (request('action') == "archive") {

	$type = request('data-type');


	// Security Check
	if (
		(
			$type != "project" &&
			$type != "page"
		) ||
		!is_numeric( intval(request('id')) )
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != 'fail') {


		// Delete the old record
		$db->where('archive_type', $type);
		$db->where('archived_object_ID', request('id'));
		$db->where('archiver_user_ID', currentUserID());
		$db->delete('archives');


		// Add the new record
		$dbData = Array (
			"archive_type" => $type,
			"archived_object_ID" => request('id'),
			"archiver_user_ID" => currentUserID()
		);
		$id = $db->insert('archives', $dbData);
		if ($id) $status = "successful";


	}

	// Redirect if not ajax
	if ( request('ajax') != true ) {
		$_SESSION["js_nonce"] = null;

		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}

}


// DELETE
if (request('action') == "delete") {

	$type = request('data-type');


	// Security Check
	if (
		(
			$type != "project" &&
			$type != "page"
		) ||
		!is_numeric( intval(request('id')) )
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != 'fail') {

		// Delete the old record
		$db->where('delete_type', $type);
		$db->where('deleted_object_ID', request('id'));
		$db->where('deleter_user_ID', currentUserID());
		$db->delete('deletes');


		// Add the new record
		$dbData = Array (
			"delete_type" => $type,
			"deleted_object_ID" => request('id'),
			"deleter_user_ID" => currentUserID()
		);
		$id = $db->insert('deletes', $dbData);
		if ($id) $status = "successful";


	}

	// Redirect if not ajax
	if ( request('ajax') != true ) {
		$_SESSION["js_nonce"] = null;

		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}

}


// REMOVE
if (request('action') == "remove") {

	$type = request('data-type');


	// Security Check
	if (
		(
			$type != "category" &&
			$type != "project" &&
			$type != "page"
		) ||
		!is_numeric( intval(request('id')) )
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != 'fail') {


		// Category Remove
		if ($type == "category") {

			$db->where('cat_ID', request('id'));
			$db->where('cat_user_ID', currentUserID());
			$removed = $db->delete('categories');
			if ( $removed ) $status = "successful";

		} elseif ($type == "project" || $type == "page") {

			// Remove the folder
			if ($type == "project")
				deleteDirectory( dir."/assets/cache/user-".currentUserID()."/project-".request('id')."/" );

			if ($type == "page")
				deleteDirectory( Page::ID( request('id') )->pageDir."/" );


			// Remove from archives
			$db->where('archive_type', $type);
			$db->where('archived_object_ID', request('id'));
			$db->where('archiver_user_ID', currentUserID());
			$db->delete('archives');


			// Remove from deletes
			$db->where('delete_type', $type);
			$db->where('deleted_object_ID', request('id'));
			$db->where('deleter_user_ID', currentUserID());
			$db->delete('deletes');


			// Remove from sorting
			$db->where('sort_type', $type);
			$db->where('sort_object_ID', request('id'));
			$db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');


			$db->where($type.'_ID', request('id'));
			$db->where('user_ID', currentUserID());
			$removed = $db->delete($type.'s');
			if ( $removed ) $status = "successful";

		}

	}

	// Redirect if not ajax
	if ( request('ajax') != true ) {
		$_SESSION["js_nonce"] = null;

		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}

}


// RECOVER
if ( substr(request('action'), 0, 8) == "recover-") {

	$recover_type = substr(request('action'), 8, -1);

	$type = request('data-type');


	// Security Check
	if (
		(
			$type != "project" &&
			$type != "page"
		) ||
		(
			$recover_type != "archive" &&
			$recover_type != "delete"
		) ||
		!is_numeric( intval(request('id')) )
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != 'fail') {

		// Delete the old record
		$db->where($recover_type.'_type', $type);
		$db->where($recover_type.'d_object_ID', request('id'));
		$db->where($recover_type.'r_user_ID', currentUserID());
		$deleted = $db->delete($recover_type.'s');
		if ($deleted) $status = "successful";
	}

	// Redirect if not ajax
	if ( request('ajax') != true ) {
		$_SESSION["js_nonce"] = null;

		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}

}


// RENAME
if ( request('action') == "rename") {

	$type = request('data-type');


	// Security Check
	if (
		(
			$type != "category" &&
			$type != "project" &&
			$type != "page"
		) ||
		!is_numeric( intval(request('id')) )
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != "fail") {

		// Category Name Update
		if ($type == "category") {

			$db->where('cat_ID', request('id'));
			$db->where('cat_user_ID', currentUserID());
			$updated = $db->update ('categories', array( 'cat_name' => request('inputText') ));
			if ( $updated ) $status = "successful";

		} elseif ($type == "project" || $type == "page") {

			$db->where($type.'_ID', request('id'));
			$db->where('user_ID', currentUserID());
			$updated = $db->update ($type.'s', array( $type.'_name' => request('inputText') ));
			if ( $updated ) $status = "successful";

		}

	}


	// Redirect if not ajax
	if ( request('ajax') != true ) {
		$_SESSION["js_nonce"] = null;

		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}

}



// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'action' => request('action'),
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['js_nonce'],
	//'data' => $orderData

);

echo json_encode(array(
  'data' => $data
));
