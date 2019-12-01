<?php require view('static/header_html'); ?>

<script>

	user_ID = <?=currentUserID()?>;
	user_first_name = '<?=$User->getInfo('user_first_name')?>';
	user_last_name = '<?=$User->getInfo('user_last_name')?>';
	user_picture = '<?=$User->getInfo('user_picture')?>';
	project_ID = <?=$project_ID?>;
	page_ID = <?=$page_ID?>;
	phase_ID = <?=$phase_ID?>;
	device_ID = <?=$device_ID?>;
	remote_URL = '<?=$phaseData->remoteUrl?>';

</script>



<div id="loading" class="overlay">

	<div class="progress-info">
		<ul>
			<li style="color: white;"><?=$phaseData->cachedUrl?></li>
		</ul>
	</div>


	<span class="loading-text">
		<div class="gps_ring"></div> <span id="loading-info">LOADING...</span>
	</span>


	<span class="dates">

		<?php
		$date_created = timeago($page['page_created'] );
		$last_updated = timeago($page['page_modified'] );

		echo "<div class='date created'><b>Date Created:</b> $date_created</div>";
		if ($date_created != $last_updated)
			echo "<div class='date updated'><b>Last Updated:</b> $last_updated</div>";
		?>

	</span>
</div>

<div id="wait" class="overlay" style="display: none;">

	<span class="loading-text">
		<div class="gps_ring"></div> <span id="loading-info">PLEASE WAIT...</span>
	</span>

</div>

<div class="bg-overlay modals">

	<?php

		$dataType = "page";
		require view('modules/modals');

	?>

</div>

<div class="alerts hidden">

	<div id="no-pin" class="alert error autoclose hidden">
		Pin does not exist anymore.
		<a href="#" class="close"><i class="fa fa-times"></i></a>
	</div>

</div>



<div id="top-bar">

	<div class="wrap xl-flexbox xl-between xl-bottom xl-center">
		<div class="col navigation">

			<div class="wrap xl-flexbox xl-bottom breadcrumbs">

				<div class="col account dropdown" style="margin-right: 15px;">

					<a href="#">
						<picture class="profile-picture white-border thin-border" <?=$userInfo['printPicture']?> data-type="user" data-id="<?=currentUserID()?>">
							<span><?=$userInfo['nameAbbr']?></span>
						</picture>
					</a>
					<ul class="user-menu xl-left">
						<li><a href="<?=site_url('projects', true)?>"><i class="fa fa-th"></i> All Projects</a></li>
						<li>
							<a href="#"><i class="fa fa-life-ring"></i> Help <i class="fa fa-caret-right"></i></a>
							<ul class="bottom-tooltip" data-tooltip="Coming soon...">
								<li><a href="#" data-modal="video">Quick Start</a></li>
								<li><a href="#" data-modal="video">Features Help</a></li>
								<li><a href="#" data-modal="video">Advanced Features</a></li>
								<li><a href="#" data-modal="video">Keyboard Shortcuts</a></li>
								<li><a href="#" data-modal="video">Integrations Help</a></li>
								<li><a href="#" data-modal="video">Feedback</a></li>
								<li><a href="#" data-modal="video">Contact Support</a></li>
							</ul>
						</li>
						<li><a href="<?=site_url('account', true)?>"><i class="fa fa-user-cog"></i> My Account</a></li>
						<li><a href="<?=site_url('logout', true)?>"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
					</ul>

				</div>

				<div class="col home xl-hidden">

					<a href="<?=site_url('projects', true)?>">
						<i class="fa fa-th"></i> All Projects
					</a>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>


				<div class="col projects">

					<div class="desc">Project</div>

					<span class="dropdown">
						<a href="<?=site_url('project/'.$project_ID, true)?>">
							<?=$project['project_name']?> <i class="fa fa-caret-down"></i>
						</a>
						<ul>
<?php
foreach ($allMyProjects as $myProject) {

	$selected = $myProject['project_ID'] == $project_ID ? "selected" : "";


	// Bring the pages in this project
	$pages_of_project = array_filter($allMyPages, function($pageFound) use ($myProject) {
	    return ($pageFound['project_ID'] == $myProject['project_ID']);
	});
	//die_to_print($pages_of_project, false);


	// Get pins count
	$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($myProject) {

		return $pinFound['project_ID'] == $myProject['project_ID'] && $pinFound['pin_complete'] == "0";

	}));
	$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($myProject) {

		return $pinFound['project_ID'] == $myProject['project_ID'] && $pinFound['pin_complete'] == "1";

	}));


	$action_url = 'ajax?type=data-action&data-type=project&nonce='.$_SESSION['js_nonce'].'&id='.$myProject['project_ID'];

