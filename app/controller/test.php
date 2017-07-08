<pre>
<?php


$parsed = current_url($query = "test=asd&asd");

$parsed = removeQueryArg("test", $parsed);


print_r( $parsed );

?>
</pre>