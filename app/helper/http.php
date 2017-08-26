<?php

// GET RESPONSE CODE
function get_http_response_code($url) {

	$response = "000";

	$OriginalUserAgent = ini_get('user_agent');
	ini_set('user_agent', 'Mozilla/5.0');

	$headers = @get_headers(urldecode($url), 1);

	ini_set('user_agent', $OriginalUserAgent);

	$response = intval(substr($headers[0], 9, 3));


	// If fails, try without context
	if ( $response != 200 ) {

		$headers = @get_headers(urldecode($url), 1);
		$response = intval(substr($headers[0], 9, 3));

	}



	if ($headers)
		return $response;
	else
		return false;
}