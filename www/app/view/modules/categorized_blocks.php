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
				$action_url = 'ajax?type=data-action&data-type=category&nonce='.$_SESSION['js_nonce'].'&id='.$category['cat_ID'];
			?>
				<!-- Category Bar -->
				<div
					id="<?=permalink($category['cat_name'])?>"
					class="col xl-1-1 cat-separator name-field <?php
						if (
							$category['cat_name'] == "Uncategorized" ||
							(
								$catFilter != "" &&
								$catFilter != "mine"
							)
						) echo 'xl-hidden';
						?>"
					data-order="<?=$category['sort_number']?>"
					data-id="<?=$category['cat_ID']?>"
					data-cat-id="<?=$category['cat_ID']?>"
					data-type="category"
					draggable="true"
				>
					<span class="name"><?=$category['cat_name']?></span>
					<span class="actions">

						<input class="edit-name" type="text" value="<?=$category['cat_name']?>"/>
						<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil" aria-hidden="true"></i></a>

						<a href="<?=site_url($action_url.'&action=remove')?>" data-action="remove"><i class="fa fa-trash" aria-hidden="true"></i></a>

					</span>

				</div>

			<?php


				// Block Data
				$category_ID = $category['cat_ID'];


				// THE BLOCK LOOP
				if ( isset($theCategorizedData[$category_ID]) ) {
					foreach ($theCategorizedData[$category_ID]['theData'] as $block) {


						// Block URL
						$block_url = site_url('project/'.$block[$dataType.'_ID']);


						// THUMBNAIL
						if ( $block[$dataType.'_pic'] == null ) {


							// Update the screenshots on DB
							$block_image_name = $dataType.'.jpg';

							if ($dataType == "project") $block_image_uri = Project::ID($block[$dataType.'_ID'])->getDir()."/$block_image_name";
							else $block_image_uri = Page::ID($block[$dataType.'_ID'])->pageDeviceDir."/$block_image_name";


							// Add image names to database
							if ( file_exists($block_image_uri) ) {

								$db->where($dataType.'_ID', $block[$dataType.'_ID']);
								$db->update($dataType.'s', array(
									$dataType.'_pic' => $block_image_name
								), 1);

								$block[$dataType.'_pic'] = $block_image_name;

							}

						}

						// Project Image URL
						$block_image_url = cache_url('user-'.$block['user_ID'].'/project-'.$block[$dataType.'_ID'].'/'.$block[$dataType.'_pic']);



						// Page Filter Device Functions
						if ($dataType == "page") {


							// Device filter
							if (
								$deviceFilter != "" && is_numeric($deviceFilter) &&
								$block['device_cat_ID'] != $deviceFilter
							) continue;


							// Combined Devices
							if (
								//$catFilter != "archived" &&
								//$catFilter != "deleted" &&
								$deviceFilter == "" &&
								array_search($block['parent_page_ID'], array_column($allMyPages, 'page_ID')) !== false
							) continue;


							// Page Image Url
							$image_page_ID = $block['page_ID'];
							if ( is_numeric($block['parent_page_ID']) )
								$image_page_ID = $block['parent_page_ID'];

							$block_image_url = cache_url('user-'.$block['user_ID'].'/project-'.$block['project_ID'].'/page-'.$image_page_ID.'/device-'.$block['device_ID'].'/'.$block['page_pic']);

						}


					$image_style = "background-image: url(".$block_image_url.");";
					if ( $block[$dataType.'_pic'] == null )
						$image_style = "";

					?>

							<div class="col block" data-order="<?=$block['sort_number']?>" data-id="<?=$block[$dataType.'_ID']?>" data-cat-id="<?=$block[$dataType.'_cat_ID']?>" data-type="<?=$dataType?>" draggable="true">


								<div class="box xl-center <?=empty($image_style) ? "no-thumb" : ""?>" style="<?=$image_style?>">

									<div class="wrap overlay xl-flexbox xl-top xl-between xl-5 members">
										<div class="col xl-4-12 xl-left xl-top people">

											<?php
											$block_user_ID = $block['user_ID'];
											$block_user = getUserData($block_user_ID);
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
														$shared_user = getUserData($shared_user_ID);
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
													<i class="fa fa-envelope" aria-hidden="true"></i>
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



											$livePinCount = $standardPinCount = $privatePinCount = 0;

											if ($dataType == "page" && $allMyPins) {

												$page_IDs = array_column($block['subPageData'], "page_ID");
												$page_IDs[] = $block['page_ID'];

												$livePinCount = count(array_filter($allMyPins, function($value) use ($block, $deviceFilter, $page_IDs) {

													$pageCondition = $deviceFilter == "" ? in_array($value['page_ID'], $page_IDs) : $value['page_ID'] == $block['page_ID'];

													return $pageCondition && $value['pin_type'] == "live" && $value['pin_private'] == "0";

												}));
												$standardPinCount = count(array_filter($allMyPins, function($value) use ($block, $deviceFilter, $page_IDs) {

													$pageCondition = $deviceFilter == "" ? in_array($value['page_ID'], $page_IDs) : $value['page_ID'] == $block['page_ID'];

													return $pageCondition && $value['pin_type'] == "standard" && $value['pin_private'] == "0";

												}));
												$privatePinCount = count(array_filter($allMyPins, function($value) use ($block, $deviceFilter, $page_IDs) {

													$pageCondition = $deviceFilter == "" ? in_array($value['page_ID'], $page_IDs) : $value['page_ID'] == $block['page_ID'];

													return $pageCondition && ($value['pin_type'] == "live" || $value['pin_type'] == "standard") && $value['pin_private'] == "1" && $value['user_ID'] == currentUserID();

												}));

											}
											?>

											<?php
											if ($livePinCount > 0) {
											?>
											<pin data-pin-type="live"><?=$livePinCount?>
												<div class="notif-no">3</div>
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
											}
											?>

										</div>
										<div class="col xl-1-1 xl-center <?=$dataType == "page" ? "devices" : "pages"?>" style="position: relative;">




											<?php
											if ($dataType == "project") {
											?>

												<!-- Project Link -->
												<a href="<?=$block_url?>">

												<?php
												$block_count = $pageCounts[$block['project_ID']];
												?>

													<div class="page-count"><?=$block_count?> <br>Page<?=$block_count > 1 ? 's' : ''?></div>

													<i class="fa fa-search" aria-hidden="true" style="font-size: 120px;"></i>
												</a>

											<?php
											}
											?>


											<?php
											if ($dataType == "page") {

												//$pageStatus = Page::ID( $block['page_ID'] )->getPageStatus(true)['status'];
											?>


												<!-- Current Device -->
												<a href="<?=site_url('revise/'.$block['page_ID'])?>">
													<i class="fa <?=$block['device_cat_icon']?>" data-tooltip="<?=$block['device_cat_name']?> (<?=$block['device_width']?>x<?=$block['device_height']?>)" aria-hidden="true"></i>
												</a>

												<?php

												if (
													//$catFilter != "archived" &&
													//$catFilter != "deleted" &&
													$deviceFilter == ""
												) {


													$all_devices = $block['subPageData'];
													foreach ($all_devices as $device) {

														//$pageStatus = Page::ID($device['page_ID'])->getPageStatus(true)['status'];

												?>

														<a href="<?=site_url('revise/'.$device['page_ID'])?>">
															<i class="fa <?=$device['device_cat_icon']?>" data-tooltip="<?=$device['device_cat_name']?> (<?=$device['device_width']?>x<?=$device['device_height']?>)" aria-hidden="true"></i>
														</a>

													<?php
													}
													?>

													<span class="dropdown-container">
														<a href="#" class="dropdown-opener add-device"><span style="font-family: Arial;">+</span></a>
														<?php require view('modules/add-device'); ?>
													</span>


												<?php
												} // If not Archived or Deleted or filtred
												?>

											<?php
											} // if ($dataType == "page")
											?>


										</div>
										<div class="col xl-4-12 xl-left share">


											<a href="#" class="share-button <?=$dataType?>" data-tooltip="Share"><i class="fa fa-share-alt" aria-hidden="true"></i></a>


										</div>
										<div class="col xl-4-12 xl-center version">

											<?php

											// VERSION
											if ($dataType == "page") {

/*
												$db->where('page_ID', $block['page_ID']);
												$db->where('user_ID', currentUserID());

												$db->orderBy('version_number'); // Show the final one

											    $pageVersion = $db->getValue('versions', 'version_number');
*/

												$pageVersion = '1.0';

											?>

											<a href="#" data-tooltip="Coming Soon...">v<?=empty($pageVersion) ? "0.1" : $pageVersion?></a>

											<?php
											}
											?>

										</div>
										<div class="col xl-4-12 xl-right actions">


											<?php

												$action_url = 'ajax?type=data-action&data-type='.$dataType.'&nonce='.$_SESSION['js_nonce'].'&id='.$block[$dataType.'_ID'];


												// Add the subpages
												if (
													$dataType == "page" &&
													isset($all_devices) &&
													count($all_devices) > 0
												) {

													$subPages = "";

													foreach ($all_devices as $device) {

														$subPages .= $device['page_ID'].",";

													}

													$subPages = trim($subPages, ",");

													$action_url .= "&subpages=$subPages";

												}


												if ($catFilter == "archived" || $catFilter == "deleted") {
											?>
											<a href="<?=site_url($action_url.'&action=recover-'.$catFilter)?>" data-action="recover" data-tooltip="Recover"><i class="fa fa-reply" aria-hidden="true"></i></a>
											<?php
												} else {
											?>
											<a href="<?=site_url($action_url.'&action=archive')?>" data-action="archive" data-tooltip="Archive"><i class="fa fa-archive" aria-hidden="true"></i></a>
											<?php
												}
											?>
											<a href="<?=site_url($action_url.'&action='.($catFilter == "deleted" ? 'remove' : 'delete'))?>" data-action="<?=$catFilter == "deleted" ? 'remove' : 'delete'?>" data-tooltip="<?=$catFilter == "deleted" ? 'Remove' : 'Delete'?>"><i class="fa fa-trash" aria-hidden="true"></i></a>


										</div>
									</div>

								</div>

								<div class="wrap xl-flexbox xl-top">
									<div class="col xl-8-12 xl-left box-name">


										<span class="name-field">
											<span class="share-icons"><?=$isShared ? '<i class="fa fa-share-square-o" data-tooltip="You have shared this '.$dataType.' to someone." aria-hidden="true"></i>' : ""?><?=$block['user_ID'] != currentUserID() ? '<i class="fa fa-share-alt" data-tooltip="Someone has shared this '.$dataType.' to you." aria-hidden="true"></i> ' : ''?><a href="<?=$block_url?>" class="invert-hover name"><?=$block[$dataType.'_name']?></a></span>

											<?php
											if ($block['user_ID'] == currentUserID()) {
											?>
											<span class="actions">

												<input class="edit-name" type="text" value="<?=$block[$dataType.'_name']?>"/>
												<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil" aria-hidden="true"></i></a>

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

							<a href="#" data-type="<?=$dataType?>" class="add-new-box wrap xl-flexbox xl-middle xl-center" style="min-height: inherit; letter-spacing: normal;">
								<div class="col">
									New <?=ucfirst($dataType)?>
									<div class="plus-icon" style="font-family: Arial; font-size: 90px; line-height: 80px;">+</div>
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
							<div class="notif-no">3</div>
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