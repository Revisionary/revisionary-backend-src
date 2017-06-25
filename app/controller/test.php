<pre>
<?php

$catFilter = "";

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
				$db->where('page_archived', ($catFilter == "archived" ? 1 : 0));


/*
				// Exclude other categories
				if ($pageCategory['cat_name'] != "Uncategorized")
					$db->where('cat.cat_name', $pageCategory['cat_name']);
				else
					$db->where('cat.cat_name IS NULL');
*/


				// Bring the order info
				$db->join("sorting o", "p.page_ID = o.sort_object_ID", "LEFT");
				$db->joinWhere("sorting o", "o.sort_type", "page");
				$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


				// Sorting !!! - Order options will be applied
				$db->orderBy("share_ID", "desc");
				$db->orderBy("cat_name", "asc");
				$db->orderBy("page_name", "asc");


/*
				// Order Projects !!!
				if ($order == "name") $db->orderBy("project_name", "asc");
				if ($order == "date") $db->orderBy("project_created", "asc");
*/


				$pages = $db->get('pages p', null, '');


print_r($pages);

?>
</pre>