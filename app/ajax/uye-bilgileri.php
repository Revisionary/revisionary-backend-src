<?php

$query = $db->select('users')
->run();

echo json_encode(array(
  'data' => $query
));
