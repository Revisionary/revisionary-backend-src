<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">


			<!-- Title -->
			<?php require view('modules/title'); ?>


			<!-- Filter Bar -->
			<?php require view('modules/filter-bar'); ?>


			<!-- Blocks -->
			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=$order == "" ? "sortable" : ""?>">

			<?php

			//print_r($theCategorizedData); exit;

			// THE CATEGORY LOOP
			$data_count = 0;
			foreach ($categories as $category) {


				// Category Filter
				if (
					$catFilter != ""
					&& $catFilter != "mine"
					&& $catFilter != "shared"
					&& $catFilter != "archived"
					&& $catFilter != "deleted"
					&& $catFilter != permalink($category['cat_name'])
				) continue;


				// Category action URL
				$action_url = 'ajax?type=data-action&data-type='.$dataType.'category&nonce='.$_SESSION['js_nonce'].'&id='.$category['cat_ID'];
			?>
				<!-- Category Bar -->
				<div
					id="<?=permalink($category['cat_name'])?>"
					class="item col xl-1-1 cat-separator <?php
						if (
							$category['cat_name'] == "Uncategorized" ||
							(
								$catFilter != ""
								&& $catFilter != "mine"
							)
						) echo 'xl-hidden';
						?>"
					data-order="<?=$category['cat_order_number']?>"
					data-id="<?=$category['cat_ID']?>"
					data-cat-id="<?=$category['cat_ID']?>"
					data-type="<?=$dataType?>category"
					draggable="true"
				>
					<span class="name-field handle">
						<span class="name"><?=$category['cat_name']?></span>
						<span class="actions">

							<input class="edit-name" type="text" value="<?=$category['cat_name']?>"/>
							<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil"></i></a>

							<a href="<?=site_url($action_url.'&action=remove')?>" data-action="remove"><i class="fa fa-trash"></i></a>

						</span>
					</span>

				</div>

			<?php


				// Block Data
				$category_ID = $category['cat_ID'];


				// THE BLOCK LOOP
				if ( isset($theCategorizedData[$category_ID]) ) {
					foreach ($theCategorizedData[$category_ID]['theData'] as $block) {


						if ($dataType == "project") {


							// Block URL
							$block_url = site_url('project/'.$block['project_ID']);


							// Block Images
							$block_image_name = "project.jpg";
							$block_image_path = "projects/project-".$block['project_ID']."/$block_image_name";
							$block_image_uri = cache."/$block_image_path";
							$block_image_url = cache_url($block_image_path);


						}


						if ($dataType == "page") {


							$blockDevices = $block['devicesData'];


							// Screen filter
							if ( $screenFilter != "" && is_numeric($screenFilter) ) {


								// Extract the selected screen
								$blockDevices = array_filter($blockDevices, function ($device) use ($screenFilter) {
								    return ($device['screen_cat_ID'] == $screenFilter);
								});


								if ( count($blockDevices) == 0 ) continue;

							}



							$firstDevice = reset($blockDevices);


							// First device Image
							$block_image_name = "device-".$firstDevice['device_ID'].".jpg";
							$block_image_path = "projects/project-".$block['project_ID']."/page-".$block['page_ID']."/screenshots/$block_image_name";
							$block_image_uri = cache."/$block_image_path";
							$block_image_url = cache_url($block_image_path);


							// First device URL
							$block_url = site_url('revise/'.$firstDevice['device_ID']);



							// Archive/Delete Filters
							if (
								$catFilter == "" &&
								($block['page_deleted'] == 1 || $block['page_archived'] == 1)
							) continue;


							// Delete Filters
							if (
								$catFilter == "deleted" &&
								$block['page_deleted'] == 0
							) continue;


							// Archive Filters
							if (
								$catFilter == "archived" &&
								$block['page_archived'] == 0
							) continue;


						}



						// Is directly shared to me
						$blockSharedMe = $block['share_to'] == currentUserID();

						// Is mine
						$blockIsMine = $block['user_ID'] == currentUserID();



						// Image style bg code
						$image_style = file_exists($block_image_uri) ? "background-image: url(".$block_image_url.");" : "";

					?>

							<div class="item col block" data-order="<?=$block['order_number']?>" data-id="<?=$block[$dataType.'_ID']?>" data-cat-id="<?=$block['cat_ID']?>" data-type="<?=$dataType?>" draggable="true">


								<div class="box xl-center <?=empty($image_style) ? "no-thumb" : ""?>" style="<?=$image_style?>">

									<div class="wrap overlay xl-flexbox xl-top xl-between xl-5 members">
										<div class="col xl-4-12 xl-left xl-top people" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>">

											<?php
											$block_user_ID = $block['user_ID'];
											$block_user = getUserInfo($block_user_ID);
											?>

											<!-- Owner -->
											<a href="<?=site_url($block_user['userName'])?>"
												data-tooltip="<?=$block_user['fullName']?>"
												data-mstatus="owner"
												data-fullname="<?=$block_user['fullName']?>"
												data-nameabbr="<?=$block_user['nameAbbr']?>"
												data-email="<?=$block_user['email']?>"
												data-avatar="<?=$block_user['userPicUrl']?>"
												data-userid="<?=$block_user_ID?>"
												data-unremoveable="unremoveable"
											>
												<picture class="profile-picture" <?=$block_user['printPicture']?>>
													<span <?=$block_user['userPic'] != "" ? "class='has-pic'" : ""?>><?=$block_user['nameAbbr']?></span>
												</picture>
											</a>


											<?php

											// SHARES QUERY
											$db->where('share_type', $dataType); // Exlude other types
											$db->where('shared_object_ID', $block[$dataType.'_ID']); // Is this block?

											// Get the data
											$blockShares = $db->get('shares', null, "share_to, sharer_user_ID");

											// Is shared to someone
											$isShared = false;


											foreach ($blockShares as $share) {

												// Don't show the shared people who I didn't share to and whom shared by a person I didn't share with. :O
												if (
													$block_user_ID == currentUserID() &&
													$share['sharer_user_ID'] != currentUserID()
												) {

													$projectSharedToSharer = array_search($share['sharer_user_ID'], array_column($blockShares, 'share_to'));
													if ( $projectSharedToSharer === false ) continue;

												}

											?>

												<?php
													$shared_user_ID = $share['share_to'];

													if ( is_numeric($share['share_to']) ) {
														$shared_user = getUserInfo($shared_user_ID);
												?>

											<a href="<?=site_url($shared_user['userName'])?>"
												data-tooltip="<?=$shared_user['fullName']?>"
												data-mstatus="user"
												data-fullname="<?=$shared_user['fullName']?>"
												data-nameabbr="<?=$shared_user['nameAbbr']?>"
												data-email="<?=$shared_user['email']?>"
												data-avatar="<?=$shared_user['userPicUrl']?>"
												data-userid="<?=$shared_user_ID?>"
												data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
											>
												<picture class="profile-picture" <?=$shared_user['printPicture']?>>
													<span <?=$shared_user['userPic'] != "" ? "class='has-pic'" : ""?>><?=$shared_user['nameAbbr']?></span>
												</picture>
											</a>
												<?php

													} else {

												?>
											<a href="#"
												data-tooltip="<?=$shared_user_ID?>"
												data-mstatus="email"
												data-fullname=""
												data-nameabbr=""
												data-email="<?=$shared_user_ID?>"
												data-avatar=""
												data-userid="<?=$shared_user_ID?>"
												data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
											>
												<picture class="profile-picture email">
													<i class="fa fa-envelope"></i>
												</picture>
											</a>

												<?php

													}

												?>


											<?php

												// Is shared to someone
												if ($share['sharer_user_ID'] == currentUserID())
													$isShared = true;

											}
											?>

										</div>
										<div class="col xl-8-12 xl-right xl-top pins">

											<?php

$livePinCount = $standardPinCount = $privatePinCount = $completePinCount = 0;

if ($dataType == "page" && $allMyPins) {

	$device_IDs = array_column($blockDevices, "device_ID");

	$livePinCount = count(array_filter($allMyPins, function($value) use ($block, $screenFilter, $device_IDs) {

		$pageCondition = in_array($value['device_ID'], $device_IDs);

		return $pageCondition && $value['pin_type'] == "live" && $value['pin_private'] == "0" && $value['pin_complete'] == "0";

	}));
	$standardPinCount = count(array_filter($allMyPins, function($value) use ($block, $screenFilter, $device_IDs) {

		$pageCondition = in_array($value['device_ID'], $device_IDs);

		return $pageCondition && $value['pin_type'] == "standard" && $value['pin_private'] == "0" && $value['pin_complete'] == "0";

	}));
	$privatePinCount = count(array_filter($allMyPins, function($value) use ($block, $screenFilter, $device_IDs) {

		$pageCondition = in_array($value['device_ID'], $device_IDs);

		return $pageCondition && ($value['pin_type'] == "live" || $value['pin_type'] == "standard") && $value['pin_private'] == "1" && $value['user_ID'] == currentUserID() && $value['pin_complete'] == "0";

	}));
	$completePinCount = count(array_filter($allMyPins, function($value) use ($block, $screenFilter, $device_IDs) {

		$pageCondition = in_array($value['device_ID'], $device_IDs);

		return $pageCondition && $value['pin_complete'] == "1";

	}));

}
											?>

											<?php
											if ($livePinCount > 0) {
											?>
											<pin data-pin-type="live"><?=$livePinCount?>
												<!-- <div class="notif-no">3</div> -->
												<div class="pin-title">Live</div>
											</pin>
											<?php
											} if ($standardPinCount > 0) {
											?>
											<pin data-pin-type="standard"><?=$standardPinCount?>
												<div class="pin-title">Standard</div>
											</pin>
											<?php
											} if ($privatePinCount > 0) {
											?>
											<pin data-pin-type="live" data-pin-private="1"><?=$privatePinCount?>
												<div class="pin-title">Private</div>
											</pin>
											<?php
											} if ($completePinCount > 0) {
											?>
											<pin class="show-number" data-pin-type="live" data-pin-complete="1"><?=$completePinCount?>
												<!-- <div class="notif-no">3</div> -->
												<div class="pin-title">Solved</div>
											</pin>
											<?php
											}
											?>

										</div>
										<div class="col xl-1-1 xl-center <?=$dataType == "page" ? "screens" : "pages"?>" style="position: relative;">




											<?php
											if ($dataType == "project") {
											?>

												<!-- Project Link -->
												<a href="<?=$block_url?>">

												<?php

													$block_count = 0;
													if ( isset($pageCounts[$block['project_ID']]) )
														$block_count = $pageCounts[$block['project_ID']];
												?>

													<div class="page-count"><?=$block_count?> <br>Page<?=$block_count > 1 ? 's' : ''?></div>

													<i class="fa fa-search" style="font-size: 120px;"></i>
												</a>

											<?php
											}
											?>


											<?php
											if ($dataType == "page") {

												//$pageStatus = Page::ID( $block['page_ID'] )->getPageStatus(true)['status'];
											?>


												<?php

												foreach ($blockDevices as $device) {

													//$pageStatus = Page::ID($device['page_ID'])->getPageStatus(true)['status'];

													$action_url = 'ajax?type=data-action&data-type=device&nonce='.$_SESSION['js_nonce'].'&id='.$device['device_ID'];

													$screenWidth = $device['device_width'] ? $device['device_width'] : $device['screen_width'];
													$screenHeight = $device['device_height'] ? $device['device_height'] : $device['screen_height'];
												?>

													<span class="item device-wrap" data-type="device" data-id="<?=$device['device_ID']?>">
														<a href="<?=site_url('revise/'.$device['device_ID'])?>" class="device-link">
															<i class="fa <?=$device['screen_cat_icon']?>" data-tooltip="<?=$device['screen_cat_name']?> (<?=$screenWidth?>x<?=$screenHeight?>)"></i>
														</a>
														<a href="<?=site_url($action_url.'&action=remove')?>" data-tooltip="Delete This Screen" class="remove-device" data-action="remove" data-confirm="Are you sure you want to completely remove this screen? Keep in mind that no one will be able to access this device and its pins anymore!"><i class="fa fa-times-circle"></i></a>
													</span>

												<?php
												}
												?>

												<span class="dropdown">
													<a href="#" class="add-screen"><span style="font-family: Arial;">+</span></a>
													<?php require view('modules/add-screen'); ?>
												</span>

											<?php
											} // if ($dataType == "page")
											?>


										</div>
										<div class="col xl-4-12 xl-left share">


											<a href="#" data-modal="share" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>" data-object-name="<?=$block[$dataType.'_name']?>" data-iamowner="<?=$block['user_ID'] == currentUserID() ? "yes" : "no"?>" data-tooltip="Share"><i class="fa fa-share-alt"></i></a>


										</div>
										<div class="col xl-4-12 xl-center">

										</div>
										<div class="col xl-4-12 xl-right actions">


											<?php

												$action_url = 'ajax?type=data-action&data-type='.$dataType.'&nonce='.$_SESSION['js_nonce'].'&id='.$block[$dataType.'_ID'];

											if (
												($dataType == "project" && $blockIsMine)
												|| $dataType == "page"
												|| getUserInfo()['userLevelID'] == 1
											) {

												if ($catFilter == "archived" || $catFilter == "deleted") {
											?>
<a href="<?=site_url($action_url.'&action=recover')?>" data-action="recover" data-confirm="Are you sure you want to recover this <?=$dataType?>?" data-tooltip="Recover"><i class="fa fa-reply"></i></a>
											<?php
												} else {
											?>
<a href="<?=site_url($action_url.'&action=archive')?>" data-action="archive" data-confirm="Are you sure you want to archive this <?=$dataType?>?" data-tooltip="Archive"><i class="fa fa-archive"></i></a>
											<?php
												}
											?>
<a href="<?=site_url($action_url.'&action='.($catFilter == "deleted" ? 'remove' : 'delete'))?>" data-action="<?=$catFilter == "deleted" ? 'remove' : 'delete'?>" data-confirm="<?=$catFilter == "deleted" ? 'Are you sure you want to completely remove this '.$dataType.'? Keep in mind that no one will be able to access this '.$dataType.' anymore!' : 'Are you sure you want to delete this '.$dataType.'?' ?>" data-tooltip="<?=$catFilter == "deleted" ? 'Remove' : 'Delete'?>"><i class="fa fa-trash"></i></a>
										<?php
											}
										?>




										</div>
									</div>

								</div>

								<div class="wrap xl-flexbox xl-top">
									<div class="col xl-8-12 xl-left box-name">


										<span class="name-field">
											<span class="share-icons"><?=$isShared ? '<i class="fa fa-share-square-o" data-tooltip="You have shared this '.$dataType.' to someone."></i>' : ""?><?=$block['user_ID'] != currentUserID() ? '<i class="fa fa-share-alt" data-tooltip="Someone has shared this '.$dataType.' to you."></i> ' : ''?></span> <a href="<?=$block_url?>" class="invert-hover name"><?=$block[$dataType.'_name']?></a>

											<?php
											if (
												$block['user_ID'] == currentUserID()
												|| $block['share_to'] == currentUserID()
												|| getUserInfo()['userLevelID'] == 1
												|| (isset($projectSharedMe) && $projectSharedMe)
											) {
											?>
											<span class="actions">

												<input class="edit-name item" type="text" value="<?=$block[$dataType.'_name']?>" />
												<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil"></i></a>

											</span>
											<?php
											}
											?>

										</span>


									</div>
									<div class="col xl-4-12 xl-right date">

										<?=date("d M Y", strtotime($block[$dataType.'_created']))?>

									</div>
								</div>

							</div>

					<?php

						$data_count++;

					} // END OF THE BLOCK LOOP
				} // If defined


			} // END OF THE CATEGORY LOOP

			if ($data_count == 0) echo "<div class='col xl-1-1 xl-center' style='margin-bottom: 60px;'>No ".$dataType."s found here</div>";


			if ($catFilter != "shared" && $catFilter != "deleted" && $catFilter != "archived") {
				?>

					<!-- Add New Block -->
					<div class="col block add-new-template">

						<div class="box xl-center">

							<a href="#" class="wrap xl-flexbox xl-middle xl-center" data-modal="add-new" data-type="<?=$dataType?>" data-id="<?=$dataType == "project" ? "new" : $project_ID?>">
								<div class="col">
									New <?=ucfirst($dataType)?>
									<div class="plus-icon">+</div>
								</div>
							</a>

						</div>

					</div>
					<!-- /Add New Block -->

			<?php
			}
			?>

			</div> <!-- .blocks -->



			<?php
			if ($dataType == "page") {
			?>


			<div class="wrap xl-1 xl-center">
				<div class="col" style="margin-bottom: 60px;">

					<div class="pin-statistics">
						<?php
						if ($totalLivePinCount > 0) {
						?>
						<pin class="mid" data-pin-type="live"><?=$totalLivePinCount?>
							<!-- <div class="notif-no">3</div> -->
							<div class="pin-title dark-color">Live</div>
						</pin>
						<?php
						} if ($totalStandardPinCount > 0) {
						?>
						<pin class="mid" data-pin-type="standard"><?=$totalStandardPinCount?>
							<div class="pin-title dark-color">Standard</div>
						</pin>
						<?php
						} if ($totalPrivatePinCount > 0) {
						?>
						<pin class="mid" data-pin-type="live" data-pin-private="1"><?=$totalPrivatePinCount?>
							<div class="pin-title dark-color">Private</div>
						</pin>
						<?php
						} if ($totalCompletePinCount > 0) {
						?>
						<pin class="mid show-number" data-pin-type="live" data-pin-complete="1"><?=$totalCompletePinCount?>
							<!-- <div class="notif-no">3</div> -->
							<div class="pin-title dark-color">Solved</div>
						</pin>
						<?php
						}
						?>
					</div>
					<div class="date-statistics">
						<b>Created:</b> <?=timeago($projectInfo['project_created'])?><br/>
						<b>Modified:</b> <?=timeago($project_modified)?>
					</div>
				</div>
			</div>


			<?php
			}
			?>



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>