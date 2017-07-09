<?php require 'static/header_html.php' ?>
<?php require 'static/frontend/parts/header_frontend.php' ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">

			<!-- Title -->
			<?php require 'static/frontend/title.php' ?>


			<!-- Filter Bar -->
			<?php require 'static/frontend/filter-bar.php' ?>


			<!-- Blocks -->
			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=$order == "" ? "sortable" : ""?>">

			<?php

			// THE CATEGORY LOOP
			$project_count = 0;
			foreach ($projectsData as $projectCategory) {


				// Category Bar
				if (
					$catFilter == "" ||
					$catFilter == "mine"
				)
					echo '<div class="col xl-1-1 cat-separator '.($projectCategory['cat_name'] == "Uncategorized" ? 'xl-hidden' : '').'" data-order="'.$projectCategory['sort_number'].'" data-id="'.$projectCategory['cat_ID'].'" data-cat-id="'.$projectCategory['cat_ID'].'" data-type="category" draggable="true">'.$projectCategory['cat_name'].'</div>';


				$projects = $projectCategory['projectData'];


				// THE PROJECT LOOP
				foreach ($projects as $project) {

				?>

						<div class="col block" data-order="<?=$project['sort_number']?>" data-id="<?=$project['project_ID']?>" data-cat-id="<?=$project['project_cat_ID']?>" data-type="project" draggable="true">

							<div class="box xl-center" style="background-image: url(<?=cache_url('user-'.$project['user_ID'].'/project-'.$project['project_ID'].'/'.$project['project_pic'])?>);">

								<div class="wrap overlay xl-flexbox xl-between xl-5 members">
									<div class="col xl-4-12 xl-left xl-top people">

										<!-- Owner -->
										<a href="<?=site_url(User::ID($project['user_ID'])->userName)?>">
											<picture class="profile-picture" style="background-image: url(<?=User::ID($project['user_ID'])->userPicUrl?>);"></picture>
										</a>

										<?php


										// PROJECT SHARES QUERY

										// Exlude other types
										$db->where('share_type', 'project');

										// Is this project?
										$db->where('shared_object_ID', $project['project_ID']);

										// Get the data
										$projectShares = $db->get('shares', null, "share_to");


										foreach ($projectShares as $share) {
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
									<div class="col xl-1-1 xl-center pages" style="position: relative;">
										<a href="<?=site_url('project/'.$project['project_ID'])?>">

											<?php
											$db->where('project_ID', $project['project_ID']);
											$db->where('page_archived', 0);
											$db->where('page_deleted', 0);
											$page_count = $db->getValue("pages", "count(*)");
											?>

											<div class="page-count"><?=$page_count?> <br>Pages</div>

											<i class="fa fa-search" aria-hidden="true" style="font-size: 120px;"></i>
										</a>
									</div>
									<div class="col xl-6-12 xl-left share">
										<a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
									</div>
									<div class="col xl-6-12 xl-right actions">
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
									<a href="<?=site_url('project/'.$project['project_ID'])?>" class="box-name invert-hover">
										<?=$project['share_ID'] != "" ? '<i class="fa fa-share-alt" aria-hidden="true"></i> ' : ''?><?=$project['project_name']?>
									</a>
								</div>
								<div class="col xl-4-12 xl-right date">
									<?=date("d M Y", strtotime($project['project_created']))?>
								</div>
							</div>

						</div>

				<?php

					$project_count++;

				} // END OF THE PROJECT LOOP


			} // END OF THE CATEGORY LOOP

			if ($project_count == 0) echo "<div class='col xl-1-1 xl-center' style='margin-bottom: 60px;'>No projects found here</div>";


			if ($catFilter != "shared" && $catFilter != "deleted" && $catFilter != "archived") {
				?>

					<!-- Add New Block -->
					<div class="col block add-new-template">

						<div class="box xl-center">

							<a href="#" class="add-new-box wrap xl-flexbox xl-middle xl-center" style="min-height: inherit; letter-spacing: normal;">
								<div class="col">
									New Project
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


		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>