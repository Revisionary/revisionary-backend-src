<?php


// GET RESPONSE CODE
function get_http_response_code($url) {
	$headers = @get_headers(urldecode($url));

	if ($headers)
		return intval(substr($headers[0], 9, 3));
	else
		return 0;
}