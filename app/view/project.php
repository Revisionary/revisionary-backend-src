<?php require 'static/header_html.php' ?>
<?php require 'static/frontend/parts/header_frontend.php' ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">

			<!-- Title -->
			<?php require 'static/frontend/title.php' ?>


			<!-- Filter Bar -->
			<?php require 'static/frontend/filter-bar.php' ?>


			<!-- Blocks -->
			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=get('order') == "" ? "sortable" : ""?>" draggable="true">

			<?php

			// THE CATEGORY LOOP
			$page_count = 0;
			foreach ($pageData as $pageCategory) {


				// Category Bar
				if (
					$pageCategory['cat_name'] != "Uncategorized" &&
					( $catFilter == "" || $catFilter == "mine" )
				)
					echo '<div class="col xl-1-1 cat-separator" data-order="'.$pageCategory['sort_number'].'" data-id="'.$pageCategory['cat_ID'].'" draggable="true">'.$pageCategory['cat_name'].'</div>';



				// THE PAGES
				$pages = $pageCategory['pageData'];


				// List the pages under the category
				foreach ($pages as $page) {

					// Device filter
					if (
						$deviceFilter != "" && is_numeric($deviceFilter) &&
						$page['device_cat_ID'] != $deviceFilter
					) continue;


					// Combined Devices
					if (
						$catFilter != "archived" &&
						$catFilter != "deleted" &&
						$deviceFilter == "" &&
						array_search($page['parent_page_ID'], array_column($pages, 'page_ID')) !== false
					) continue;



					$image_page_ID = $page['page_ID'];
					if ( is_numeric($page['parent_page_ID']) )
						$image_page_ID = $page['parent_page_ID'];

					$pageImageUrl = cache_url('user-'.$page['user_ID'].'/project-'.$page['project_ID'].'/page-'.$image_page_ID.'/device-'.$page['device_ID'].'/'.$page['page_pic']);

			?>
				<div class="col block" data-order="<?=$page['sort_number']?>" draggable="true">

					<div class="box xl-center" style="background-image: url(<?=$pageImageUrl?>);">

						<div class="wrap overlay xl-flexbox xl-between xl-5 members">
							<div class="col xl-4-12 xl-left xl-top people">

								<!-- Owner -->
								<a href="<?=site_url(User::ID($page['user_ID'])->userName)?>">
									<picture class="profile-picture" style="background-image: url(<?=User::ID($project['user_ID'])->userPicUrl?>);"></picture>
								</a>

								<?php


								// PAGE SHARES QUERY

								// Exlude other types
								$db->where('share_type', 'page');

								// Is this project?
								$db->where('shared_object_ID', $page['page_ID']);

								// Get the data
								$pageShares = $db->get('shares', null, "share_to");


								foreach ($pageShares as $share) {
								?>

								<!-- Other Shared People -->
								<a href="<?=site_url(User::ID($share['share_to'])->userName)?>">
									<picture class="profile-picture" style="background-image: url(<?=User::ID($share['share_to'])->userPicUrl?>);"></picture>
								</a>

								<?php
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
							<div class="col xl-1-1 xl-center devices" style="position: relative;">

									<!-- Current Device -->
									<a href="<?=site_url('revise/'.$page['page_ID'])?>" title="<?=$page['device_name']?>">
										<i class="fa <?=$page['device_cat_icon']?>" aria-hidden="true"></i>
									</a>

									<?php

									if (
										$catFilter != "archived" &&
										$catFilter != "deleted"
									) {



										foreach (array_merge($page['parentPageData'], $page['subPageData']) as $device) {
									?>

									<a href="<?=site_url('revise/'.$device['page_ID'])?>" title="<?=$device['device_name']?>">
										<i class="fa <?=$device['device_cat_icon']?>" aria-hidden="true"></i>
									</a>

									<?php
										}

										echo '<a href="#"><span style="font-family: Arial;">+</span></a>';

									}
									?>

							</div>
							<div class="col xl-4-12 xl-left share">
								<a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
							</div>
							<div class="col xl-4-12 xl-center version">
								<a href="#">v0.1</a>
							</div>
							<div class="col xl-4-12 xl-right actions">
								<?php
									if ($catFilter == "archived" || $catFilter == "deleted") {
								?>
								<a href="#"><i class="fa fa-reply" aria-hidden="true"></i></a>
								<?php
									} else {
								?>
								<a href="#"><i class="fa fa-archive" aria-hidden="true"></i></a>
								<?php
									}
								?>
								<a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
							</div>
						</div>

					</div>

					<div class="wrap xl-flexbox xl-middle">
						<div class="col xl-8-12 xl-left">
							<a href="<?=site_url('revise/'.$page['page_ID'])?>" class="box-name invert-hover">
								<?=$page['share_ID'] != "" ? '<i class="fa fa-share-alt" aria-hidden="true"></i> ' : ''?><?=$page['page_name']?></a>
						</div>
						<div class="col xl-4-12 xl-right date">
							<?=date("d M Y", strtotime($page['page_created']))?>
						</div>
					</div>

				</div>
			<?php

				$page_count++;

				} // END OF THE PAGE LOOP

			} // END OF THE CATEGORY LOOP

			if ($page_count == 0) echo "<div class='col xl-1-1 xl-center' style='margin-bottom: 60px;'>No pages found here</div>";


			if ($catFilter != "shared" && $catFilter != "deleted" && $catFilter != "archived") {
				?>

					<!-- Add New Block -->
					<div class="col block add-new-template">

						<div class="box xl-center">

							<a href="#" class="add-new-box wrap xl-flexbox xl-middle xl-center" style="min-height: inherit; letter-spacing: normal;">
								<div class="col">
									New Page
									<div class="plus-icon" style="font-family: Arial; font-size: 90px; line-height: 80px;">+</div>
								</div>
							</a>

						</div>

					</div>
					<!-- /Add New Block -->

			<?php
			}
			?>

			</div><!-- .blocks -->



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



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>