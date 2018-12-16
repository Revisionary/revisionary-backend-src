<?php require view('static/header_html'); ?>

<script>

	user_ID = '<?=currentUserID()?>';
	device_ID = '<?=$device_ID?>';
	page_ID = '<?=$page_ID?>';
	remote_URL = '<?=$pageData->remoteUrl?>';

	var pin_nonce = '<?=$_SESSION["pin_nonce"]?>';

</script>


<div id="loading" class="overlay">

	<div class="progress-info">
		<ul>
			<li style="color: white;"><?=$pageData->cachedUrl?></li>
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

				<div class="col home">

					<a href="<?=site_url('projects/')?>">
						<i class="fa fa-th"></i> All Projects
					</a>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>


				<div class="col projects">

					<div class="desc">Project</div>

					<span class="dropdown-container">
						<a href="<?=site_url('project/'.$project_ID)?>" class="dropdown-opener">
							<?=$projectInfo['project_name']?> <i class="fa fa-caret-down" aria-hidden="true"></i>
						</a>
						<nav class="dropdown">
							<ul>
								<?php
								foreach ($allMyProjects as $project) {

									$selected = $project['project_ID'] == $project_ID ? "selected" : "";


									$pages_of_project = array_filter($allMyPages, function($pageFound) use ($project) {
									    return ($pageFound['project_ID'] == $project['project_ID']);
									});
									$pages_of_project = categorize($pages_of_project, 'page', true);
									//die_to_print($pages_of_project);

								?>
								<li class="<?=$selected?>">
									<div class="dropdown-container">
										<a href="<?=site_url('project/'.$project['project_ID'])?>" class="<?=count($pages_of_project) ? 'dropdown-opener' : ""?>"><i class="fa fa-sign-in-alt"></i> <?=$project['project_name']?><?=count($pages_of_project) ? '<i class="fa fa-caret-right" aria-hidden="true"></i>' : ""?></a>

										<?php
										if ( count($pages_of_project) ) {
										?>
										<nav class="dropdown">
											<ul>
												<?php
												foreach ($pages_of_project as $pageFromProject) {

													$selected = $pageFromProject['page_ID'] == $page_ID ? "class='selected'" : "";


													$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageFromProject) {
													    return ($deviceFound['page_ID'] == $pageFromProject['page_ID']);
													});
													$firstDevice = reset($devices_of_page);
													//die_to_print($devices_of_page);
												?>
												<li <?=$selected?>>
													<div class="dropdown-container">
														<a href="<?=site_url('revise/'.$firstDevice['device_ID'])?>" class="dropdown-opener"><i class="fa fa-sign-in-alt"></i> <?=$pageFromProject['page_name']?></a>

														<?php
														if ( count($devices_of_page) ) {
														?>
														<nav class="dropdown">
															<ul>
																<?php
																foreach ($devices_of_page as $deviceFromPage) {

																	$selected = $deviceFromPage['device_ID'] == $device_ID ? "class='selected'" : "";
																?>
																<li <?=$selected?>><a href="<?=site_url('revise/'.$deviceFromPage['device_ID'])?>"><i class="fa fa-sign-in-alt"></i> <?=$deviceFromPage['screen_cat_name']?></a></li>
																<?php
																}
																?>
															</ul>
														</nav>
														<?php
														}
														?>

													</div>
												</li>
												<?php
												}
												?>
											</ul>
										</nav>
										<?php
										}
										?>
									</div>
								</li>
								<?php
								}
								?>
								<li><a href="#" class="add-new-box" data-type="project"><i class="fa fa-plus" aria-hidden="true"></i> Add New Project</a></li>
							</ul>
						</nav>
					</span>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>


				<?php
				if ($page['cat_name'] != null) {
				?>
				<div class="col categories">

					<div class="desc">Category</div>

					<a href="<?=site_url('project/'.$project_ID.'/'.permalink($page['cat_name']))?>">
						<?=$page['cat_name']?>
					</a>
					<sep><i class="fa fa-chevron-right"></i></sep>

				</div>
				<?php
				}
				?>

				<div class="col pages">

					<div class="desc">Page</div>

					<span class="dropdown-container">
						<a href="<?=site_url('project/'.$project_ID)?>" class="dropdown-opener">
							<?=$page['page_name']?> <i class="fa fa-caret-down" aria-hidden="true"></i>
						</a>
						<nav class="dropdown">
							<ul>
								<?php

								foreach ($other_pages as $pageOther) {

									$selected = $pageOther['page_ID'] == $page_ID ? "class='selected'" : "";


									$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageOther) {
									    return ($deviceFound['page_ID'] == $pageOther['page_ID']);
									});
									$firstDevice = reset($devices_of_page);
									//die_to_print($devices_of_page);

								?>
								<li <?=$selected?>>
									<div class="dropdown-container">
										<a href="<?=site_url('revise/'.$firstDevice['device_ID'])?>" class=" dropdown-opener"><i class="fa fa-sign-in-alt"></i> <?=$pageOther['page_name']?></a>
										<?php
										if ( count($devices_of_page) ) {
										?>
										<nav class="dropdown">
											<ul>
												<?php
												foreach ($devices_of_page as $deviceFromPage) {

													$selected = $deviceFromPage['device_ID'] == $device_ID ? "class='selected'" : "";
												?>
												<li <?=$selected?>><a href="<?=site_url('revise/'.$deviceFromPage['device_ID'])?>"><i class="fa fa-sign-in-alt"></i> <?=$deviceFromPage['screen_cat_name']?></a></li>
												<?php
												}
												?>
											</ul>
										</nav>
										<?php
										}
										?>


									</div>
								</li>
								<?php
								}
								?>
								<li>

									<a href="#" class="add-new-box" data-type="page"><i class="fa fa-plus" aria-hidden="true"></i> Add New Page</a>

								</li>
							</ul>
						</nav>
					</span>

				</div>
			</div>

		</div>
		<div class="col screen">

			<div class="wrap xl-gutter-8">
				<div class="col screen">

					<div class="desc nomargin">Screen Size</div>
					<span class="dropdown-container">

						<a href="#" class="button dropdown-opener select-screen"><i class="fa <?=$screenIcon?>" aria-hidden="true"></i> <?=$screen_name?> (<?=$width?>x<?=$height?>)  <i class="fa fa-caret-down" aria-hidden="true"></i></a>
						<nav class="dropdown">
							<ul class="xl-left">
							<?php

							// EXISTING DEVICES
							foreach ($allMyDevices as $device) {
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

								<li class="screen-registered block">
									<a href="<?=site_url('revise/'.$device['device_ID'])?>"><i class="fa <?=$device['screen_cat_icon']?>" aria-hidden="true"></i> <?=$device['screen_cat_name']?> (<?=$existing_screen_width?>x<?=$existing_screen_height?>)</a>
									<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=remove')?>" data-tooltip="Delete This Screen" data-action="remove" data-type="device"></i>
								</li>

							<?php
							}
							?>
								<li class="dropdown-container">
									<a href="#" class="dropdown-opener add-screen"><i class="fa fa-plus" aria-hidden="true"></i> Add New Screen</a>
									<nav class="dropdown xl-left">
										<ul class="screen-adder">
											<?php
											foreach ($screen_data as $screen_cat) {
											?>

											<li>

												<div class="dropdown-container">
													<div class="dropdown-opener">
														<i class="fa <?=$screen_cat['screen_cat_icon']?>" aria-hidden="true"></i> <?=$screen_cat['screen_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
													</div>
													<nav class="dropdown selectable addable xl-left">
														<ul class="screen-addd">
															<?php
															foreach ($screen_cat['screens'] as $screen) {


																$screen_link = site_url("project/$project_ID?new_screen=".$screen['screen_ID']."&page_ID=".$page_ID);
																$screen_label = $screen['screen_name']." (".$screen['screen_width']."x".$screen['screen_height'].")";
																if ($screen['screen_ID'] == 11) {
																	$screen_link = queryArg('page_width='.$screen['screen_width'], $screen_link);
																	$screen_link = queryArg('page_height='.$screen['screen_height'], $screen_link);
																	$screen_label = $screen['screen_name']." (<span class='screen-width'>".$screen['screen_width']."</span>x<span class='screen-height'>".$screen['screen_height']."</span>)";
																}

																$screen_link = queryArg('nonce='.$_SESSION["new_screen_nonce"], $screen_link);




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
													</nav>

												</div>

											</li>

											<?php
											}
											?>
										</ul>
									</nav>
								</li>
							</ul>
						</nav>
					</span>


				</div>
			</div>

		</div>
		<div class="col xl-left pin-mode">

			<div class="desc nomargin">Pin Mode</div>
			<div class="dropdown-container current-mode active" data-pin-type="<?=$pin_mode?>" data-pin-private="<?=$pin_private?>">
				<a href="#" class="button dropdown-opener"><i class="fa fa-dot-circle"></i><i class="fa fa-mouse-pointer"></i> <span class="mode-label"><?=$pin_text?></span></a>
				<nav class="dropdown">
					<ul class="pin-types">
						<li data-pin-type="live" data-pin-private="0"><a href="#"><i class="fa fa-dot-circle"></i> LIVE PREFERRED COMMENT</a></li>
						<li data-pin-type="standard" data-pin-private="0"><a href="#"><i class="fa fa-dot-circle"></i> ONLY COMMENT</a></li>
						<li data-pin-type="live" data-pin-private="1"><a href="#"><i class="fa fa-dot-circle"></i> LIVEABLE PRIVATE COMMENT</a></li>
						<li class="deactivator"><a href="#"><i class="fa fa-mouse-pointer"></i> OFF</a></li>
					</ul>
				</nav>
			</div>

		</div>
		<div class="col share">

			<a href="#" class="button page share-button" data-type="page" data-object-id="<?=$page_ID?>"><i class="fa fa-share-alt"></i> SHARE</a>

		</div>
		<div class="col pins tab-container open">

			<a href="#" class="button opener open">PINS <i class="fa fa-list-ul"></i></a>
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

		<iframe id="the-page" name="the-page" src="" data-url="" width="<?=$width?>" height="<?=$height?>" scrolling="auto" style="min-width: <?=$width?>px; min-height: <?=$height?>px;"></iframe>

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
		data-revisionary-showing-changes="0"
		data-revisionary-index="0"
	>

		<div class="wrap xl-flexbox xl-between top-actions">
			<div class="col">

				<div class="wrap xl-flexbox actions">
					<div class="col action dropdown-container">

						<pin
							class="chosen-pin"
							data-pin-type="live"
							data-pin-private="0"
						></pin>
						<span class="dropdown-opener"><span class="pin-label">Live Edit</span> <i class="fa fa-caret-down" aria-hidden="true"></i></span>

						<nav class="dropdown xl-left">
							<ul class="type-convertor">

								<li class="convert-to-live">
									<a href="#" class="xl-flexbox xl-middle">
										<pin data-pin-type="live" data-pin-private="0" data-pin-modification-type=""></pin>
										<span>Live Edit</span>
									</a>
								</li>

								<li class="convert-to-standard">
									<a href="#" class="xl-flexbox xl-middle">
										<pin data-pin-type="standard" data-pin-private="0" data-pin-modification-type="null"></pin>
										<span>Only Comment</span>
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
										<span>Private</span>
									</a>
								</li>

							</ul>
						</nav>

					</div>
					<div class="col action" data-tooltip="Coming soon.">

						<i class="fa fa-user-o" aria-hidden="true"></i>
						<span>ASSIGNEE</span>

					</div>
				</div>

			</div>
			<div class="col">
				<a href="#" class="close-button"><i class="fa fa-check"></i></a>
			</div>
		</div>

		<div class="image-editor">

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
				<div class="col title"><i class="fa fa-image"></i> Drag & Drop or <span class="select-file">Select File</span></div>
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
					<i class="fa fa-unlink" aria-hidden="true"></i> REMOVE IMAGE
				</a>
			</div>
		</div>

		<div class="content-editor">

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
				<div class="col title"><i class="fa fa-pencil-alt"></i> EDIT CONTENT:</div>
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

			<div class="wrap xl-1 difference-switch-wrap">
				<a href="#" class="col xl-right switch difference-switch">
					<i class="fa fa-random" aria-hidden="true"></i> <span class="diff-text">SHOW DIFFERENCE</span>
				</a>
			</div>

		</div>

		<div class="visual-editor <?=$debug_mode ? "" : "xl-hidden"?>">

			<div class="wrap xl-1">
				<div class="col">
					<i class="fa fa-sliders-h"></i> VIEW OPTIONS:
				</div>
				<div class="col options">

					<ul class="no-bullet">
						<li>
							<i class="fa fa-eye"></i> Show | <i class="fa fa-eye-slash"></i> Hide
						</li>
						<li>
							<i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i>
						</li>
						<li>
							<i class="fa fa-object-group"></i> Spacing & Positions <i class="fa fa-angle-down"></i>
							<ul class="xl-hidden">
								<li>Margins</li>
								<li>Paddings</li>
								<li>Positions</li>
							</ul>
						</li>
						<li>
							<i class="fa fa-tint"></i> Color <i class="fa fa-angle-down"></i>
							<ul class="xl-hidden">
								<li><i class="fa fa-palette"></i> Text</li>
								<li><i class="fa fa-fill-drip"></i> Background</li>
							</ul>
						</li>
					</ul>

				</div>
			</div>

		</div>

		<div class="pin-comments" style="margin-bottom: 15px;"></div>

		<div class="comment-actions">

			<form action="" method="post" id="comment-sender">
				<div class="wrap xl-flexbox xl-between">
					<div class="col comment-input-col">
						<input type="text" class="comment-input" placeholder="Type your comments, and hit 'Enter'..." required/>
					</div>
					<div class="col">
						<input type="image" src="<?=asset_url('icons/comment-send.svg')?>"/>
					</div>
				</div>
			</form>

		</div>

		<div class="bottom-actions">

			<div class="wrap xl-flexbox xl-between">
				<div class="col action dropdown-container">
					<a href="#" class="dropdown-opener">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i> MARK <i class="fa fa-caret-down" aria-hidden="true"></i>
					</a>
					<nav class="dropdown">
						<ul>
							<li>
								<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon.">
									<span><img src="<?=asset_url('icons/mark-rectangle.png')?>" width="15" height="10" alt=""/></span>
									RECTANGLE
								</a>
							</li>
							<li>
								<a href="#" class="xl-left" data-tooltip="Coming soon.">
									<span><img src="<?=asset_url('icons/mark-ellipse.png')?>" width="15" height="14" alt=""/></span>
									ELLIPSE
								</a>
							</li>
						</ul>
					</nav>
				</div>
				<div class="col action">
					<a href="#" class="remove-pin"><i class="fa fa-trash-o" aria-hidden="true"></i> REMOVE</a>
				</div>
				<div class="col action pin-complete">
					<a href="#" class="complete-pin">
						<pin
							data-pin-type="standard"
							data-pin-private="0"
							data-pin-complete="1"
						></pin>
						DONE
					</a>
					<a href="#" class="incomplete-pin">
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



<script>
// When page is ready
$(function(){


	var loadingProcessID = newProcess(false);
	checkPageStatus(
		<?=$page_ID?>,
		<?=is_numeric($queue_ID) ? $queue_ID : "''"?>,
		<?=is_numeric($process_ID) ? $process_ID : "''"?>,
		loadingProcessID
	);


});
</script>

<?php require view('static/footer_html'); ?>