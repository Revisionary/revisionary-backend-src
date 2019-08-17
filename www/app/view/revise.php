<?php require view('static/header_html'); ?>

<script>

	user_ID = '<?=currentUserID()?>';
	device_ID = '<?=$device_ID?>';
	phase_ID = '<?=$phase_ID?>';
	page_ID = '<?=$page_ID?>';
	project_ID = '<?=$project_ID?>';
	remote_URL = '<?=$phaseData->remoteUrl?>';
	pages_downloaded = {<?php

		foreach ($other_pages as $pageOther) {

		echo "'".$pageOther['page_url']."' : ".$pageOther['page_ID'].",";

	}

	?>};

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

<div class="bg-overlay">

	<?php

		$dataType = "page";
		require view('modules/modals');

	?>

</div>



<div id="top-bar">

	<div class="wrap xl-flexbox xl-between xl-bottom xl-center">
		<div class="col navigation">

			<div class="wrap xl-flexbox xl-bottom breadcrumbs">

				<div class="col account dropdown" style="margin-right: 15px;">

					<a href="#">
						<picture class="profile-picture white-border thin-border" <?=getUserInfo()['printPicture']?> data-type="user" data-id="<?=currentUserID()?>">
							<span><?=getUserInfo()['nameAbbr']?></span>
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
							<?=$projectInfo['project_name']?> <i class="fa fa-caret-down"></i>
						</a>
						<ul>
