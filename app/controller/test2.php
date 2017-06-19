<pre>
<?php

/*
// Bring the shared ones
$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
$db->joinWhere("shares s", "s.share_type", "project");
$db->joinWhere("shares s", "s.share_to", currentUserID());


// Bring the category connection
$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_cat_project_ID", "LEFT");
$db->joinWhere("project_cat_connect cat_connect", "cat_connect.project_cat_connect_user_ID", currentUserID());


// Bring the category info
$db->join("project_categories category", "cat_connect.project_cat_ID = category.project_cat_ID", "LEFT");
$db->joinWhere("project_categories category", "category.project_cat_user_ID", currentUserID());


// Bring the order info !!! Use this later
$db->join("sorting o", "p.project_ID = o.sort_object_ID", "LEFT");
$db->joinWhere("sorting o", "o.sort_type", "project");
$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


// Exclude other users
$db->where('user_ID', currentUserID());
$db->orWhere('share_to', currentUserID());


// Exclude deleted and archived
$db->where('project_deleted', 0);
$db->where('project_archived', 0);


// Sorting
$db->orderBy("share_ID", "desc");
$db->orderBy("project_cat_name", "asc");
$db->orderBy("project_name", "asc");
*/



// Exclude other category types
$db->where('cat_type', 'project');

// Exclude other users
$db->where('cat_user_ID', currentUserID());


$projectCategories = $db->get('categories p', null, '');


// Add the uncategorized item
array_unshift($projectCategories , array(
	'project_cat_ID' => 0,
	'project_cat_name' => 'Uncategorized'
));


print_r($projectCategories);

?>
</pre>