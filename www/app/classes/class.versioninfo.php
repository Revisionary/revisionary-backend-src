<?php

class Version {


	// The version ID
	public static $version_ID;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($version_ID) {

	    // Set the device ID
		self::$version_ID = $version_ID;
		return new static;

    }




	// GETTERS:

    // Get version info
    public function getInfo($columns = null, $array = false) {
	    global $db;

	    $db->where('version_ID', self::$version_ID);

	    return $array ? $db->getOne("versions", $columns) : $db->getValue("versions", $columns);
    }

}