<?php

function userloggedIn() {
	return isset($_SESSION['user_ID']) ? true : false;
}

function currentUserID() {
	return userloggedIn() ? $_SESSION['user_ID'] : "guest";
}