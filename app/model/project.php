<?php

function the_data() {

	global $db, $project_ID, $order, $catFilter, $deviceFilter;


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


		// Bring the devices
		$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");


		// Bring the device category info
		$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");


		// Filters
		if ($catFilter == "")
			$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
		elseif ($catFilter == "mine")
			$db->where('user_ID = '.currentUserID());
		elseif ($catFilter == "shared")
			$db->where('share_to = '.currentUserID());
		else
			$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');


		// Exclude deleted and archived
		$db->where('page_deleted', ($catFilter == "deleted" ? 1 : 0));
		if ($catFilter != "deleted")
			$db->where('page_archived', ($catFilter == "archived" ? 1 : 0));


		// Exclude the other project pages
		$db->where('project_ID', $project_ID);


		// Device Filters - Filter works from view page, because of the available_devices data
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
		$db->orderBy("share_ID", "desc");
		$db->orderBy("cat_name", "asc");
		$db->orderBy("page_name", "asc");


		// Order Pages
		if ($order == "name") $db->orderBy("page_name", "asc");
		if ($order == "date") $db->orderBy("page_created", "asc");


		$pages = $db->get('pages p', null, '');

		// THE PAGE LOOP
		// List the pages under the category
		foreach ($pages as $page) {

			// Add the page data
			$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']] = $page;
			$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['subPageData'] = array();


			// SUB PAGE QUERY

			// Check if other devices available
			$db->where('parent_page_ID', $page['page_ID']);

			// Exclude deleted and archived
			$db->where('page_deleted', ($catFilter == "deleted" ? 1 : 0));
			if ($catFilter != "deleted")
				$db->where('page_archived', ($catFilter == "archived" ? 1 : 0));

			// Bring the devices
			$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");

			// Bring the device category info
			$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

			$subPages = $db->get('pages p');
			foreach ($subPages as $subPage) {

				$theData[ $pageCategory['cat_ID'] ]['pageData'][$page['page_ID']]['subPageData'][] = $subPage;

			}


			$page_count++;

		} // END OF THE PAGE LOOP



	} // END OF THE CATEGORY LOOP

	return $theData;

}