<?php

if (isset($_GET['revise']) ) {
	header('Location: '.site_url('revise/23423142'));
	die();
}

$page_title = "Revisionary App";
require view('index');