?>
<li class="item <?=$selected?>" data-type="project" data-id="<?=$myProject['project_ID']?>">

	<a href="<?=site_url('project/'.$myProject['project_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$myProject['project_name']?>
			<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span> <?=count($pages_of_project) ? '<i class="fa fa-caret-right"></i>' : ''?></a>

	<?php
	if ( count($pages_of_project) ) {
	?>
	<ul>
		<?php
		foreach ($pages_of_project as $pageFromProject) {

			$selected = $pageFromProject['page_ID'] == $page_ID ? "selected" : "";


			// Get phases of the page
			$phases_of_page = array_filter($allMyPhases, function($phaseFound) use ($pageFromProject) {
			    return ($phaseFound['page_ID'] == $pageFromProject['page_ID']);
			});
			$phases_of_page = array_values($phases_of_page); // Reset the keys to get phase numbers
			//die_to_print($phases_of_page);


			// Get pins count
			$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($pageFromProject) {

				return $pinFound['page_ID'] == $pageFromProject['page_ID'] && $pinFound['pin_complete'] == "0";

			}));
			$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($pageFromProject) {

				return $pinFound['page_ID'] == $pageFromProject['page_ID'] && $pinFound['pin_complete'] == "1";

			}));


			// Get page status
			$pinStatus = "no-tasks";
			if ($inCompletePinCount)
				$pinStatus = "has-tasks";

			if ($completePinCount && !$inCompletePinCount)
				$pinStatus = "done";

			$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;


		?>
		<li class="item <?=$selected?>" data-type="page" data-id="<?=$pageFromProject['page_ID']?>" data-pin-status="<?=$pinStatus?>">
			<a href="<?=site_url('page/'.$pageFromProject['page_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$pageFromProject['page_name']?>
			<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span> <?=count($phases_of_page) > 1 ? '<i class="fa fa-caret-right"></i>' : ''?></a>

			<?php
			if ( count($phases_of_page) > 1 ) {
			?>
			<ul>
				<?php
				foreach ($phases_of_page as $phaseFromPage) {

					$selected = $phaseFromPage['phase_ID'] == $phase_ID ? "selected" : "";
					$phaseNumber = array_search($phaseFromPage, $phases_of_page) + 1;


					// Get devices of this phase
					$devices_of_phase = array_filter($allMyDevices, function($deviceFound) use ($phaseFromPage) {
					    return ($deviceFound['phase_ID'] == $phaseFromPage['phase_ID']);
					});
					//die_to_print($devices_of_phase);


					// Get pins count
					$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFromPage) {

						return $pinFound['phase_ID'] == $phaseFromPage['phase_ID'] && $pinFound['pin_complete'] == "0";

					}));
					$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFromPage) {

						return $pinFound['phase_ID'] == $phaseFromPage['phase_ID'] && $pinFound['pin_complete'] == "1";

					}));


					// Get page status
					$pinStatus = "no-tasks";
					if ($inCompletePinCount)
						$pinStatus = "has-tasks";

					if ($completePinCount && !$inCompletePinCount)
						$pinStatus = "done";

					$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;

				?>
				<li class="item <?=$selected?>" data-type="phase" data-id="<?=$phaseFromPage['phase_ID']?>" data-pin-status="<?=$pinStatus?>">
					<a href="<?=site_url('phase/'.$phaseFromPage['phase_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?>
					<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span></a>

					<?php
					if ( count($devices_of_phase) > 1 ) {
					?>
					<ul>
						<?php
						foreach ($devices_of_phase as $deviceFromPage) {

							$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";

							$deviceFromPage['screen_cat_name'] = $deviceFromPage['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPage['screen_cat_name'];
						?>
						<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
							<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'], true)?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
						</li>
						<?php
						}
						?>
					</ul>
					<?php
					}
					?>


				</li>
				<?php
				}
				?>
			</ul>
			<?php
			} else {
			?>


				<?php
				// Get devices of this phase
				$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageFromProject) {
				    return ($deviceFound['page_ID'] == $pageFromProject['page_ID']);
				});
				//die_to_print($devices_of_page);


				if ( count($devices_of_page) > 1 ) {
				?>
				<ul>
					<?php
					foreach ($devices_of_page as $deviceFromPage) {

						$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";

						$deviceFromPage['screen_cat_name'] = $deviceFromPage['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPage['screen_cat_name'];
					?>
					<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
						<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'], true)?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
					</li>
					<?php
					}
					?>
				</ul>
				<?php
				}
				?>


			<?php
			}
			?>

		</li>
		<?php
		}
		?>
	</ul>
	<?php
	}
	?>

	<?php
	if ($project['project_ID'] != $project_ID && 2 != 2) {
	?>
	<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=delete', true)?>" data-tooltip="Delete This Project" data-action="delete" data-confirm="Are you sure you want to delete this project?"></i>
	<?php
	}
	?>
