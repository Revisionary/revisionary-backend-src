<?php

function userloggedIn() {
	return isset($_SESSION['user_ID']) ? true : false;
}