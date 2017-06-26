<pre>
<?php
$db->where("user_ID", 2);
$user = $db->get("users");
print_r($user);
?>
</pre>