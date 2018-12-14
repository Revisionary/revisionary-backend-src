<?php

function categorize($objects, $dataType, $prepared = false) {
	global $thePreparedData, $devices;


	// Bring the devices data for pages
	if ($dataType == "page") {

		$page_IDs = array_unique(array_column($objects, 'page_ID'));
		$devices = Device::ID()->getDevices($page_IDs);

	}


	// Prepare the data
	$thePreparedData = [];
	foreach ($objects as $object) {


		// Project data
		if ($dataType == "project")
			$thePreparedData[ $object["project_ID"] ] = $object;


		// Page Data
		if ($dataType == "page") {

			$thePreparedData[ $object["page_ID"] ] = $object;
			$thePreparedData[ $object["page_ID"] ]['devicesData'] = array();


			// Extract this page's devices
			$pageDevices = array_filter($devices, function ($device) use ($object) {
			    return ($device['page_ID'] == $object['page_ID']);
			});


			// Import the page devices
			if (is_array($pageDevices) && count($pageDevices) > 0)
				$thePreparedData[ $object["page_ID"] ]['devicesData'] = $pageDevices;


		}


	}


	//if ($prepared) return $thePreparedData;


	// Categorize the data
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
$url = 'https://www.bilaltas.net/resume/my-resume/';
$pathes = explode('/', trim(parse_url($url)['path'], '/'));
print_r( end($path) );
echo "</pre> ***********************************************************";
//exit;
*/




/*
echo "<pre>";
print_r( the_data() );
echo "</pre>";
exit;
*/