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



// Prepare the CSS code to save
$css_code = "";
foreach ($css as $key => $value) {


	// Key checks
	if (
		$key != "display"
		&& $key != "opacity"
	) return;


	// Value checks
	if (

		// Display rules
		($key == "display" && $value != "block" && $value != "none")

		// Opacity rules
		|| ($key == "opacity" && (!is_numeric($value) || $value > 1 || $value < 0) )

	) return;


	// If display == block, skip this
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