<?php
foreach ($allMyProjects as $project) {

	$selected = $project['project_ID'] == $project_ID ? "selected" : "";


	$pages_of_project = array_filter($allMyPages, function($pageFound) use ($project) {
	    return ($pageFound['project_ID'] == $project['project_ID']);
	});
	$pages_of_project = categorize($pages_of_project, 'page', true);
	//die_to_print($pages_of_project, false);


	$action_url = 'ajax?type=data-action&data-type=project&nonce='.$_SESSION['js_nonce'].'&id='.$project['project_ID'];

?>
<li class="item <?=$selected?>" data-type="project" data-id="<?=$project['project_ID']?>">

	<a href="<?=site_url('project/'.$project['project_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$project['project_name']?><?=count($pages_of_project) ? '<i class="fa fa-caret-right"></i>' : ""?></a>

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
			//$firstPhase = reset($phases_of_page);
			//die_to_print($phases_of_page);


		?>
		<li class="item <?=$selected?>" data-type="page" data-id="<?=$pageFromProject['page_ID']?>">
			<a href="<?=site_url('page/'.$pageFromProject['page_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$pageFromProject['page_name']?> <i class="fa fa-caret-right"></i></a>

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
					//$firstDevice = reset($devices_of_phase);
					//die_to_print($devices_of_phase);

				?>
				<li class="item <?=$selected?>" data-type="phase" data-id="<?=$phaseFromPage['phase_ID']?>">
					<a href="<?=site_url('phase/'.$phaseFromPage['phase_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?></a>

					<?php
					if ( count($devices_of_phase) ) {
					?>
					<ul>
						<?php
						foreach ($devices_of_phase as $deviceFromPage) {

							$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
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
				//$firstDevice = reset($devices_of_page);
				//die_to_print($devices_of_page);


				if ( count($devices_of_page) ) {
				?>
				<ul>
					<?php
					foreach ($devices_of_page as $deviceFromPage) {

						$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
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
							<li><a href="#" data-modal="add-new" data-type="project" data-id="new"><i class="fa fa-plus"></i> <b>Add New Project</b></a></li>
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

foreach ($other_pages as $pageOther) {

	$selected = $pageOther['page_ID'] == $page_ID ? "selected" : "";


	// Get phases of the page
	$phases_of_page = array_filter($allMyPhases, function($phaseFound) use ($pageOther) {
	    return ($phaseFound['page_ID'] == $pageOther['page_ID']);
	});
	$phases_of_page = array_values($phases_of_page); // Reset the keys to get phase numbers
	//$firstPhase = reset($phases_of_page);
	//die_to_print($phases_of_page);


	$action_url = 'ajax?type=data-action&data-type=page&nonce='.$_SESSION['js_nonce'].'&id='.$pageOther['page_ID'];

?>
<li class="item deletable <?=$selected?>" data-type="page" data-id="<?=$pageOther['page_ID']?>">

	<a href="<?=site_url('page/'.$pageOther['page_ID'], true)?>"><i class="fa fa-sign-in-alt"></i> <?=$pageOther['page_name']?></a>
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
			//$firstDevice = reset($devices_of_phase);
			//die_to_print($devices_of_phase);

		?>
		<li class="item <?=$selected?>" data-type="phase" data-id="<?=$phaseFromPage['phase_ID']?>">
			<a href="<?=site_url('phase/'.$phaseFromPage['phase_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?></a>

			<?php
			if ( count($devices_of_phase) ) {
			?>
			<ul>
				<?php
				foreach ($devices_of_phase as $deviceFromPage) {

					$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
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
	$firstDevice = reset($devices_of_page);
	//die_to_print($devices_of_page);


	if ( count($devices_of_page) ) {
	?>
	<ul>
		<?php
		foreach ($devices_of_page as $deviceFromPage) {

			$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
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

								<a href="#" data-modal="add-new" data-object-name="<?=$projectInfo['project_name']?>" data-type="page" data-id="<?=$projectInfo['project_ID']?>"><i class="fa fa-plus"></i> <b>Add New Page</b></a>

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

					$currentPhaseNumber = array_search($phase, $other_phases) + 1;

					?>

						<a href="#" class="button select-phase"><i class="fa fa-code-branch"></i> v<?=$currentPhaseNumber?> <i class="fa fa-caret-down"></i></a>
						<ul class="xl-left">

							<?php
							foreach($other_phases as $phaseFound) {

								if ($phaseFound['phase_ID'] == $phase_ID) continue;
								$phaseNumber = array_search($phaseFound, $other_phases) + 1;


								// Devices of the phase
								$devices_of_phase = array_filter($allMyDevices, function($deviceFound) use ($phaseFound) {
								    return ($deviceFound['phase_ID'] == $phaseFound['phase_ID']);
								});
								$firstDevice = reset($devices_of_phase);
								//die_to_print($devices_of_page);


								$action_url = 'ajax?type=data-action&data-type=phase&nonce='.$_SESSION['js_nonce'].'&id='.$phaseFound['phase_ID'];
							?>

							<li class="item deletable" data-type="phase" data-id="<?=$phaseFound['phase_ID']?>">
								<a href="<?=site_url('revise/'.$firstDevice['device_ID'], true)?>"><i class="fa fa-code-branch"></i> v<?=$phaseNumber?> (<?=timeago($phaseFound['phase_created'])?>)</a>

								<?php
								if ( count($devices_of_phase) ) {
								?>
								<ul>
									<?php
									foreach ($devices_of_phase as $deviceFromPhase) {

										$selected = $deviceFromPhase['device_ID'] == $device_ID ? "selected" : "";
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

							<li><a href="<?=site_url("projects?new_phase=$page_ID&page_width=1440&page_height=774", true)?>" class="add-phase"><i class="fa fa-plus"></i> <b>Add New Phase</b></a></li>
						</ul>
					</span>


				</div>
				<div class="col screen">

					<div class="desc nomargin">Screen Size</div>
					<span class="dropdown">

						<a href="#" class="button select-screen"><i class="fa <?=$screenIcon?>"></i> <?=$screen_name?> (<?=$width?>x<?=$height?>)  <i class="fa fa-caret-down"></i></a>
						<ul class="xl-left">
						<?php

						// EXISTING DEVICES
						$devices_of_mypage = array_filter($allMyDevices, function($deviceFound) use ($phase_ID) {
						    return ($deviceFound['phase_ID'] == $phase_ID);
						});
						foreach ($devices_of_mypage as $device) {
							if ($device['device_ID'] == $device_ID) continue;



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

							<li class="item deletable screen-registered" data-type="device" data-id="<?=$device['device_ID']?>">
								<a href="<?=site_url('revise/'.$device['device_ID'], true)?>"><i class="fa <?=$device['screen_cat_icon']?>"></i> <?=$device['screen_cat_name']?> (<?=$existing_screen_width?>x<?=$existing_screen_height?>)</a>
								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=remove', true)?>" data-tooltip="Delete This Screen" data-action="remove" data-confirm="Are you sure you want to remove this screen?"></i>
							</li>

						<?php
						}
						?>
							<li>
								<a href="#" class="add-screen"><i class="fa fa-plus"></i> <b>Add New Screen</b></a>
								<ul class="xl-left screen-adder">
									<?php
									foreach ($screen_data as $screen_cat) {
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
													$screen_label = $screen['screen_name']." (<span class='screen-width'>".$screen['screen_width']."</span>x<span class='screen-height'>".$screen['screen_height']."</span>)";
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
							</li>
						</ul>
					</span>


				</div>
			</div>

		</div>
		<div class="col xl-left pin-mode">

			<div class="desc nomargin">Pin Mode</div>

			<div class="dropdown current-mode" data-pin-type="<?=$pin_mode?>" data-pin-private="<?=$pin_private?>">
				<a href="#" class="button browse-switcher">
					<i class="fa fa-dot-circle"></i><i class="fa fa-mouse-pointer"></i> <span class="mode-label"></span></span>
				</a>
				<a href="#" class="button pin-type-selector">
					<i class="fa fa-caret-down"></i></span>
				</a>
				<ul class="pin-types">
					<li class="bottom-tooltip <?=$pin_mode == "live" && $pin_private == "0" ? "selected" : ""?>" data-pin-type="live" data-pin-private="0" data-tooltip="You can do both content(image & text) and visual changes."><a href="#"><i class="fa fa-dot-circle"></i> CONTENT AND VIEW CHANGES</a></li>
					<li class="bottom-tooltip <?=$pin_mode == "standard" && $pin_private == "0" ? "selected" : ""?>" data-pin-type="standard" data-pin-private="0" data-tooltip="You can only do the visual changes."><a href="#"><i class="fa fa-dot-circle"></i> ONLY VIEW CHANGES</a></li>
					<li class="bottom-tooltip <?=$pin_mode == "live" && $pin_private == "1" ? "selected" : ""?>" data-pin-type="live" data-pin-private="1" data-tooltip="Only you can see the changes you made."><a href="#"><i class="fa fa-dot-circle"></i> PRIVATE CONTENT AND VIEW CHANGES</a></li>
					<li class="bottom-tooltip <?=$pin_mode == "browse" && $pin_private == "0" ? "selected" : ""?>" data-pin-type="browse" data-pin-private="0" data-tooltip="Use this mode to be able to do something like opening a menu, closing popups, skipping slides, and navigating to different pages."><a href="#"><i class="fa fa-mouse-pointer"></i> BROWSE MODE [Shift Key]</a></li>
				</ul>
			</div>

		</div>
		<div class="col share">

			<a href="#" class="button" data-modal="share" data-type="page" data-id="<?=$page_ID?>" data-object-name="<?=$page['page_name']?>" data-iamowner="<?=$page['user_ID'] == currentUserID() ? "yes" : "no"?>"><i class="fa fa-share-alt"></i> SHARE</a>

		</div>
		<div class="col notifications-wrapper">

			<a href="#" class="button notification-opener refresh-notifications">

				<i class="fa fa-bell"></i>
				<div class="notif-no hide">0</div>

				NOTIFICATIONS

			</a>
			<div class="notifications">
				<ul></ul>
			</div>

		</div>
		<div class="col help-information">

			<div class="desc nomargin">Information</div>

			<div class="wrap xl-gutter-8">
				<div class="col dropdown info">

					<a href="#" class="button"><i class="fa fa-tools"></i> TOOLS</a>
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
						<li><a href="<?=site_url('revise/'.$device_ID.'?redownload')?>" class="bottom-tooltip center-tooltip redownload" data-tooltip="Try redownloading the page only if the page is not showing correctly." data-confirm="All your pins for this page will be removed, are you sure you want to redownload this page?"><i class="fa fa-angle-double-down"></i> REDOWNLOAD THIS VERSION</a></li>
						<li><a href="<?=site_url('revise/'.$device_ID.'?redownload&ssr')?>" class="bottom-tooltip center-tooltip redownload" data-tooltip="If you want to revise a JS-created webpage, you can revise your page after the JS outputs the content." data-confirm="All your pins for this page will be removed, are you sure you want to redownload this page?"><i class="fa fa-bolt"></i> REDOWNLOAD FOR JS SITE</a></li>
						<li><a href="#" class="bottom-tooltip center-tooltip" data-tooltip="Coming soon: Revisionary takes your page's full size screenshot and you can only put comment pins on it. This should only be used if the page doesn't show up correctly."><i class="fa fa-file-image"></i> SITE CAPTURE MODE</a></li>
						<li><a href="#" class="bottom-tooltip center-tooltip" data-tooltip="Coming soon: Connect this project with GitHub, BitBucket, Trello, Asana, etc. platforms to create tasks automatically by adding a pin."><i class="fa fa-plug"></i> INTEGRATIONS</a></li>
					</ul>

				</div>
				<div class="col dropdown help">

					<a href="#" class="button"><i class="fa fa-question-circle"></i> HELP</a>
					<ul class="xl-left bottom-tooltip" data-tooltip="Coming soon...">
						<li><a href="#" data-modal="video">Quick Start</a></li>
						<li><a href="#" data-modal="video">Features Help</a></li>
						<li><a href="#" data-modal="video">Advanced Features</a></li>
						<li><a href="#" data-modal="video">Keyboard Shortcuts</a></li>
						<li><a href="#" data-modal="video">Integrations Help</a></li>
						<li><a href="#" data-modal="video">Contact Support</a></li>
					</ul>

				</div>
			</div>

		</div>
		<div class="col pins tab-container open">

			<a href="#" class="button opener open">
				PINS <i class="fa fa-list-ul"></i>
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



	<div id="pin-window" class="loading"
		data-pin-id="0"
		data-pin-type="live"
		data-pin-private="0"
		data-pin-complete="0"
		data-pin-x="30"
		data-pin-y="30"
		data-pin-modification-type=""
		data-revisionary-edited="0"
		data-changed="no"
		data-showing-changes="no"
		data-has-comments="no"
		data-revisionary-showing-changes="0"
		data-revisionary-index="0"
	>

		<div class="wrap xl-flexbox xl-between top-actions">
			<div class="col move-window left-tooltip" data-tooltip="Drag & Drop the pin window to detach from the pin.">
				<i class="fa fa-arrows-alt"></i>
			</div>
			<div class="col">

				<div class="wrap xl-flexbox actions">
					<div class="col action dropdown">

						<pin
							class="chosen-pin"
							data-pin-type="live"
							data-pin-private="0"
						></pin>
						<a href="#"><span class="pin-label">Live Edit</span> <i class="fa fa-caret-down"></i></a>

						<ul class="xl-left type-convertor">

							<li class="convert-to-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="0" data-pin-modification-type=""></pin>
									<span>Live Edit</span>
								</a>
							</li>

							<li class="convert-to-standard">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="0" data-pin-modification-type="null"></pin>
									<span>Only View</span>
								</a>
							</li>

							<li class="convert-to-private-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="1" data-pin-modification-type=""></pin>
									<span>Private Live</span>
								</a>
							</li>

							<li class="convert-to-private">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="1" data-pin-modification-type="null"></pin>
									<span>Private View</span>
								</a>
							</li>

						</ul>

					</div>
					<div class="col action">
						<a href="#" class="center-tooltip bottom-tooltip device-specific" data-tooltip="Only For Current Device"><i class="fa fa-thumbtack"></i></a>
					</div>
					<div class="col action" data-tooltip="Coming soon." style="display: none !important;">

						<i class="fa fa-user-o"></i>
						<span>ASSIGNEE</span>

					</div>
				</div>

			</div>
			<div class="col">
				<a href="#" class="close-button" data-tooltip="Close this pin window when you're done here."><i class="fa fa-check"></i></a>
			</div>
		</div>

		<div class="image-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-image"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">Drag & Drop or <span class="select-file">Select File</span></div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">ORIGINAL IMAGE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1">
						<div class="col">
							<div class="edit-content changes uploader">

							    <img class="new-image" src=""/>
							    <div class="info"><span><span style="text-decoration: underline;">Click here</span> or drag here your image for preview</span></div>
							    <input type="file" name="image" id="filePhoto" data-max-size="3145728" />

							</div>
							<div class="edit-content original">
							    <img class="original-image" src=""/>
							</div>
						</div>
					</div>
					<div class="wrap xl-1 xl-right difference-switch-wrap">
						<a href="#" class="col switch remove-image">
							<i class="fa fa-unlink"></i> REMOVE IMAGE
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="content-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-pencil-alt"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">EDIT CONTENT:</div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">
							<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
							ORIGINAL CONTENT:
						</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap differences">
						<div class="col"><i class="fa fa-random"></i> DIFFERENCE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes xl-hidden">
								<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1 content-boxes">
						<div class="col">
							<div class="edit-content changes" contenteditable="true"></div>
							<div class="edit-content original"></div>
							<div class="edit-content differences"></div>
						</div>
					</div>

					<div class="wrap xl-2 difference-switch-wrap" style="padding-left: 10px;">
						<a href="#" class="col switch reset-content">
							<span><i class="fa fa-unlink"></i> RESET CHANGES</span>
						</a>
						<a href="#" class="col xl-right switch difference-switch">
							<i class="fa fa-random"></i> <span class="diff-text">SHOW DIFFERENCE</span>
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="visual-editor">

			<div class="wrap xl-1">
				<div class="col section-title collapsed">

					<i class="fa fa-sliders-h"></i> STYLE <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content options">

					<ul class="no-bullet options" style="margin-bottom: 0;">
						<li class="current-element">

							<span class="css-selector"><b>EDIT STYLE:</b> <span class="element-tag">tag</span><span class="element-id">#id</span><span class="element-class">.class</span></span>

							<a href="#" class="switch show-original-css" style="position: absolute; right: 0; top: 5px; z-index: 1;">
								<span class="original"><img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/> SHOW ORIGINAL</span>
								<span class="changes"><img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/> SHOW CHANGES</span>
							</a>

							<a href="#" class="switch reset-css" style="position: absolute; right: 0; top: 22px; z-index: 1;">
								<span><i class="fa fa-unlink"></i>RESET CHANGES</span>
							</a>

						</li>
						<li class="main-option choice">

							<a href="#" data-edit-css="display" data-value="block" data-default="none" class="active"><i class="fa fa-eye"></i> Show</a> |
							<a href="#" data-edit-css="display" data-value="none" data-default="block"><i class="fa fa-eye-slash"></i> Hide</a>

						</li>
						<li class="main-option dropdown edit-opacity hide-when-hidden">

							<a href="#"><i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li>

									<input type="range" min="0" max="1" step="0.01" value="1" class="range-slider" id="edit-opacity" data-edit-css="opacity" data-default="1"> <div class="percentage">100</div>

								</li>
							</ul>

						</li>
						<li class="main-option dropdown hide-when-hidden">

							<a href="#"><i class="fa fa-font"></i> Text & Item <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay">
								<li class="choice">

									<label class="main-option sub"><span class="inline"><i class="fa fa-font"></i> Size</span> <input type="text" class="increaseable" data-edit-css="font-size" data-default="initial"></label>
									<label class="main-option sub"><span class="inline"><i class="fa fa-text-height"></i> Line</span> <input type="text" class="increaseable" data-edit-css="line-height" data-default="normal"></label>

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-tint"></i> Color</span> <input type="color" data-edit-css="color" data-default="initial">

								</li>
								<li class="main-option sub choice selectable">

									<a href="#" data-edit-css="font-weight" data-value="bold" data-default="normal"><i class="fa fa-bold"></i> Bold</a> |
									<a href="#" data-edit-css="font-style" data-value="italic" data-default="normal"><i class="fa fa-italic"></i> Italic</a> |
									<a href="#" data-edit-css="text-decoration-line" data-value="underline" data-default="none"><i class="fa fa-underline"></i> Underline</a>

								</li>
								<li class="main-option sub choice">

									<a href="#" data-edit-css="text-align" data-value="left" data-default="right"><i class="fa fa-align-left"></i> Left</a> |
									<a href="#" data-edit-css="text-align" data-value="center" data-default="left"><i class="fa fa-align-center"></i> Center</a> |
									<a href="#" data-edit-css="text-align" data-value="justify" data-default="left"><i class="fa fa-align-justify"></i> Justify</a> |
									<a href="#" data-edit-css="text-align" data-value="right" data-default="left"><i class="fa fa-align-right"></i> Right</a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden">
							<a href="#"><i class="fa fa-layer-group"></i> Background <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-fill-drip"></i> Color:</span>
									<input type="color" data-edit-css="background-color" data-default="initial">

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-image"></i> Image URL:</span> <input type="url" data-edit-css="background-image" data-default="none" class="full no-padding" />

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-crosshairs"></i> Position:</span>

									<span class="inline">X:</span> <input type="text" class="increaseable" data-edit-css="background-position-x" data-default="initial"/>
									<span class="inline">Y:</span> <input type="text" class="increaseable" data-edit-css="background-position-y" data-default="initial" />

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-arrows-alt-v"></i> Size: </span>

									<a href="#" data-edit-css="background-size" data-value="auto" data-default="cover">Auto</a> |
									<a href="#" data-edit-css="background-size" data-value="cover" data-default="auto">Cover</a> |
									<a href="#" data-edit-css="background-size" data-value="contain" data-default="auto">Contain</a>

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-redo"></i> Repeat: </span>

									<a href="#" data-edit-css="background-repeat" data-value="no-repeat" data-tooltip="No Repeat" data-default="repeat-x"><i class="fa fa-compress-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat" data-tooltip="Repeat X and Y" data-default="no-repeat"><i class="fa fa-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-x" data-tooltip="Repeat X" data-default="no-repeat"><i class="fa fa-long-arrow-alt-right"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-y" data-tooltip="Repeat Y" data-default="no-repeat"><i class="fa fa-long-arrow-alt-down"></i></a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden" data-tooltip="Experimental">
							<a href="#"><i class="fa fa-object-group"></i> Spacing & Positions <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full" style="width: auto;">
								<li>

									<div class="css-box">

										<div class="layer positions">

<div class="main-option sub input top"><input type="text" data-edit-css="top" data-default="initial"/></div>
<div class="main-option sub input right"><input type="text" data-edit-css="right" data-default="initial"/></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="bottom" data-default="initial"/></div>
<div class="main-option sub input left"><input type="text" data-edit-css="left" data-default="initial"/></div>


											<div class="layer margins">

<div class="main-option sub input top"><input type="text" data-edit-css="margin-top" data-default="initial"/></div>
<div class="main-option sub input right"><input type="text" data-edit-css="margin-right" data-default="initial"/></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="margin-bottom" data-default="initial"/></div>
<div class="main-option sub input left"><input type="text" data-edit-css="margin-left" data-default="initial"/></div>


												<div class="layer borders">

<div class="main-option sub input top"><input type="text" data-edit-css="border-top-width" data-default="initial"/></div>
<div class="main-option sub input right"><input type="text" data-edit-css="border-right-width" data-default="initial"/></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="border-bottom-width" data-default="initial"/></div>
<div class="main-option sub input left"><input type="text" data-edit-css="border-left-width" data-default="initial"/></div>



<div class="main-option sub input top left middle"><input type="text" data-edit-css="border-style" data-default="initial"></div>
<div class="main-option sub input top right middle"><input type="color" data-edit-css="border-color" data-default="initial"></div>

<div class="main-option sub input top left"><input type="text" data-edit-css="border-top-left-radius" data-default="initial"/><span>Radius</span></div>
<div class="main-option sub input top right"><input type="text" data-edit-css="border-top-right-radius" data-default="initial"/><span>Radius</span></div>
<div class="main-option sub input bottom left"><span>Radius</span><input type="text" data-edit-css="border-bottom-left-radius" data-default="initial"/></div>
<div class="main-option sub input bottom right"><span>Radius</span><input type="text" data-edit-css="border-bottom-right-radius" data-default="initial"/></div>


													<div class="layer paddings">

<div class="main-option sub input top"><input type="text" data-edit-css="padding-top" data-default="initial"/></div>
<div class="main-option sub input right"><input type="text" data-edit-css="padding-right" data-default="initial"/></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="padding-bottom" data-default="initial"/></div>
<div class="main-option sub input left"><input type="text" data-edit-css="padding-left" data-default="initial"/></div>


														<div class="layer sizes">

<input type="text" data-edit-css="width" data-default="initial"/> x
<input type="text" data-edit-css="height" data-default="initial"/>

														</div>

													</div>

												</div>

											</div>

										</div>

									</div>

								</li>
							</ul>
						</li>
					</ul>

				</div>
			</div>

		</div>

		<div class="comments">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-comment-dots"></i> COMMENTS <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content">

					<div class="pin-comments">

					</div>
					<div class="comment-actions">

						<form action="" method="post" id="comment-sender">
							<div class="wrap xl-flexbox xl-between">
								<div class="col comment-input-col">
									<textarea class="comment-input resizeable" rows="1" placeholder="Type your comments, and hit 'Enter'..." required></textarea>
								</div>
								<div class="col">
									<a href="#" class="send-comment"><i class="fa fa-paper-plane"></i></a>
								</div>
							</div>
						</form>

					</div>

				</div>
			</div>



		</div>

		<div class="bottom-actions">

			<div class="wrap xl-flexbox xl-between">
				<div class="col action dropdown">
					<a href="#">
						<i class="fa fa-pencil-square-o"></i> MARK <i class="fa fa-caret-down"></i>
					</a>
					<ul>
						<li>
							<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon." style="padding-right: 15px;">
								<img src="<?=asset_url('icons/mark-rectangle.png')?>" width="15" height="10" alt=""/>
								RECTANGLE
							</a>
						</li>
						<li>
							<a href="#" class="xl-left" data-tooltip="Coming soon.">
								<img src="<?=asset_url('icons/mark-ellipse.png')?>" width="15" height="14" alt=""/>
								ELLIPSE
							</a>
						</li>
					</ul>
				</div>
				<div class="col action">
					<a href="#" class="remove-pin"><i class="fa fa-trash-o"></i> REMOVE</a>
				</div>
				<div class="col action pin-complete">
					<a href="#" class="complete-pin" data-tooltip="Mark as resolved">
						<pin
							data-pin-type="standard"
							data-pin-private="0"
							data-pin-complete="1"
						></pin>
						DONE
					</a>
					<a href="#" class="incomplete-pin" data-tooltip="Mark as unresolved">
						<pin
							data-pin-type="standard"
							data-pin-private="0"
							data-pin-complete="0"
						></pin>
						INCOMPLETE
					</a>
				</div>
			</div>

		</div>

	</div>


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