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
		$dbData = array(
			"archive_type" => $type,
			"archived_object_ID" => request('id'),
			"archiver_user_ID" => currentUserID()
		);
		$id = $db->insert('archives', $dbData);
		if ($id) $status = "successful";


		// If a page is being archived, also recover the subpages as well
		if ($type == 'page' && request('subpages') != "") {

			$subPages = explode(',', request('subpages'));

			foreach($subPages as $subPage_ID) {

				// Add the new record
				$dbData = array(
					"archive_type" => $type,
					"archived_object_ID" => $subPage_ID,
					"archiver_user_ID" => currentUserID()
				);
				$id = $db->insert('archives', $dbData);
				if ($id) $status = "successful";

			}


		}


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



		// If a page is being deleted, also delete the subpages as well
		if ($type == 'page' && request('subpages') != "") {

			$subPages = explode(',', request('subpages'));

			foreach($subPages as $subPage_ID) {

				// Add the new record
				$dbData = Array (
					"delete_type" => $type,
					"deleted_object_ID" => $subPage_ID,
					"deleter_user_ID" => currentUserID()
				);
				$id = $db->insert('deletes', $dbData);
				if ($id) $status = "successful";

			}


		}


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


			// Remove from sorting
			$db->where('sort_type', 'category');
			$db->where('sort_object_ID', request('id'));
			$db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');


			// Remove the category
			$db->where('cat_ID', request('id'));
			$db->where('cat_user_ID', currentUserID());
			$removed = $db->delete('categories');
			if ( $removed ) $status = "successful";


		} elseif ($type == "project" || $type == "page") {

			// If project is removing
			if ($type == "project") {


				// Remove the project folder
				deleteDirectory( dir."/assets/cache/user-".currentUserID()."/project-".request('id')."/" );


				// Check all the categories belong to it
				$db->where('cat_type', request('id'));
				$db->where('cat_user_ID', currentUserID());
				$categories = $db->get('categories');


				// Remove all the categories
				foreach ($categories as $category) {

					// Remove from sorting
					$db->where('sort_type', 'category');
					$db->where('sort_object_ID', $category['cat_ID']);
					$db->where('sorter_user_ID', currentUserID());
					$db->delete('sorting');


					// Remove the category
					$db->where('cat_ID', $category['cat_ID']);
					$db->where('cat_user_ID', currentUserID());
					$db->delete('categories');

				}



				// Check all the pages belong to it
				$db->where('project_ID', request('id'));
				$pages = $db->get('pages');


				// Remove all the pages
				foreach ($pages as $page) {


					// Remove from archives
					$db->where('archive_type', 'page');
					$db->where('archived_object_ID', $page['page_ID']);
					$db->where('archiver_user_ID', currentUserID());
					$db->delete('archives');


					// Remove from deletes
					$db->where('delete_type', 'page');
					$db->where('deleted_object_ID', $page['page_ID']);
					$db->where('deleter_user_ID', currentUserID());
					$db->delete('deletes');


					// Remove from sorting
					$db->where('sort_type', 'page');
					$db->where('sort_object_ID', $page['page_ID']);
					$db->where('sorter_user_ID', currentUserID());
					$db->delete('sorting');


					// Remove from shares
					$db->where('share_type', 'page');
					$db->where('shared_object_ID', $page['page_ID']);
					$db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
					$db->delete('shares');


/*
					// Remove the item - !!! NO NEED BECAUSE OF CASCADING
					$db->where('page_ID', $page['page_ID']);
					$db->where('user_ID', currentUserID());
					$db->delete('pages');
*/


				}


			}

			// Remove the page folder
			if ($type == "page")
				deleteDirectory( dir."/assets/cache/user-".currentUserID()."/".Page::ID( request('id') )->projectPath."/".Page::ID( request('id') )->pagePath."/".Page::ID( request('id') )->devicePath."/" );


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


			// Remove from shares
			$db->where('share_type', $type);
			$db->where('shared_object_ID', request('id'));
			$db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
			$db->delete('shares');


			// Remove the item
			$db->where($type.'_ID', request('id'));
			$db->where('user_ID', currentUserID());
			$removed = $db->delete($type.'s');
			if ( $removed ) $status = "successful";



			// If a page is being deleted, also delete the subpages as well
			if ($type == 'page' && request('subpages') != "") {

				$subPages = explode(',', request('subpages'));

				foreach($subPages as $subPage_ID) {

					deleteDirectory( dir."/assets/cache/user-".currentUserID()."/".Page::ID( $subPage_ID )->projectPath."/".Page::ID( $subPage_ID )->pagePath."/".Page::ID( $subPage_ID )->devicePath."/" );

					// Remove from archives
					$db->where('archive_type', $type);
					$db->where('archived_object_ID', $subPage_ID);
					$db->where('archiver_user_ID', currentUserID());
					$db->delete('archives');


					// Remove from deletes
					$db->where('delete_type', $type);
					$db->where('deleted_object_ID', $subPage_ID);
					$db->where('deleter_user_ID', currentUserID());
					$db->delete('deletes');


					// Remove from sorting
					$db->where('sort_type', $type);
					$db->where('sort_object_ID', $subPage_ID);
					$db->where('sorter_user_ID', currentUserID());
					$db->delete('sorting');


					// Remove from shares
					$db->where('share_type', $type);
					$db->where('shared_object_ID', $subPage_ID);
					$db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
					$db->delete('shares');


					// Remove the item
					$db->where($type.'_ID', $subPage_ID);
					$db->where('user_ID', currentUserID());
					$removed = $db->delete($type.'s');
					if ( $removed ) $status = "successful";

				}


			}


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


		// If a page is being recovered, also recover the subpages as well
		if ($type == 'page' && request('subpages') != "") {

			$subPages = explode(',', request('subpages'));

			foreach($subPages as $subPage_ID) {

				// Delete the old record
				$db->where($recover_type.'_type', $type);
				$db->where($recover_type.'d_object_ID', $subPage_ID);
				$db->where($recover_type.'r_user_ID', currentUserID());
				$deleted = $db->delete($recover_type.'s');
				if ($deleted) $status = "successful";

			}


		}
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


// ADD NEW CATEGORY
if ( request('action') == "add-new-category") {

	$type = request('data-type');


	// Security Check
	if (
		$type != "project" &&
		$type != "page"
	) {
		$status = "fail";
	}


	// DB Checks !!! If exists...


	// If no problem, DB Update
	if ($status != "fail") {


		// Add a new category
		$dbData = Array(
			"cat_type" => $type,
			"cat_name" => 'Untitled',
			"cat_user_ID" => currentUserID()
		);

		if ( is_numeric( request('project_ID') ) ) // !!! Security Issue, check the ID from DB
			$dbData['cat_type'] = request('project_ID');

		$cat_ID = $db->insert('categories', $dbData);
		if ($cat_ID) $status = "successful";


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
