<?php

function the_data() {

	global $db, $order, $catFilter;


	// CATEGORY QUERY
	$theData = array();


	// Exclude other category types
	$db->where('cat_type', 'project');

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


	$projectCategories = $db->get('categories catt', null, '');


	// Add the uncategorized item
	array_unshift($projectCategories , array(
		'cat_ID' => 0,
		'cat_name' => 'Uncategorized',
		'projectData' => array()
	));


	// THE CATEGORY LOOP
	$project_count = 0;
	foreach ($projectCategories as $projectCategory) {

		// Filters
		if (
			$catFilter != "" &&
			$catFilter != "mine" &&
			$catFilter != "shared" &&
			$catFilter != "deleted" &&
			$catFilter != "archived" &&
			$catFilter != permalink($projectCategory['cat_name'])
		) continue;


		// Add the category data
		$theData[ $projectCategory['cat_ID'] ] = $projectCategory;
		$theData[ $projectCategory['cat_ID'] ]['projectData'] = array();





		// PROJECTS QUERY

		// Bring the shared ones
		$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "s.share_type", "project");
		$db->joinWhere("shares s", "s.share_to", currentUserID());


		// Bring the category connection
		$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_cat_project_ID", "LEFT");
		$db->joinWhere("project_cat_connect cat_connect", "cat_connect.project_cat_connect_user_ID", currentUserID());


		// Bring the category info
		$db->join("categories cat", "cat_connect.project_cat_ID = cat.cat_ID", "LEFT");
		$db->joinWhere("categories cat", "cat.cat_user_ID", currentUserID());


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
		$db->where('project_deleted', ($catFilter == "deleted" ? 1 : 0));
		$db->where('project_archived', ($catFilter == "archived" ? 1 : 0));


		// Exclude other categories
		if ($projectCategory['cat_name'] != "Uncategorized")
			$db->where('cat.cat_name', $projectCategory['cat_name']);
		else
			$db->where('cat.cat_name IS NULL');


		// Bring the order info
		$db->join("sorting o", "p.project_ID = o.sort_object_ID", "LEFT");
		$db->joinWhere("sorting o", "o.sort_type", "project");
		$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


		// Default Sorting
		if ($order == "") $db->orderBy("o.sort_number", "asc");
		$db->orderBy("share_ID", "desc");
		$db->orderBy("cat_name", "asc");
		$db->orderBy("project_name", "asc");


		// Order Projects
		if ($order == "name") $db->orderBy("project_name", "asc");
		if ($order == "date") $db->orderBy("project_created", "asc");


		$projects = $db->get('projects p', null, '');


		// THE PROJECT LOOP
		// List the projects under the category
		foreach ($projects as $project) {

			// Add the page data
			$theData[ $projectCategory['cat_ID'] ]['projectData'][] = $project;
			$project_count++;

		} // END OF THE PROJECT LOOP


	} // END OF THE CATEGORY LOOP

	return $theData;

}