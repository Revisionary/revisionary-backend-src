<?php

if (post('type')){
	if (file_exists(dir . '/app/ajax/' . post('type') . '.php')){
		require dir . '/app/ajax/' . post('type') . '.php';
	}
}