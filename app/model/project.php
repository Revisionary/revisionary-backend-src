<?php

function the_data() {

	global $db, $project, $projectShares, $project_ID, $order, $catFilter, $deviceFilter;


	// CATEGORY QUERY
	$theData = array();


	// Exclude other category types
	$db->where('cat_type', $project_ID);

	// Exclude other users
	$db->where('cat_user_ID', currentUserID());


	// Bring the order info
	$db->join("sorting oc", "catt.cat_ID = oc.sort_object_ID", "LEFT");
	$db->joinWhere("sorting oc", "oc.sort_type", "category");
	$db->joinWhere("sorting oc", "oc.sorter_user_ID", currentUserID());


	// Default order
	if ($order == "") $db->orderBy("oc.sort_number", "asc");


	// Order Categories
	if ($order == "name") $db->orderBy("cat_name", "asc");
	if ($order == "date") $db->orderBy("cat_name", "asc");


	$pageCategories = $db->get('categories catt', null, '');


	// Add the uncategorized item
	array_unshift($pageCategories , array(
		'cat_ID' => 0,
		'cat_name' => 'Uncategorized',
		'sort_number' => 0,
		'pageData' => array()
	));


	// THE PAGE CATEGORY LOOP
	$page_count = 0;
	foreach ($pageCategories as $pageCategory) {

		// Category Filters
		if (
			$catFilter != "" &&
			$catFilter != "mine" &&
			$catFilter != "shared" &&
			$catFilter != "deleted" &&
			$catFilter != "archived" &&
			$catFilter != permalink($pageCategory['cat_name'])
		) continue;


		// Add the category data
		$theData[ $pageCategory['cat_ID'] ] = $pageCategory;
		$theData[ $pageCategory['cat_ID'] ]['pageData'] = array();





		// PAGES QUERY

		// Bring the shared ones
		$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "s.share_type", "page");
		$db->joinWhere("shares s", "s.share_to", currentUserID());


		// Bring the category connection
		$db->join("page_cat_connect cat_connect", "p.page_ID = cat_connect.page_cat_page_ID", "LEFT");
		$db->joinWhere("page_cat_connect cat_connect", "cat_connect.page_cat_connect_user_ID", currentUserID());


		// Bring the category info
		$db->join("categories cat", "cat_connect.page_cat_ID = cat.cat_ID", "LEFT");
		$db->joinWhere("categories cat", "cat.cat_user_ID", currentUserID());


		// Bring the archive info
		$db->join("archives arc", "arc.archived_object_ID = p.page_ID", "LEFT");
		$db->joinWhere("archives arc", "arc.archiver_user_ID", currentUserID());
		$db->joinWhere("archives arc", "arc.archive_type", "page");


		// Bring the delete info
		$db->join("deletes del", "del.deleted_object_ID = p.page_ID", "LEFT");
		$db->joinWhere("deletes del", "del.deleter_user_ID", currentUserID());
		$db->joinWhere("deletes del", "del.delete_type", "page");


		// Bring the devices
		$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");


		// Bring the device category info
		$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");


		// Mine and Shared Filters
		if ($catFilter == "mine")
			$db->where('user_ID = '.currentUserID());

		elseif ($catFilter == "shared")
			$db->where('user_ID != '.currentUserID());


		// If project is not belong to current user
		if ( $project['user_ID'] != currentUserID() ) {


			// Project is shared to current user
			$projectSharedID = array_search(currentUserID(), array_column($projectShares, 'share_to'));
			if (  $projectSharedID !== false ) {

				// Show everything belong to sharer
				$db->where('user_ID = '.$projectShares[$projectSharedID]['sharer_user_ID']);

			}


		} else { // If the project is current user's or shared to him

			// Show only current user's
			$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');

		}


		// Exclude deleted and archived
		$db->where('del.deleted_object_ID IS '.($catFilter == "deleted" ? 'NOT' : '').' NULL');
		if ($catFilter != "deleted")
			$db->where('arc.archived_object_ID IS '.($catFilter == "archived" ? 'NOT' : '').' NULL');


		// Exclude the other project pages
		$db->where('project_ID', $project_ID);


		// Device Filters - NO NEED FOR NOW - This filter works from view page, because of the available_devices data
		//if ($deviceFilter != "" && is_numeric($deviceFilter))
			//$db->where('d.device_cat_id', $deviceFilter);


		// Exclude the sub pages - NO NEED FOR NOW
		//if ($deviceFilter == "")
			//$db->where('parent_page_ID IS NULL');


		// Exclude other categories
		if ($pageCategory['cat_name'] != "Uncategorized")
			$db->where('cat.cat_name', $pageCategory['cat_name']);
		else
			$db->where('cat.cat_name IS NULL');


		// Bring the order info
		$db->join("sorting o", "p.page_ID = o.sort_object_ID", "LEFT");
		$db->joinWhere("sorting o", "o.sort_type", "page");
		$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


		// Sorting
		if ($order == "") $db->orderBy("o.sort_number", "asc");
		if ($order == "") $db->orderBy("share_ID", "desc");
		$db->orderBy("cat_name", "asc");
		$db->orderBy("page_name", "asc");


		// Order Pages
		if ($order == "name") $db->orderBy("page_name", "asc");
		if ($order == "date") $db->orderBy("page_created", "asc");


		$pages = $db->get('pages p', null, '');

		// THE PAGE LOOP
		// List the pages under the category
		foreach ($pages as $page) {


/*
			// If project is not mine or not shared to me
			$projectSharedID = array_search(currentUserID(), array_column($projectShares, 'share_to'));
			if (
				$project['user_ID'] != currentUserID() &&
				$projectSharedID === false
			) {

				if ($page['user_ID'] != currentUserID() && !$page['share_to'] != currentUserID()) continue;

			}
*/




			// Add the page data
			$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']] = $page;
			$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['subPageData'] = array();
			$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['parentPageData'] = array();


			// SUB PAGE QUERY

			// Check if other devices available
			$db->where('parent_page_ID', $page['page_ID']);

			// Bring the archive info
			$db->join("archives arc", "arc.archived_object_ID = p.page_ID", "LEFT");
			$db->joinWhere("archives arc", "arc.archiver_user_ID", currentUserID());
			$db->joinWhere("archives arc", "arc.archive_type", "page");

			// Bring the delete info
			$db->join("deletes del", "del.deleted_object_ID = p.page_ID", "LEFT");
			$db->joinWhere("deletes del", "del.deleter_user_ID", currentUserID());
			$db->joinWhere("deletes del", "del.delete_type", "page");

			// Exclude deleted and archived
			$db->where('del.deleted_object_ID IS '.($catFilter == "deleted" ? 'NOT' : '').' NULL');
			if ($catFilter != "deleted")
				$db->where('arc.archived_object_ID IS '.($catFilter == "archived" ? 'NOT' : '').' NULL');

			// Bring the devices
			$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");

			// Bring the device category info
			$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

			$subPages = $db->get('pages p');
			foreach ($subPages as $subPage) {

				$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['subPageData'][] = $subPage;

			}


			// PARENT PAGE QUERY

			// Check if other devices available
			$db->where('page_ID', $page['parent_page_ID']);

			// Bring the archive info
			$db->join("archives arc", "arc.archived_object_ID = p.page_ID", "LEFT");
			$db->joinWhere("archives arc", "arc.archiver_user_ID", currentUserID());
			$db->joinWhere("archives arc", "arc.archive_type", "page");

			// Bring the delete info
			$db->join("deletes del", "del.deleted_object_ID = p.page_ID", "LEFT");
			$db->joinWhere("deletes del", "del.deleter_user_ID", currentUserID());
			$db->joinWhere("deletes del", "del.delete_type", "page");

			// Exclude deleted and archived
			$db->where('del.deleted_object_ID IS '.($catFilter == "deleted" ? 'NOT' : '').' NULL');
			if ($catFilter != "deleted")
				$db->where('arc.archived_object_ID IS '.($catFilter == "archived" ? 'NOT' : '').' NULL');

			// Bring the devices
			$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");

			// Bring the device category info
			$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

			$parentPages = $db->get('pages p');
			foreach ($parentPages as $parentPage) {

				$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['parentPageData'][] = $parentPage;

			}


			$page_count++;

		} // END OF THE PAGE LOOP



	} // END OF THE CATEGORY LOOP

	return $theData;

}