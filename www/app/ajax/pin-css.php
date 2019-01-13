<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$pin_ID = request('pin_ID');
$css = request('css');



// Are they numbers?
if ( !is_numeric($pin_ID) || !is_array($css) ) return;



/*
die(json_encode(array(
  'data' => 'HEYY'
)));
*/




// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?




/*

			'display' 			: options.attr('data-display'),
			'opacity' 			: options.attr('data-opacity'),
			'text-align'		: options.attr('data-text-align'),
			'text-decoration'	: options.attr('data-text-decoration'),
			'font-weight'		: options.attr('data-font-weight'),
			'font-style'		: options.attr('data-font-style')

*/



// Prepare the CSS code to save
$css_code = "";
foreach ($css as $key => $value) {


	// Key checks
	if (
		$key != "display"
		&& $key != "opacity"
		&& $key != "text-align"
		&& $key != "text-decoration-line"
		&& $key != "font-weight"
		&& $key != "font-style"
	) return;


	// Value checks
	if (

		// Display rules
		($key == "display" && $value != "block" && $value != "none")

		// Opacity rules
		|| ($key == "opacity" && (!is_numeric($value) || $value > 1 || $value < 0) )

		// Text Align rules
		|| ($key == "text-align" && $value != "left" && $value != "center" && $value != "justify" && $value != "right")

		// Text Decoration rules
		|| ($key == "text-decoration-line" && $value != "underline" && $value != "none")

		// Font Weight rules
		|| ($key == "font-weight" && (!is_numeric($value) && $value != "bold" && $value != "normal") )

		// Font style rules
		|| ($key == "font-style" && $value != "italic" && $value != "normal")

	) return;


	// Skip if display is block
	if ($key == "display" && $value == "block") continue;


	// Add the code
	$css_code .= "$key: $value; ";


}


// If the code output is empty
if (empty($css_code)) $css_code = null;



// Update the pin
$pin_updated = Pin::ID($pin_ID)->updateCSS($css_code);

if ($pin_updated) $status = "Pin Updated: $pin_ID";
else $status = "error";



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'css' => print_r($css, true),
	'css_code' => $css_code,
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

die(json_encode(array(
  'data' => $data
)));
