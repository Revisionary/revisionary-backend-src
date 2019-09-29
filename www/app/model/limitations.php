<?php

$limit_user_ID = currentUserID();
//$limit_user_ID = 1;
$limitUser = User::ID($limit_user_ID);


// PROJECTS
$maxProjects = getUserInfo($limit_user_ID)['userLevelMaxProject'];
$myProjects = array_filter($limitUser->getProjects(), function($projectFound) use ($limit_user_ID) {
	return $projectFound['user_ID'] == $limit_user_ID;
});
$projectsCount = count( $myProjects );
$projectsPercentage = intval((100 * $projectsCount) / $maxProjects);
$maxProjects = $maxProjects == 99999 ? "∞" : $maxProjects;


// PAGES (PHASES)
$maxPhases = getUserInfo($limit_user_ID)['userLevelMaxPage'];
$myPhases = array_filter($limitUser->getPhases(), function($phaseFound) use ($limit_user_ID) {
	return $phaseFound['user_ID'] == $limit_user_ID;
});
$phasesCount = count( $myPhases );
$phasesPercentage = intval((100 * $phasesCount) / $maxPhases);
$maxPhases = $maxPhases == 99999 ? "∞" : $maxPhases;


// PINS
$maxPins = getUserInfo($limit_user_ID)['userLevelMaxLivePin'];
$myPins = array_filter($limitUser->getPins(), function($pinFound) use ($limit_user_ID) {
	return $pinFound['user_ID'] == $limit_user_ID && $pinFound['pin_type'] == 'live';
});
$pinsCount = count( $myPins );
$pinsPercentage = intval((100 * $pinsCount) / $maxPins);
$maxPins = $maxPins == 99999 ? "∞" : $maxPins;


// LOAD
$maxLoad = getUserInfo($limit_user_ID)['userLevelMaxLoad'];
$cached_userLoad = $cache->get('userload:'.$limit_user_ID);
if ( $cached_userLoad !== false ) $loadCount = $cached_userLoad;
else {

	$filesLoadMb = 0;
	foreach ($myPhases as $phaseFound) {

		$phaseDirectory = Phase::ID($phaseFound['phase_ID'])->phaseDir;

		$sizeMb = getDirectorySize($phaseDirectory, true); // True for the MB conversion
		$filesLoadMb += $sizeMb;

	}
	$loadCount = $filesLoadMb;

	// Set the cache
	$cache->set('userload:'.$limit_user_ID, $loadCount);

}
$loadPercentage = intval((100 * $loadCount) / $maxLoad);
$maxLoad = $maxLoad == 99999 ? "∞" : $maxLoad;

?>