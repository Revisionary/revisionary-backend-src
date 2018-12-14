<?php

function die_to_print($array) {
	die( "<pre>".print_r($array, true)."</pre>" );
}
