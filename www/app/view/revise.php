<?php require view('static/header_html'); ?>

<script>

	user_ID = '<?=currentUserID()?>';
	device_ID = '<?=$device_ID?>';
	page_ID = '<?=$page_ID?>';
	remote_URL = '<?=$pageData->remoteUrl?>';

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

					<span class="dropdown">
						<a href="<?=site_url('project/'.$project_ID)?>">
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

								<a href="<?=site_url('project/'.$project['project_ID'])?>"><i class="fa fa-sign-in-alt"></i> <?=$project['project_name']?><?=count($pages_of_project) ? '<i class="fa fa-caret-right"></i>' : ""?></a>

								<?php
								if ( count($pages_of_project) ) {
								?>
								<ul>
									<?php
									foreach ($pages_of_project as $pageFromProject) {

										$selected = $pageFromProject['page_ID'] == $page_ID ? "selected" : "";


										$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageFromProject) {
										    return ($deviceFound['page_ID'] == $pageFromProject['page_ID']);
										});
										$firstDevice = reset($devices_of_page);
										//die_to_print($devices_of_page, false);


									?>
									<li class="item <?=$selected?>" data-type="page" data-id="<?=$pageFromProject['page_ID']?>">
										<a href="<?=site_url('revise/'.$firstDevice['device_ID'])?>"><i class="fa fa-sign-in-alt"></i> <?=$pageFromProject['page_name']?></a>

										<?php
										if ( count($devices_of_page) ) {
										?>
										<ul>
											<?php
											foreach ($devices_of_page as $deviceFromPage) {

												$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
											?>
											<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
												<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'])?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
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
								}
								?>

								<?php
								if ($selected != "selected" && 2 != 2) {
								?>
								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=delete')?>" data-tooltip="Delete This Project" data-action="delete" data-confirm="Are you sure you want to delete this project?"></i>
								<?php
								}
								?>
							</li>
							<?php
							}
							?>
							<li><a href="#" data-modal="add-new" data-type="project" data-id="new"><i class="fa fa-plus"></i> Add New Project</a></li>
						</ul>
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

					<span class="dropdown">
						<a href="<?=site_url('project/'.$project_ID)?>">
							<?=$page['page_name']?> <i class="fa fa-caret-down"></i>
						</a>
						<ul>
							<?php

							foreach ($other_pages as $pageOther) {

								$selected = $pageOther['page_ID'] == $page_ID ? "selected" : "";


								$devices_of_page = array_filter($allMyDevices, function($deviceFound) use ($pageOther) {
								    return ($deviceFound['page_ID'] == $pageOther['page_ID']);
								});
								$firstDevice = reset($devices_of_page);
								//die_to_print($devices_of_page);


								$action_url = 'ajax?type=data-action&data-type=page&nonce='.$_SESSION['js_nonce'].'&id='.$pageOther['page_ID'];

							?>
							<li class="item <?=$selected?>" data-type="page" data-id="<?=$pageOther['page_ID']?>">

								<a href="<?=site_url('revise/'.$firstDevice['device_ID'])?>"><i class="fa fa-sign-in-alt"></i> <?=$pageOther['page_name']?></a>
								<?php
								if ( count($devices_of_page) ) {
								?>
								<ul>
									<?php
									foreach ($devices_of_page as $deviceFromPage) {

										$selected = $deviceFromPage['device_ID'] == $device_ID ? "selected" : "";
									?>
									<li class="item <?=$selected?>" data-type="device" data-id="<?=$deviceFromPage['device_ID']?>">
										<a href="<?=site_url('revise/'.$deviceFromPage['device_ID'])?>"><i class="fa <?=$deviceFromPage['screen_cat_icon']?>"></i> <?=$deviceFromPage['screen_cat_name']?></a>
									</li>
									<?php
									}
									?>
								</ul>
								<?php
								}
								?>

								<?php
								if ($selected != "selected" && 2 != 2) {
								?>
								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=delete')?>" data-tooltip="Delete This Page" data-action="delete" data-confirm="Are you sure you want to delete this page?"></i>
								<?php
								}
								?>

							</li>
							<?php
							}
							?>
							<li>

								<a href="#" data-modal="add-new" data-object-name="<?=$projectInfo['project_name']?>" data-type="page" data-id="<?=$projectInfo['project_ID']?>"><i class="fa fa-plus"></i> Add New Page</a>

							</li>
						</ul>
					</span>

				</div>
			</div>

		</div>
		<div class="col screen">

			<div class="wrap xl-gutter-8">
				<div class="col screen">

					<div class="desc nomargin">Screen Size</div>
					<span class="dropdown">

						<a href="#" class="button select-screen"><i class="fa <?=$screenIcon?>"></i> <?=$screen_name?> (<?=$width?>x<?=$height?>)  <i class="fa fa-caret-down"></i></a>
						<ul class="xl-left">
						<?php

						// EXISTING DEVICES
						$devices_of_mypage = array_filter($allMyDevices, function($deviceFound) use ($page_ID) {
						    return ($deviceFound['page_ID'] == $page_ID);
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
								<a href="<?=site_url('revise/'.$device['device_ID'])?>"><i class="fa <?=$device['screen_cat_icon']?>"></i> <?=$device['screen_cat_name']?> (<?=$existing_screen_width?>x<?=$existing_screen_height?>)</a>
								<i class="fa fa-times delete" href="<?=site_url($action_url.'&action=remove')?>" data-tooltip="Delete This Screen" data-action="remove" data-confirm="Are you sure you want to remove this screen?"></i>
							</li>

						<?php
						}
						?>
							<li>
								<a href="#" class="add-screen"><i class="fa fa-plus"></i> Add New Screen</a>
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


												$screen_link = site_url("projects?new_screen=".$screen['screen_ID']."&page_ID=".$page_ID);
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
			<div class="dropdown current-mode active" data-pin-type="<?=$pin_mode?>" data-pin-private="<?=$pin_private?>">
				<a href="#" class="button">
					<i class="fa fa-dot-circle"></i><i class="fa fa-mouse-pointer"></i> <span class="mode-label"></span>
				</a>
				<ul class="pin-types">
					<li class="bottom-tooltip" data-pin-type="live" data-pin-private="0" data-tooltip="You can do both content(image & text) and visual changes."><a href="#"><i class="fa fa-dot-circle"></i> CONTENT AND VIEW CHANGES</a></li>
					<li class="bottom-tooltip" data-pin-type="standard" data-pin-private="0" data-tooltip="You can only do the visual changes."><a href="#"><i class="fa fa-dot-circle"></i> ONLY VIEW CHANGES</a></li>
					<li class="bottom-tooltip" data-pin-type="live" data-pin-private="1" data-tooltip="Only you can see the changes you made."><a href="#"><i class="fa fa-dot-circle"></i> PRIVATE CONTENT AND VIEW CHANGES</a></li>
					<li class="deactivator bottom-tooltip" data-tooltip="Use this mode to be able to do something like opening a menu, closing popups, skipping slides, and changing pages(in development)."><a href="#"><i class="fa fa-mouse-pointer"></i> BROWSE MODE</a></li>
				</ul>
			</div>

		</div>
		<div class="col share">

			<a href="#" class="button" data-modal="share" data-type="page" data-id="<?=$page_ID?>" data-object-name="<?=$page['page_name']?>" data-iamowner="<?=$page['user_ID'] == currentUserID() ? "yes" : "no"?>"><i class="fa fa-share-alt"></i> SHARE</a>

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
				<div class="col section-title"><i class="fa fa-image"></i> CONTENT</div>
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
				<div class="col section-title"><i class="fa fa-pencil-alt"></i> CONTENT</div>
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

					<div class="wrap xl-1 difference-switch-wrap">
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

					<i class="fa fa-sliders-h"></i> VIEW OPTIONS

				</div>
				<div class="col section-content options">

					<ul class="no-bullet options" style="margin-bottom: 0;" data-changed="no" data-showing-changes="yes" data-display="" data-opacity="">
						<li class="current-element" style="position: relative; padding-right: 100px;">

							<b>EDIT STYLE FOR:</b> <span class="element-tag">tag</span><span class="element-id">#id</span><span class="element-class">.class</span>

							<a href="#" class="switch show-original-css" style="position: absolute; right: 0; top: 5px;">
								<span class="original"><img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/> SHOW ORIGINAL</span>
								<span class="changes"><img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/> SHOW CHANGES</span>
							</a>

							<a href="#" class="switch reset-css" style="position: absolute; right: 0; top: 22px;">
								<span><i class="fa fa-unlink"></i>RESET CHANGES</span>
							</a>

						</li>
						<li class="choice edit-display">
							<a href="#" class="active edit-display-block" data-edit-css="show"><i class="fa fa-eye"></i> Show</a>
							 |
							<a href="#" class="edit-display-none" data-edit-css="hide"><i class="fa fa-eye-slash"></i> Hide</a>
						</li>
						<li class="dropdown edit-opacity hide-when-hidden">
							<a href="#"><i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i></a>
							<ul class="full">
								<li>
									<input type="range" min="0" max="1" step="0.01" value="1" class="range-slider" id="edit-opacity" data-edit-css="opacity"> <div class="percentage">100</div>
								</li>
							</ul>
						</li>
						<li class="dropdown hide-when-hidden" data-tooltip="In Development">
							<a href="#"><i class="fa fa-font"></i> Text & Item <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li><i class="fa fa-tint"></i> Color</li>
								<li><i class="fa fa-bold"></i> Style</li>
								<li><i class="fa fa-align-left"></i> Alignment</li>
							</ul>
						</li>
						<li class="dropdown hide-when-hidden" data-tooltip="In Development">
							<a href="#"><i class="fa fa-object-group"></i> Spacing & Positions <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li>Margins</li>
								<li>Borders</li>
								<li>Paddings</li>
								<li>Positions</li>
							</ul>
						</li>
						<li class="dropdown hide-when-hidden" data-tooltip="In Development">
							<a href="#"><i class="fa fa-layer-group"></i> Background <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li><i class="fa fa-fill-drip"></i> Color</li>
								<li><i class="fa fa-image"></i> Image</li>
								<li><i class="fa fa-crosshairs"></i> Position</li>
							</ul>
						</li>
					</ul>

				</div>
			</div>

		</div>

		<div class="comments">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-comment-dots"></i> COMMENTS

				</div>
				<div class="col section-content">

					<div class="pin-comments">

					</div>
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