<?php

function categorize($objects, $dataType, $prepared = false) {
	global $db, $thePreparedData, $versions, $devices, $catFilter;


	// Bring the devices data for pages
	if ($dataType == "page") {


		$page_IDs = array_unique(array_column($objects, 'page_ID'));
		//die_to_print($page_IDs);


		// If no pages in it
		if ( is_array($page_IDs) && count($page_IDs) == 0 ) return array();


		// All my versions
		$db->where('page_ID', $page_IDs, 'IN');
		$versions = $db->get('versions');


		// All my devices
		$devices = Device::ID()->getDevices($page_IDs);



		// PREPARE VERSIONS WITH DEVICES
		$versionsWithDevices = [];
		foreach ($versions as $version) {

			$versionsWithDevices[ $version["version_ID"] ] = $version;
			$versionsWithDevices[ $version["version_ID"] ]['devicesData'] = array();


			// Extract this page's devices
			$pageDevices = array_filter($devices, function ($device) use ($version) {
			    return ($device['version_ID'] == $version['version_ID']);
			});


			// Import the page devices
			if (is_array($pageDevices) && count($pageDevices) > 0)
				$versionsWithDevices[ $version["version_ID"] ]['devicesData'] = $pageDevices;

		}


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
			$thePreparedData[ $object["page_ID"] ]['versionsData'] = array();


			// Extract this page's versions
			$pageVersions = array_filter($versionsWithDevices, function ($version) use ($object) {
			    return ($version['page_ID'] == $object['page_ID']);
			});
			$pageVersions = array_values($pageVersions);


			// Import the page versions
			if (is_array($pageVersions) && count($pageVersions) > 0)
				$thePreparedData[ $object["page_ID"] ]['versionsData'] = $pageVersions;


		}


	}



	if ($prepared) return $thePreparedData;


	// Categorize the data
	$theData = [];
	foreach ($thePreparedData as $object) {

		// Don't categorize if in Archives or Deletes pages
		if ( $object['cat_ID'] == null || $catFilter == "archived" || $catFilter == "deleted" ) $object['cat_ID'] = 0;

		// If not owned by a category, add it to Uncategorized
		if ( $object['cat_name'] == null ) $object['cat_name'] = 'Uncategorized';

		// Default order number
		if ( $object['cat_order_number'] == null ) $object['cat_order_number'] = 0;

		// Default user ID ? !!!
		if ( $object['user_ID'] == null ) $object['user_ID'] = 0;


		if ( !isset($theData[$object['cat_ID']]['theData']) ) {

			$theData[$object['cat_ID']] = array(
				'cat_ID' => $object['cat_ID'],
				'cat_name' => $object['cat_name'],
				'cat_order_number' => $object['cat_order_number'],
				'sorter_user_ID' => $object['user_ID'],
				'theData' => array(),
			);

		}

		$theData[$object['cat_ID']]['theData'][$object[$dataType."_ID"]] = $object;


	}

	return $theData;

}