</li>
<?php
}
?>
							<li>

							<?php if ($projectsPercentage >= 100) { ?>
								<a href="<?=site_url("upgrade")?>" data-modal="upgrade"><i class="fa fa-exclamation-circle"></i> <b>Increase Projects Limit Now</b></a>	
							<?php } else { ?>
								<a href="#" data-modal="add-new" data-type="project" data-id="new"><i class="fa fa-plus"></i> <b>Add New Project</b></a>
							<?php } ?>
							
							</li>
						</ul>
					</span>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>


				<?php
				if (isset($page['cat_name']) && $page['cat_name'] != null) {
				?>
				<div class="col categories">

					<div class="desc">Category</div>

					<a href="<?=site_url('project/'.$project_ID.'/'.permalink($page['cat_name']), true)?>">
						<?=$page['cat_name']?>
					</a>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>
				<?php
				}
				?>

				<div class="col pages">

					<div class="desc">Page</div>

					<span class="dropdown">
						<a href="<?=site_url('project/'.$project_ID, true)?>">
							<?=$page['page_name']?> <i class="fa fa-caret-down"></i>
						</a>
						<ul>
<?php

// Find the other pages from this project
$other_pages = array_filter($allMyPages, function($pageFound) use ($project_ID) {
	return ($pageFound['project_ID'] == $project_ID);
});
//die_to_print($other_pages);


foreach ($other_pages as $pageOther) {

	$selected = $pageOther['page_ID'] == $page_ID ? "selected" : "";


	// Get phases of the page
	$phases_of_page = array_filter($allMyPhases, function($phaseFound) use ($pageOther) {
	    return ($phaseFound['page_ID'] == $pageOther['page_ID']);
	});
	$phases_of_page = array_values($phases_of_page); // Reset the keys to get phase numbers
	//die_to_print($phases_of_page);


	// Get pins count
	$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($pageOther) {

		return $pinFound['page_ID'] == $pageOther['page_ID'] && $pinFound['pin_complete'] == "0";

	}));
	$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($pageOther) {

		return $pinFound['page_ID'] == $pageOther['page_ID'] && $pinFound['pin_complete'] == "1";

	}));


	// Get page status
	$pinStatus = "no-tasks";
	if ($inCompletePinCount)
		$pinStatus = "has-tasks";

	if ($completePinCount && !$inCompletePinCount)
		$pinStatus = "done";

	$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;


	$action_url = 'ajax?type=data-action&data-type=page&nonce='.$_SESSION['js_nonce'].'&id='.$pageOther['page_ID'];

