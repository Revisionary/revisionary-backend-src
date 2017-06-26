<?php

class Project {


	// The project ID
	public static $projectId;

	// The project name
	public $projectName;



	// SETTERS:

	public function __construct() {

		// Set the project name
        $this->projectName = $this->getProjectInfo('project_name');

    }


	// ID Setter
    public static function ID($projectId) {

	    // Set the project ID
		self::$projectId = $projectId;
		return new static;

    }




	// GETTERS:

    // Get project info
    public function getProjectInfo($column) {
	    global $db;

	    $db->where('project_ID', self::$projectId);
	    $project = $db->getOne('projects', $column);
		if ($project)
			return $project[$column];

	    return false;
    }

}