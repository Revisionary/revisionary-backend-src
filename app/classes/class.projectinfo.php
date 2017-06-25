<?php

class Project {


	// The project ID
	public static $projectId;

	// The project name
	public $projectName;



	// SETTERS:

	public function __construct($projectId) {


		// Set the project ID
		self::$projectId = $projectId;


		// Set the project ID
        $this->projectName = $this->getProjectInfo('project_name');

    }


	// ID Setter
    public static function ID($projectId) {

	    // Set the project ID
		if ( is_null( self::$projectId ) ) {
			self::$projectId = new self($projectId);
		}
		return self::$projectId;

    }




	// GETTERS:

    // Get project info
    public function getProjectInfo($col) {
	    global $db;

	    $db->where('project_ID', self::$projectId);
	    $project = $db->getOne('projects', $col);

	    return $project[$col];
    }



}