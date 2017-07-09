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


		// If no problem, DB Update
		if ($status != 'fail') {

			// Delete the old record
			$db->where('sort_type', $data['type']);
			$db->where('sort_object_ID', $data['ID']);
			$db->where('sorter_user_ID', currentUserID());
			$db->delete('sorting');


			// Add the new record
			$data = Array (
				"sort_type" => $data['type'],
				"sort_object_ID" => $data['ID'],
				"sort_number" => $data['order'],
				"sorter_user_ID" => currentUserID()
			);
			$id = $db->insert('sorting', $data);
			if ($id) $status = "success";

		}


	}

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