?>
<li class="item deletable <?=$selected?>" data-type="page" data-id="<?=$pageOther['page_ID']?>" data-pin-status="<?=$pinStatus?>">

	<a href="<?=site_url('page/'.$pageOther['page_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$pageOther['page_name']?>
	<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span></a>

	<?php
	if ( count($phases_of_page) > 1 ) {
	?>
	<ul>
		<?php
		foreach ($phases_of_page as $phaseFromPage) {

			$selected = $phaseFromPage['phase_ID'] == $phase_ID ? "selected" : "";
			$phaseNumber = array_search($phaseFromPage, $phases_of_page) + 1;


			// Get devices of this phase
			$devices_of_phase = array_filter($allMyDevices, function($deviceFound) use ($phaseFromPage) {
			    return ($deviceFound['phase_ID'] == $phaseFromPage['phase_ID']);
			});
			//die_to_print($devices_of_phase);


			// Get pins count
			$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFromPage) {

				return $pinFound['phase_ID'] == $phaseFromPage['phase_ID'] && $pinFound['pin_complete'] == "0";

			}));
			$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFromPage) {

				return $pinFound['phase_ID'] == $phaseFromPage['phase_ID'] && $pinFound['pin_complete'] == "1";

			}));


			// Get page status
			$pinStatus = "no-tasks";
			if ($inCompletePinCount)
				$pinStatus = "has-tasks";

			if ($completePinCount && !$inCompletePinCount)
				$pinStatus = "done";

			$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;

		?>
		<li class="item <?=$selected?>" data-type="phase" data-id="<?=$phaseFromPage['phase_ID']?>" data-pin-status="<?=$pinStatus?>">
			<a href="<?=site_url('phase/'.$phaseFromPage['phase_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?>
			<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span></a>

			<?php
			if ( count($devices_of_phase) > 1 ) {
			?>
			<ul>
				<?php
				foreach ($devices_of_phase as $deviceFromPage) {

					$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";

					$deviceFromPage['screen_cat_name'] = $deviceFromPage['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPage['screen_cat_name'];
				?>
				<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
					<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'], true)?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
				</li>
				<?php
				}
				?>
			</ul>
			<?php
			}
			?>


		</li>
		<?php
		}
		?>
	</ul>
<?php
} else {
?>


	<?php
	// Get devices of this phase
	$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageOther) {
	    return ($deviceFound['page_ID'] == $pageOther['page_ID']);
	});
	//die_to_print($devices_of_page);


	if ( count($devices_of_page) > 1 ) {
	?>
	<ul>
		<?php
		foreach ($devices_of_page as $deviceFromPage) {

			$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";

			$deviceFromPage['screen_cat_name'] = $deviceFromPage['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPage['screen_cat_name'];
		?>
		<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
			<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'], true)?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
		</li>
		<?php
		}
		?>
	</ul>
	<?php
	}
	?>


<?php
}
?>

	<?php
	if ($pageOther['page_ID'] != $page_ID) {
	?>
	<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=delete', true)?>" data-tooltip="Delete This Page" data-action="delete" data-confirm="Are you sure you want to delete this page?"></i>
	<?php
	}
	?>

