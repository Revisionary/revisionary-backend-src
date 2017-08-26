<?php
	$url = "http://junipercleaning.com/wp-content/cache/minify/eae17.css";



	// For the SSL Problem
	$ContextOptions = array(
	    "ssl" => array(
	        "verify_peer" => false,
	        "verify_peer_name" => false,
	    ),
        "http" => array (
            "follow_location" => true, // follow redirects
            "user_agent" => "Mozilla/5.0"
        )
	);

	// Get the HTML
	$content = file_get_contents($url, FILE_TEXT/* , stream_context_create($ContextOptions) */);

?>

<pre>

	Response Code: <?=get_http_response_code($url)?>

	Content: <?=var_dump($content);?>

</pre>