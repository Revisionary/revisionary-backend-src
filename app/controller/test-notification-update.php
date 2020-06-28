<pre>
<?php

$notifications = $db->get('notifications');
//print_r($notifications);


foreach($notifications as $n) {

    $db->where ('notification_ID', $n['notification_ID']);

    print_r($n);
    if ($n['object_type'] == "project")
        $db->update('notifications', array("project_ID" => $n['object_ID']));

    //print_r($n);
    elseif ($n['object_type'] == "page")
        $db->update('notifications', array("page_ID" => $n['object_ID']));

    //print_r($n);
    elseif ($n['object_type'] == "pin")
        $db->update('notifications', array("pin_ID" => $n['object_ID']));

}