<?php

function the_data() {
	global $order, $catFilter, $deviceFilter, $dataType, $project_ID, $thePreparedData;


	// Get the current user projects with categories
	$objects = UserAccess::ID()->getMy($dataType."s", $catFilter, $order, $project_ID);

	//return $objects;


	// Prepare the data
	$thePreparedData = [];
	foreach ($objects as $object) {


		// Project data
		if ($dataType == "project")
			$thePreparedData[ $object["project_ID"] ] = $object;


		// Parent Page Data
		if ($dataType == "page" && $object['parent_page_ID'] == null && empty($deviceFilter)) {
			$thePreparedData[ $object["page_ID"] ] = $object;
			$thePreparedData[ $object["page_ID"] ]['subPageData'] = array();
		}


	}


	// Import the subpages
	if ($dataType == "page") {

		foreach ($objects as $object) {


			// Show the device pages separately when device filter selected
			if ( !empty($deviceFilter) ) {
				$thePreparedData[ $object['page_ID'] ] = $object;
				$thePreparedData[ $object['page_ID'] ]['subPageData'] = array();
			}


			// Sub Page Data
			if (
				$object['parent_page_ID'] != null
				&& empty($deviceFilter)
			) {


				// If no parent page data added, add the subpage data there
				if ( !isset($thePreparedData[ $object['parent_page_ID'] ]) ) {

					$thePreparedData[ $object['parent_page_ID'] ] = $object;
					$thePreparedData[ $object['parent_page_ID'] ]['subPageData'] = array();

				} else {

					$thePreparedData[ $object['parent_page_ID'] ]['subPageData'][] = $object;

				}


			}


		}

	}

	//return $thePreparedData;


	// Categorize the pages
	$theData = [];
	foreach ($thePreparedData as $object) {

		//echo "object: ";print_r($object);

		if ( $object['cat_ID'] == null ) $object['cat_ID'] = 0;
		if ( $object['cat_name'] == null ) $object['cat_name'] = 'Uncategorized';
		if ( $object['cat_type'] == null ) $object['cat_type'] = $dataType;
		if ( $object['cat_sort_number'] == null ) $object['cat_sort_number'] = 0;


		if ( !isset($theData[$object['cat_ID']]['theData']) ) {

			$theData[$object['cat_ID']] = array(
				'cat_ID' => $object['cat_ID'],
				'cat_name' => $object['cat_name'],
				'cat_type' => $object['cat_type'],
				'cat_user_ID' => $object['cat_user_ID'],
				'sort_ID' => $object['cat_sort_ID'],
				'sort_type' => $object['cat_sort_type'],
				'sort_object_ID' => $object['cat_sort_object_ID'],
				'sort_number' => $object['cat_sort_number'],
				'sorter_user_ID' => $object['cat_sorter_user_ID'],
				'theData' => array(),
			);

		}

		$theData[$object['cat_ID']]['theData'][$object[$dataType."_ID"]] = $object;


	}

	return $theData;

}


//echo "<pre>";
//print_r($projects);
//echo "</pre> *********************************************************** DATA:";

/*
echo "<pre>";
//print_r($theData);
echo "</pre> ***********************************************************";
//exit;
*/




/*
echo "<pre>";
print_r( the_data() );
echo "</pre>";
exit;
*/