<?php

class Version {


	// The device ID
	public static $versionId;

	// The project name
	public $versionName;



	// SETTERS:

	public function __construct() {

		// Set the project name
        $this->versionName = $this->getInfo('version_name');

    }


	// ID Setter
    public static function ID($versionId) {

	    // Set the device ID
		self::$versionId = $versionId;
		return new static;

    }




	// GETTERS:

    // Get version info
    public function getInfo($column) {
	    global $db;

	    $db->where('version_ID', self::$versionId);
	    $version = $db->getOne('versions', $column);
		if ($version)
			return $version[$column];

	    return false;
    }

}