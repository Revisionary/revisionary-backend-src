<?php

class Project {


	// The project ID
	public static $project_ID;

	// The project name
	public $projectName;

	// The project directory
	public $projectDir;

	// Current user ID
	public $user_ID;


	// Paths
	public $userPath;
	public $projectPath;



	// SETTERS:

	public function __construct() {

		// Set the project name
        $this->projectName = $this->getProjectInfo('project_name');

        // Set the user ID
        $this->user_ID = $this->getProjectInfo('user_ID');


		// Paths
        $userPath = $this->userPath = "user-".$this->user_ID;
        $projectPath = $this->projectPath = "project-".self::$project_ID;


        // Set the project directory
        $this->projectDir = dir."/assets/cache/".$userPath."/".$projectPath;

    }


	// ID Setter
    public static function ID($project_ID) {

	    // Set the project ID
		self::$project_ID = $project_ID;
		return new static;

    }




	// GETTERS:

    // Get project info
    public function getProjectInfo($column) {
	    global $db;

	    $db->where('project_ID', self::$project_ID);
	    $project = $db->getOne('projects', $column);
		if ($project)
			return $project[$column];

	    return false;
    }

}