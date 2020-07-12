<pre>
<?php

$db->where('user_ID', NULL, 'IS');
$phases = $db->get('phases');
//print_r($phases);

foreach($phases as $i => $phase) {

    $phase_ID = $phase['phase_ID'];
    $page_ID = $phase['page_ID'];
    $pageData = Page::ID($page_ID);
    if (!$pageData) {
        echo "<b>$i - Page #$page_ID NOT FOUND <br><br><br><br>";
        continue;
    }
    $user_ID = $pageData->getInfo('user_ID');


    echo "$i - Phase #$phase_ID will be assigned to User #$user_ID -> <br>";


    $db->where('phase_ID', $phase_ID);
    $result = $db->update('phases', array(
        "user_ID" => $user_ID
    ));

    if (!$result) {
        echo "<b>NOT DONE</b> - ".$db->getLastError()." <br><br><br><br>";
    } else {
        echo "DONE <br><br>";
    }
}