</li>
<?php
}
?>
							<li>

							<?php if ($phasesPercentage >= 100) { ?>
								<a href="<?=site_url("upgrade")?>" data-modal="upgrade"><i class="fa fa-exclamation-circle"></i> <b>Increase Pages Limit Now</b></a>	
							<?php } else { ?>
								<a href="#" data-modal="add-new" data-object-name="<?=$project['project_name']?>" data-type="page" data-id="<?=$project['project_ID']?>"><i class="fa fa-plus"></i> <b>Add New Page</b></a>
							<?php } ?>

							</li>
						</ul>
					</span>

				</div>
			</div>

		</div>
		<div class="col screen">


			<div class="wrap xl-gutter-8">
				<div class="col phase">

					<div class="desc nomargin">Phase</div>
					<span class="dropdown">

					<?php

					// Find the other phases from this device
					$other_phases = array_filter($allMyPhases, function($phaseFound) use ($page_ID) {
						return $phaseFound['page_ID'] == $page_ID;
					});
					$other_phases = array_values($other_phases); // Reset the keys to get phase numbers
					//die_to_print($other_phases);

					$currentPhaseNumber = array_search($phase, $other_phases) + 1;

					?>

						<a href="#" class="button select-phase"><i class="fa fa-code-branch"></i> <span>v<?=$currentPhaseNumber?></span> <i class="fa fa-caret-down"></i></a>
						<ul class="xl-left">

							<?php
							foreach($other_phases as $phaseFound) {

								if ($phaseFound['phase_ID'] == $phase_ID) continue;
								$phaseNumber = array_search($phaseFound, $other_phases) + 1;


								// Devices of the phase
								$devices_of_phase = array_filter($allMyDevices, function($deviceFound) use ($phaseFound) {
								    return ($deviceFound['phase_ID'] == $phaseFound['phase_ID']);
								});
								//die_to_print($devices_of_phase, false);


								// Get pins count
								$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFound) {

									return $pinFound['phase_ID'] == $phaseFound['phase_ID'] && $pinFound['pin_complete'] == "0";

								}));
								$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($phaseFound) {

									return $pinFound['phase_ID'] == $phaseFound['phase_ID'] && $pinFound['pin_complete'] == "1";

								}));


								// Get page status
								$pinStatus = "no-tasks";
								if ($inCompletePinCount)
									$pinStatus = "has-tasks";

								if ($completePinCount && !$inCompletePinCount)
									$pinStatus = "done";

								$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;


								$action_url = 'ajax?type=data-action&data-type=phase&nonce='.$_SESSION['js_nonce'].'&id='.$phaseFound['phase_ID'];
							?>

							<li class="item deletable" data-type="phase" data-id="<?=$phaseFound['phase_ID']?>" data-pin-status="<?=$pinStatus?>">
								<a href="<?=site_url('phase/'.$phaseFound['phase_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?> (<?=timeago($phaseFound['phase_created'])?>)
								<span class="pin-count small remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count small done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span></a>

								<?php
								if ( count($devices_of_phase) > 1 ) {
								?>
								<ul>
									<?php
									foreach ($devices_of_phase as $deviceFromPhase) {

										$selected = $deviceFromPhase['device_ID'] == $device_ID ? "selected" : "";

										$deviceFromPhase['screen_cat_name'] = $deviceFromPhase['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPhase['screen_cat_name'];
									?>
									<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPhase['device_ID']?>">
										<a href="<?=site_url('revise/'.$deviceFromPhase['device_ID'], true)?>"><i class="fa <?=$deviceFromPhase['screen_cat_icon']?>"></i> <?=$deviceFromPhase['screen_cat_name']?></a>
									</li>
									<?php
									}
									?>
								</ul>
								<?php
								}
								?>

								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=remove', true)?>" data-tooltip="Delete This Phase" data-action="remove" data-confirm="Are you sure you want to remove this phase?"></i>

							</li>

							<?php
							}
							?>

							<li>

							<?php if ($phasesPercentage >= 100) { ?>
								<a href="<?=site_url("upgrade")?>" data-modal="upgrade"><i class="fa fa-exclamation-circle"></i> <b>Increase Pages/Phases Limit Now</b></a>	
							<?php } else { ?>
								<a href="<?=site_url("projects?new_phase=$page_ID&page_width=1440&page_height=774", true)?>" class="add-phase bottom-tooltip" data-tooltip="When you are done with the changes mentioned here, add new phase to download latest state of the page and revise again in a new scope."><i class="fa fa-plus"></i> <b>Add New Phase</b></a>
							<?php } ?>
						
							</li>
						</ul>
					</span>


				</div>
				<div class="col screen">

					<div class="desc nomargin">Screen Size</div>
					<span class="dropdown">

						<a href="#" class="button select-screen"><i class="fa <?=$screenIcon?>"></i> <span><?=$screen_name?> (<?=$width?>x<?=$height?>)</span>  <i class="fa fa-caret-down"></i></a>
						<ul class="xl-left">
						<?php

						// EXISTING DEVICES
						$devices_of_mypage = array_filter($allMyDevices, function($deviceFound) use ($phase_ID) {
						    return ($deviceFound['phase_ID'] == $phase_ID);
						});
						foreach ($devices_of_mypage as $device) {
							//if ($device['device_ID'] == $device_ID) continue;
							$selected = $device['device_ID'] == $device_ID ? "selected" : "";



							$existing_screen_width = $device['screen_width'];
							$existing_screen_height = $device['screen_height'];

							$page_width = $device['device_width'];
							$page_height = $device['device_height'];

							if ($page_width != null && $page_width != null) {
								$existing_screen_width = $page_width;
								$existing_screen_height = $page_height;
							}


							$action_url = 'ajax?type=data-action&data-type=device&nonce='.$_SESSION['js_nonce'].'&id='.$device['device_ID'];

						?>

							<li class="item deletable screen-registered <?=$selected?>" data-type="device" data-id="<?=$device['device_ID']?>">
								<a href="<?=site_url('revise/'.$device['device_ID'], true)?>"><i class="fa <?=$device['screen_cat_icon']?>"></i> <?=$device['screen_cat_name']?> (<?=$existing_screen_width?>x<?=$existing_screen_height?>)</a>

								<?php
								if ($selected != "selected") {
								?>
								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=remove', true)?>" data-tooltip="Delete This Screen" data-action="remove" data-confirm="Are you sure you want to remove this screen?"></i>
								<?php
								}
								?>


							</li>

						<?php
						}
						?>
							<li>

							<?php if ($screensPercentage >= 100) { ?>
								<a href="<?=site_url("upgrade")?>" data-modal="upgrade"><i class="fa fa-exclamation-circle"></i> <b>Increase Screens Limit Now</b></a>	
							<?php } else { ?>
								<a href="#" class="add-screen bottom-tooltip" data-tooltip="To see this page on different device sizes."><i class="fa fa-plus"></i> <b>Add New Screen</b></a>
								<ul class="xl-left screen-adder">
									<?php
									foreach ($User->getScreenData() as $screen_cat) {
									?>

									<li>

										<a href="#">
											<i class="fa <?=$screen_cat['screen_cat_icon']?>"></i> <?=$screen_cat['screen_cat_name']?> <i class="fa fa-caret-right"></i>
										</a>
										<ul class="addable xl-left screen-addd">
											<?php
											foreach ($screen_cat['screens'] as $screen) {

												$screen_link = site_url("projects?new_screen=".$screen['screen_ID']."&phase_ID=".$phase_ID, true);
												$screen_label = $screen['screen_name']." (".$screen['screen_width']."x".$screen['screen_height'].")";
												if ($screen['screen_ID'] == 11) {
													$screen_link = queryArg('page_width='.$screen['screen_width'], $screen_link);
													$screen_link = queryArg('page_height='.$screen['screen_height'], $screen_link);
													$screen_label = "Current Window (<span class='screen-width'>".$screen['screen_width']."</span>x<span class='screen-height'>".$screen['screen_height']."</span>)";
												}

												//$screen_link = queryArg('nonce='.$_SESSION["new_screen_nonce"], $screen_link);
											?>
											<li>
												<a href="<?=$screen_link?>"
													class="new-screen"
													data-screen-id="<?=$screen['screen_ID']?>"
													data-screen-width="<?=$screen['screen_width']?>"
													data-screen-height="<?=$screen['screen_height']?>"
													data-screen-cat-name="<?=$screen_cat['screen_cat_name']?>"
													data-screen-cat-icon="<?=$screen_cat['screen_cat_icon']?>"
												>
													<?=$screen_label?>
												</a>
											</li>
											<?php
											}
											?>
										</ul>

									</li>

									<?php
									}
									?>
								</ul>
							<?php } ?>

							</li>
						</ul>
					</span>


				</div>
			</div>

		</div>
		<div class="col xl-left pin-mode">
			
			<div class="wrap xl-gutter-8">
				<div class="col pin-modes">

					<div class="desc nomargin">Pin Mode</div>

					<div class="dropdown current-mode" data-pin-type="<?=$pin_mode?>" data-pin-private="<?=$pin_private?>">
						<a href="#" class="button browse-switcher">
							<i class="fa fa-dot-circle"></i>
							<i class="fa fa-comment"></i>
							<i class="fa fa-mouse-pointer"></i>
							<span class="mode-label"></span>
						</a>
						<a href="#" class="button pin-type-selector">
							<i class="fa fa-caret-down"></i>
						</a>
						<ul class="right pin-types">
							<li class="bottom-tooltip" data-pin-type="live" data-pin-private="0" data-tooltip="You can do both content(image & text) and visual changes."><a href="#"><i class="fa fa-dot-circle"></i> CONTENT AND VIEW CHANGES</a></li>
							<li class="bottom-tooltip" data-pin-type="style" data-pin-private="0" data-tooltip="You can only do visual changes."><a href="#"><i class="fa fa-dot-circle"></i> ONLY VIEW CHANGES</a></li>
							<!-- <li class="bottom-tooltip" data-pin-type="live" data-pin-private="1" data-tooltip="Only you can see the changes you made."><a href="#"><i class="fa fa-dot-circle"></i> PRIVATE CONTENT AND VIEW CHANGES</a></li> -->
							<li class="bottom-tooltip" data-pin-type="comment" data-pin-private="0" data-tooltip="You can only add comments."><a href="#"><i class="fa fa-comment"></i> ONLY COMMENT</a></li>
							<li class="bottom-tooltip" data-pin-type="browse" data-pin-private="0" data-tooltip="Use this mode to be able to do something like opening a menu, closing popups, skipping slides, and navigating to different pages."><a href="#"><i class="fa fa-mouse-pointer"></i> BROWSE MODE [Shift Key]</a></li>
						</ul>
					</div>

				</div>
				<div class="col xl-right pin-limits dropdown">

					<div class="dropdown-opener">
						
						<div class="desc nomargin"><?=getUserInfo()['userLevelName']?> Account <span>Limits</span></div>
						<span class="pins-count" data-modal="upgrade"><?=$pinsCount?></span> <span class="pin-limit-text">Live Pins Left</span>

					</div>
					<ul class="left limit-details">
						<li>
							
							<div class="xl-center dropdown-content">
								<?php require view('modules/limitations'); ?>
								<?php if ( getUserInfo()['userLevelName'] != "Enterprise" ): ?>
								<a href="#" class="button" data-modal="upgrade"><i class="fa fa-angle-double-up"></i> UPGRADE NOW</a>
								<?php endif; ?>
							</div>

						</li>
					</ul>

					<a href="<?=site_url('upgrade')?>" class="button upgrade bottom-tooltip" data-modal="upgrade"><i class="fa fa-angle-double-up"></i> UPGRADE NOW</a>

				</div>
			</div>

		</div>
		<div class="col share">

			<a href="#" class="button" data-modal="share" data-type="page" data-id="<?=$page_ID?>" data-object-name="<?=$page['page_name']?>" data-iamowner="<?=$page['user_ID'] == currentUserID() ? "yes" : "no"?>"><i class="fa fa-share-alt"></i> <span>SHARE</span></a>

		</div>
		<div class="col notifications-wrapper">

			<a href="#" class="button notification-opener refresh-notifications">

				<i class="fa fa-bell"></i>
				<div class="notif-no hide">0</div>

				<span>NOTIFICATIONS</span>

			</a>
			<div class="notifications">
				<ul></ul>
			</div>

		</div>
		<div class="col help-information">

			<div class="desc nomargin">Information</div>

			<div class="wrap xl-gutter-8">
				<div class="col dropdown info">

					<a href="#" class="button"><i class="fa fa-tools"></i> <span>TOOLS</span></a>
					<ul class="center xl-left" style="max-width: 300px;">
						<li>
							<div class="xl-left page-info" style="font-size: 12px;">

								<b>Site URL:</b> <a href="<?=$page['page_url']?>" target="_blank" style="letter-spacing: 0; white-space: normal;"><i class="fa fa-external-link-alt"></i> <?=$page['page_url']?></a> <br/>


								<?php
								$date_created = timeago($page['page_created'] );
								$last_updated = timeago($page['page_modified'] );

								echo "<b>Date Created:</b> $date_created<br>";
								if ($date_created != $last_updated)
									echo "<b>Last Updated:</b> $last_updated<br>";
								?>

								<b>Current Frame Scale:</b> <span class="iframe-scale">1.0</span> <br/>

							</div>

						</li>
						<li><a href="<?=site_url('revise/'.$device_ID.'?redownload')?>" class="bottom-tooltip center-tooltip redownload" data-tooltip="Try redownloading the page only if the page is not showing correctly." data-confirm="All your pins for this page will be removed, are you sure you want to redownload this page?"><i class="fa fa-angle-double-down"></i> REDOWNLOAD THIS PHASE</a></li>
						<li><a href="<?=site_url('revise/'.$device_ID.'?redownload&ssr')?>" class="bottom-tooltip center-tooltip redownload" data-tooltip="If you want to revise a JS-created webpage, you can revise your page after the JS outputs the content." data-confirm="All your pins for this page will be removed, are you sure you want to redownload this page?"><i class="fa fa-bolt"></i> REDOWNLOAD FOR JS SITE</a></li>
						<li><a href="#" class="bottom-tooltip center-tooltip" data-tooltip="Coming soon: Revisionary takes your page's full size screenshot and you can only put comment pins on it. This should only be used if the page doesn't show up correctly."><i class="fa fa-file-image"></i> SITE CAPTURE MODE</a></li>
						<li><a href="#" class="bottom-tooltip center-tooltip" data-tooltip="Coming soon: Connect this project with GitHub, BitBucket, Trello, Asana, etc. platforms to create tasks automatically by adding a pin."><i class="fa fa-plug"></i> INTEGRATIONS</a></li>
					</ul>

				</div>
				<div class="col dropdown help">

					<a href="#" class="button"><i class="fa fa-question-circle"></i> <span>HELP</span></a>
					<ul class="xl-left bottom-tooltip" data-tooltip="Coming soon...">
						<li><a href="#" data-modal="video">Quick Start</a></li>
						<li><a href="#" data-modal="video">Features Help</a></li>
						<li><a href="#" data-modal="video">Advanced Features</a></li>
						<li><a href="#" data-modal="video">Keyboard Shortcuts</a></li>
						<li><a href="#" data-modal="video">Integrations Help</a></li>
						<li><a href="#" data-modal="video">Feedback</a></li>
						<li><a href="#" data-modal="video">Contact Support</a></li>
					</ul>

				</div>
			</div>

		</div>
		<div class="col pins tab-container open">

			<a href="#" class="button opener open">
				<span>PINS</span> <i class="fa fa-list-ul"></i>
				<div class="notif-no left hide">0</div>
			</a>
			<div class="tab right wrap">
				<div class="col xl-1-1">

					<div class="pins-filter">
						<a href="#" class="<?=$pin_filter == 'all' ? 'selected' : ''?>" data-filter="all">Show All</a> |
						<a href="#" class="<?=$pin_filter == 'incomplete' ? 'selected' : ''?>" data-filter="incomplete">Only Incompleted</a> |
						<a href="#" class="<?=$pin_filter == 'complete' ? 'selected' : ''?>" data-filter="complete">Only Completed</a>
					</div>

					<div class="scrollable-content">
						<div class="pins-list" data-filter="<?=$pin_filter?>"></div>
					</div>
				</div>
			</div>

		</div>
	</div>

