<?php

// PROJECTS
$maxProjects = getUserInfo()['userLevelMaxProject'];
$myProjects = array_filter($User->getProjects(), function($projectFound) {
	return $projectFound['user_ID'] == currentUserID();
});
$projectsCount = count( $myProjects );
$projectsPercentage = intval((100 * $projectsCount) / $maxProjects);
$maxProjects = $maxProjects == 99999 ? "∞" : $maxProjects;


// PAGES (PHASES)
$maxPhases = getUserInfo()['userLevelMaxPage'];
$myPhases = array_filter($User->getPhases(), function($phaseFound) {
	return $phaseFound['user_ID'] == currentUserID();
});
$phasesCount = count( $myPhases );
$phasesPercentage = intval((100 * $phasesCount) / $maxPhases);
$maxPhases = $maxPhases == 99999 ? "∞" : $maxPhases;


// PINS
$maxPins = getUserInfo()['userLevelMaxLivePin'];
$myPins = array_filter($User->getPins(), function($pinFound) {
	return $pinFound['user_ID'] == currentUserID() && $pinFound['pin_type'] == 'live';
});
$pinsCount = count( $myPins );
$pinsPercentage = intval((100 * $pinsCount) / $maxPins);
$maxPins = $maxPins == 99999 ? "∞" : $maxPins;


// LOAD !!! CACHE THIS
$maxLoad = getUserInfo()['userLevelMaxLoad'];


// CHECK THE CACHE FIRST
$cached_userLoad = $cache->get('userload:'.currentUserID());
if ( $cached_userLoad !== false ) $loadCount = $cached_userLoad;
else {

	$filesLoadMb = 0;
	foreach ($myPhases as $phaseFound) {

		$phaseDirectory = Phase::ID($phaseFound['phase_ID'])->phaseDir;

		$sizeByte = getDirectorySize($phaseDirectory);
		$sizeMb = number_format($sizeByte / 1048576, 1);
		$filesLoadMb += $sizeMb;

	}
	$loadCount = $filesLoadMb;

	// Set the cache
	$cache->set('userload:'.currentUserID(), $loadCount);

}
$loadPercentage = intval((100 * $loadCount) / $maxLoad);
$maxLoad = $maxLoad == 99999 ? "∞" : $maxLoad;

?>