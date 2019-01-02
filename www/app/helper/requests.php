<?php

// REQUESTS

function get($name, $noFilter = false) {

	return filterRequest($_GET, $name);

}

function post($name, $noFilter = false) {

	return filterRequest($_POST, $name);

}

function request($name, $noFilter = false) {

	return filterRequest($_REQUEST, $name);

}