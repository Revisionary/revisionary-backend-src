<?php

// If already logged in, go projects page
if (userloggedIn()) {
	header('Location: '.site_url('projects'));
	die();
}

$additionalBodyJS = ['block-script.js'];

$page_title = "Signup - Revisionary App";
require view('signup');