<?php

// GET RESPONSE CODE
function get_http_response_code($url) {

	$OriginalUserAgent = ini_get('user_agent');
	ini_set('user_agent', 'Mozilla/5.0');

	$headers = @get_headers(urldecode($url), 1);

	ini_set('user_agent', $OriginalUserAgent);


	if ($headers)
		return intval(substr($headers[0], 9, 3));
	else
		return 0;
}