</div>

<div id="page" class="site">


	<div class="iframe-container">

		<iframe id="the-page" name="the-page" src="" width="<?=$width?>" height="<?=$height?>" scrolling="auto" style="min-width: <?=$width?>px; min-height: <?=$height?>px;"></iframe>

		<!-- THE PINS LIST -->
		<div id="pins" data-filter="<?=$pin_filter?>"></div>

		<!-- THE PIN CURSOR -->
		<pin class="mouse-cursor big" data-pin-type="live">1</pin>

	</div>


	<?php require view('modules/pin-window'); ?>


</div> <!-- #page.site -->

<div class="progress-bar"></div>


<?php if ( get('new') === "" ) { ?>

<div class="ask-showing-correctly">
	<div class="inner">

		<a href="#" class="close-pop" data-popup="close"><i class="fa fa-times"></i></a>

		<p>Is this page showing correctly now?</p>

		<div class="answers">
			<a href="#" class="button good" data-popup="close"><i class="fa fa-thumbs-up"></i> Yes</a>
			<a href="<?=site_url('revise/'.$device_ID.'?redownload&ssr&secondtry')?>" class="button bad"><i class="fa fa-thumbs-down"></i> No</a>
		</div>

	</div>
</div>

<?php } ?>

<?php if ( get('secondtry') === "" ) { ?>

<div class="ask-showing-correctly">
	<div class="inner">

		<a href="#" class="close-pop" data-popup="close"><i class="fa fa-times"></i></a>

		<p>Is that fixed now?</p>

		<div class="answers">
			<a href="#" class="button good" data-popup="close"><i class="fa fa-smile"></i> Looks good now!</a><br/>
			<a href="<?=site_url('revise/'.$device_ID.'?redownload')?>" class="button middle"><i class="fa fa-meh"></i> Previous was better</a><br/>
			<a href="<?=site_url('revise/'.$device_ID.'?redownload&ssr&secondtry')?>" class="button bad"><i class="fa fa-frown"></i> Both are bad</a>
		</div>

	</div>
</div>

<?php } ?>



<script>
// When page is ready
$(function(){


	var loadingProcessID = newProcess(false, "loadingProcess");
	checkPageStatus(
		<?=$phase_ID?>,
		<?=$page_ID?>,
		<?=is_numeric($queue_ID) ? $queue_ID : "''"?>,
		<?=is_numeric($process_ID) ? $process_ID : "''"?>,
		loadingProcessID
	);


});
</script>

<?php require view('static/footer_html'); ?>