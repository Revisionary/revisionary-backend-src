<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$pin_ID = request('pin_ID');
$css = request('css');



// Are they numbers?
if ( !is_numeric($pin_ID) || !is_array($css) ) return;



// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?



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
		&& $key != "color"
	) return;


	// Value checks
	if (

		// Display rules
		($key == "display" && !is_string($value))

		// Opacity rules
		|| ($key == "opacity" && (!is_numeric($value) || $value > 1 || $value < 0) )

		// Text Align rules
		|| ($key == "text-align" && !is_string($value))

		// Text Decoration rules
		|| ($key == "text-decoration-line" && !is_string($value))

		// Font Weight rules ??!!!
		|| ($key == "font-weight"
			&& $value != "100"
			&& $value != "200"
			&& $value != "300"
			&& $value != "400"
			&& $value != "500"
			&& $value != "600"
			&& $value != "700"
			&& $value != "800"
			&& $value != "900"
			&& $value != "bold"
			&& $value != "bolder"
			&& $value != "light"
			&& $value != "lighter"
			&& $value != "normal"
			&& $value != "unset"
			&& $value != "inherit"
			&& $value != "initial"
		)

		// Font style rules
		|| ($key == "font-style" && !is_string($value))

		// Color rules
		|| ($key == "color" && (!is_string($value) || !preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $value) ) )

	) return;


	// Skip other values
	if (

		// Display rules
		($key == "display" && $value != "none")

		// Text Align rules
		|| ($key == "text-align" && $value != "left" && $value != "center" && $value != "justify" && $value != "right")

		// Text Decoration rules
		|| ($key == "text-decoration-line" && $value != "underline" && $value != "none")

		// Font Weight rules
		|| ($key == "font-weight" && $value != "bold" && $value != "normal")

		// Font style rules
		|| ($key == "font-style" && $value != "italic" && $value != "normal")

		// Color rules
		|| ($key == "color" && !is_string($value))

	) continue;


	// Add the code
	$css_code .= "$key: $value !important; ";


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
