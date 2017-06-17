<?php

$users = $db->get('test_table', null, ['test_id', 'test_column']);


echo "<pre>";
print_r($users);
echo "</pre>";