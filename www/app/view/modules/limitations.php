<?php

$maxProjects = getUserInfo()['userLevelMaxProject'];
$allMyProjects = array_filter($User->getProjects(), function($projectFound) {
	return $projectFound['user_ID'] == currentUserID();
});
$projectsCount = count( $allMyProjects );
$projectsPercentage = intval((100 * $projectsCount) / $maxProjects);

?>


<div class="dropdown limit-wrapper <?=$projectsPercentage >= 100 ? "exceed" : ""?>" data-tooltip="In development...">
	<a href="<?=site_url('upgrade')?>" class="wrap xl-2 xl-table xl-middle xl-gutter-8">
		<div class="col xl-right" style="font-size: 12px; line-height: 12px;">
			<b><?=getUserInfo()['userLevelName']?></b><br>Account
		</div>
		<div class="col">

			<div class="limit-bar">
				<div class="current-status" style="width: <?=$projectsPercentage?>%;">
					<span class="percentage bottom-tooltip" data-tooltip="You have <?="$projectsCount project".($maxProjects > 1 ? "s" : "")?>"><?=$projectsPercentage?>%</span>
				</div>
				<div class="total" data-tooltip="Maximum <?="Project".($maxProjects > 1 ? "s" : "")?> Allowed">
					<?="$projectsCount/$maxProjects<br>Project".($maxProjects > 1 ? "s" : "")?>
				</div>
			</div>

		</div>
	</a>
</div>


<div class="xl-hidden"><b>Usage:</b> 8 MB of 25 MB (<?=getUserInfo()['userLevelName']?> Account)</div>