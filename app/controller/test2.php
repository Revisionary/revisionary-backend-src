<pre>
<?php

// Exclude other category types
$db->where('cat_type', 'page');

// Exclude other users
$db->where('cat_user_ID', currentUserID());


$pageCategories = $db->get('categories', null, '');


// Add the uncategorized item
array_unshift($pageCategories , array(
	'cat_ID' => 0,
	'cat_name' => 'Uncategorized'
));


print_r($pageCategories);

?>
</pre>