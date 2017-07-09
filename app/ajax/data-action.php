<?php

$action = post('action');
$status = "initiated";

if ($action == "reorder") {

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


		// DB Checks !!!


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

}



// CREATE THE RESPONSE
$data = array(
	'action' => $action,

	'status' => $status,

	'data' => $orderData

);

echo json_encode(array(
  'data' => $data
));
