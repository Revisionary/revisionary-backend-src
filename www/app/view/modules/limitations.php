<?php

// PROJECTS
$maxProjects = getUserInfo()['userLevelMaxProject'];
$allMyProjects = array_filter($User->getProjects(), function($projectFound) {
	return $projectFound['user_ID'] == currentUserID();
});
$projectsCount = count( $allMyProjects );
$projectsPercentage = intval((100 * $projectsCount) / $maxProjects);


// PAGES (PHASES)
$maxPhases = getUserInfo()['userLevelMaxPage'];
$allMyPhases = array_filter($User->getPhases(), function($phaseFound) {
	return $phaseFound['user_ID'] == currentUserID();
});
$phasesCount = count( $allMyPhases );
$phasesPercentage = intval((100 * $phasesCount) / $maxPhases);


// PINS
$maxPins = getUserInfo()['userLevelMaxLivePin'];
$allMyPins = array_filter($User->getPins(), function($pinFound) {
	return $pinFound['user_ID'] == currentUserID() && $pinFound['pin_type'] == 'live';
});
$pinsCount = count( $allMyPins );
$pinsPercentage = intval((100 * $pinsCount) / $maxPins);

?>


<div class="limit-wrapper">
	<div class="wrap xl-2 xl-table xl-middle xl-gutter-8">
		<div class="col xl-right" style="font-size: 12px; line-height: 12px;">
			<b><?=getUserInfo()['userLevelName']?></b><br>Account
		</div>
		<div class="col">


			<div class="dropdown">
				<a href="<?=site_url('upgrade')?>" class="dropdown-opener">
				
					<div class="limit-bar <?=$projectsPercentage >= 100 ? "exceed" : ""?>">
						<div class="current-status" style="width: <?=$projectsPercentage?>%;">
							<span class="percentage bottom-tooltip" data-tooltip="You have <?="$projectsCount project".($projectsCount > 1 ? "s" : "")?>"><?=$projectsPercentage?>%</span>
						</div>
						<div class="total">
							<?="$projectsCount/$maxProjects<br>Project".($maxProjects > 1 ? "s" : "")?>
						</div>
					</div>
				
				</a>
				<ul class="right">
					<li>
						<a href="#">

							<div class="limit-bar <?=$phasesPercentage >= 100 ? "exceed" : ""?>">
								<div class="current-status" style="width: <?=$phasesPercentage?>%;">
									<span class="percentage bottom-tooltip" data-tooltip="You have <?="$phasesCount page".($phasesCount > 1 ? "s" : "")?>"><?=$phasesPercentage?>%</span>
								</div>
								<div class="total">
									<?="$phasesCount/$maxPhases<br>Page".($maxPhases > 1 ? "s" : "")?>
								</div>
							</div>

						</a>
					</li>
					
					<li>
						<a href="#">

							<div class="limit-bar <?=$pinsPercentage >= 100 ? "exceed" : ""?>">
								<div class="current-status" style="width: <?=$pinsPercentage?>%;">
									<span class="percentage bottom-tooltip" data-tooltip="You have <?="$pinsCount pin".($pinsCount > 1 ? "s" : "")?>"><?=$pinsPercentage?>%</span>
								</div>
								<div class="total">
									<?="$pinsCount/$maxPins<br>Pin".($maxPins > 1 ? "s" : "")?>
								</div>
							</div>

						</a>
					</li>
					<li><a href="#" class="button">UPGRADE</a></li>
				</ul>
			</div>


		</div>
	</div>
</div>


<div class="xl-hidden"><b>Usage:</b> 8 MB of 25 MB (<?=getUserInfo()['userLevelName']?> Account)</div>