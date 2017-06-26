<?php

class Device {


	// The device ID
	public static $deviceId;

	// The project name
	public $deviceName;



	// SETTERS:

	public function __construct() {

		// Set the project name
        $this->deviceName = $this->getDeviceInfo('device_name');

    }


	// ID Setter
    public static function ID($deviceId) {

	    // Set the device ID
		self::$deviceId = $deviceId;
		return new static;

    }




	// GETTERS:

    // Get device info
    public function getDeviceInfo($column) {
	    global $db;

	    $db->where('device_ID', self::$deviceId);
	    $device = $db->getOne('devices', $column);
		if ($device)
			return $device[$column];

	    return false;
    }

}