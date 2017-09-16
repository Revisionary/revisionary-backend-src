<?php require view('static/header_html'); ?>
<?php require view('static/frontend/parts/header_frontend'); ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">


			<!-- Title -->
			<?php require view('static/frontend/title'); ?>


			<!-- Filter Bar -->
			<?php require view('static/frontend/filter-bar'); ?>


			<!-- Blocks -->
			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=$order == "" ? "sortable" : ""?>">

			<?php

			// THE CATEGORY LOOP
			$data_count = 0;
			foreach ($theCategorizedData as $category) {


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
				$theData = $category['theData'];


				// THE BLOCK LOOP
				foreach ($theData as $block) {


					// Block URL
					$block_url = site_url('project/'.$block[$dataType.'_ID']);

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
							$catFilter != "archived" &&
							$catFilter != "deleted" &&
							$deviceFilter == "" &&
							array_search($block['parent_page_ID'], array_column($onlyPageData, 'page_ID')) !== false
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

										<!-- Owner -->
										<a href="<?=site_url(User::ID($block['user_ID'])->userName)?>" data-tooltip="<?=User::ID($block['user_ID'])->fullName?>">
											<picture class="profile-picture" <?=User::ID($block['user_ID'])->printPicture()?>>
												<span <?=User::ID($block['user_ID'])->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID($block['user_ID'])->firstName, 0, 1).substr(User::ID($block['user_ID'])->lastName, 0, 1)?></span>
											</picture>
										</a>


										<?php

										// SHARES QUERY

										// Exlude other types
										$db->where('share_type', $dataType);

										// Is this block?
										$db->where('shared_object_ID', $block[$dataType.'_ID']);

										// Get the data
										$blockShares = $db->get('shares', null, "share_to, sharer_user_ID");

										// Is shared to someone
										$isShared = false;


										foreach ($blockShares as $share) {
										?>

										<!-- Other Shared Person -->
											<?php

												if ( is_numeric($share['share_to']) ) {

											?>
										<a href="<?=site_url(User::ID($share['share_to'])->userName)?>" data-tooltip="<?=User::ID($share['share_to'])->fullName?>">
											<picture class="profile-picture" <?=User::ID($share['share_to'])->printPicture()?>>
												<span <?=User::ID($share['share_to'])->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID($share['share_to'])->firstName, 0, 1).substr(User::ID($share['share_to'])->lastName, 0, 1)?></span>
											</picture>
										</a>
											<?php

												} else {

											?>
										<a href="#" data-tooltip="<?=$share['share_to']?>">
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

										<pin data-pin-mode="live">13
											<div class="notif-no">3</div>
											<div class="pin-title">Live</div>
										</pin>
										<pin data-pin-mode="standard">7
											<div class="pin-title">Standard</div>
										</pin>
										<pin data-pin-mode="private">4
											<div class="pin-title">Private</div>
										</pin>

									</div>
									<div class="col xl-1-1 xl-center <?=$dataType == "page" ? "devices" : "pages"?>" style="position: relative;">




										<?php
										if ($dataType == "project") {
										?>

											<!-- Project Link -->
											<a href="<?=$block_url?>">

											<?php

											// Bring the shared ones
											$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
											$db->joinWhere("shares s", "s.share_type", "page");
											$db->joinWhere("shares s", "s.share_to", currentUserID());


											// Bring the archive info
											$db->join("archives arc", "arc.archived_object_ID = p.page_ID", "LEFT");
											$db->joinWhere("archives arc", "arc.archiver_user_ID", currentUserID());
											$db->joinWhere("archives arc", "arc.archive_type", "page");


											// Bring the delete info
											$db->join("deletes del", "del.deleted_object_ID = p.page_ID", "LEFT");
											$db->joinWhere("deletes del", "del.deleter_user_ID", currentUserID());
											$db->joinWhere("deletes del", "del.delete_type", "page");


											// Exclude archived and deleted ones
											$db->where('arc.archived_object_ID IS NULL');
											$db->where('del.deleted_object_ID IS NULL');


											// Exclude other projects
											$db->where('project_ID', $block['project_ID']);


											// If project is not belong to current user
											if ( $block['user_ID'] != currentUserID() ) {


												// Project is shared to current user
												$blockSharedID = array_search(currentUserID(), array_column($blockShares, 'share_to'));
												if (  $blockSharedID !== false ) {

													// Show everything belong to sharer
													$db->where('user_ID = '.$blockShares[$blockSharedID]['sharer_user_ID']);

												}


											} else { // If the project is current user's or shared to him

												// Show only current user's
												$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');

											}


											$block_count = $db->getValue("pages p", "count(*)");
											?>

												<div class="page-count"><?=$block_count?> <br>Pages</div>

												<i class="fa fa-search" aria-hidden="true" style="font-size: 120px;"></i>
											</a>

										<?php
										}
										?>


										<?php
										if ($dataType == "page") {

											$pageStatus = Page::ID($block['page_ID'])->getPageStatus()['status'];
										?>


											<!-- Current Device -->
											<a href="<?=site_url('revise/'.$block['page_ID'])?>" data-status="<?=$pageStatus?>">
												<i class="fa <?=$block['device_cat_icon']?>" data-tooltip="<?=$block['device_name']?>: <?=ucfirst($pageStatus)?>" aria-hidden="true" <?=$pageStatus != "ready" ? "style='color: red;'" : ""?>></i>
											</a>

											<?php

											if (
												$catFilter != "archived" &&
												$catFilter != "deleted" &&
												$deviceFilter == ""
											) {



												foreach (array_merge($block['parentPageData'], $block['subPageData']) as $device) {


													$pageStatus = Page::ID($device['page_ID'])->getPageStatus()['status'];
											?>

											<a href="<?=site_url('revise/'.$device['page_ID'])?>" data-status="<?=$pageStatus?>">
												<i class="fa <?=$device['device_cat_icon']?>" data-tooltip="<?=$device['device_name']?>: <?=ucfirst($pageStatus)?>" aria-hidden="true" <?=$pageStatus != "ready" ? "style='color: red;'" : ""?>></i>
											</a>

											<?php
												}

												echo '<a href="#"><span style="font-family: Arial;">+</span></a>';

											}
											?>

										<?php
										}
										?>





									</div>
									<div class="col xl-4-12 xl-left share">


										<a href="#" class="share-button" data-tooltip="Share"><i class="fa fa-share-alt" aria-hidden="true"></i></a>


									</div>
									<div class="col xl-4-12 xl-center version">

										<?php

										// VERSION
										if ($dataType == "page") {

											$db->where('version_page_ID', $block['page_ID']);
											$db->where('version_user_ID', currentUserID());

											// Show the final one
											$db->orderBy('version_number');

										    $pageVersion = $db->getValue('versions', 'version_number');

										?>

										<a href="#" data-tooltip="Versions: Not working right now.">v<?=empty($pageVersion) ? "0.1" : $pageVersion?></a>

										<?php
										}
										?>

									</div>
									<div class="col xl-4-12 xl-right actions">


										<?php
											$action_url = 'ajax?type=data-action&data-type='.$dataType.'&nonce='.$_SESSION['js_nonce'].'&id='.$block[$dataType.'_ID'];

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
										<?=$isShared ? '<i class="fa fa-share-square-o" aria-hidden="true"></i>' : ""?><?=$block['user_ID'] != currentUserID() ? '<i class="fa fa-share-alt" aria-hidden="true"></i> ' : ''?><a href="<?=$block_url?>" class="invert-hover name"><?=$block[$dataType.'_name']?></a>

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
						<pin class="mid" data-pin-mode="live">13
							<div class="notif-no">3</div>
							<div class="pin-title dark-color">Live</div>
						</pin>
						<pin class="mid" data-pin-mode="standard">7
							<div class="pin-title dark-color">Standard</div>
						</pin>
						<pin class="mid" data-pin-mode="private">4
							<div class="pin-title dark-color">Private</div>
						</pin>
					</div>
					<div class="date-statistics">
						<b>Created:</b> <?=date( "d M Y, g:i A", strtotime(Project::ID($project_ID)->getProjectInfo('project_created')) )?><br/>
						<b>Modified:</b> <?=date( "d M Y, g:i A", strtotime($project_modified) )?>
					</div>
				</div>
			</div>


			<?php
			}
			?>



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/frontend/parts/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>