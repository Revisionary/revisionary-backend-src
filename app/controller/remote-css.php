<?php

	$css = "";

	$remote_url = urldecode($_GET['file']); // CSS Url

	// Check the url
	if ( get_http_response_code($remote_url) == "200" )
    	$css .= file_get_contents($remote_url); // CSS DATA


	//$css = filter_css($css);


	header('Content-type: text/css');
	echo $css;
	die();

?>