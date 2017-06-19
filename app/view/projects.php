<?php require 'static/header_html.php' ?>
<?php require 'static/frontend/parts/header_frontend.php' ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">

			<!-- Title -->
			<div class="wrap xl-flexbox xl-middle xl-3">
				<div class="col xl-left">

				</div>

				<div class="col xl-center title">

					<div class="dropdown-container" style="display: inline-block;">
						<h1 class="dropdown-opener bullet bigger-bullet">PROJECTS</h1>
						<nav class="dropdown higher">
							<ul class="projects-menu xl-left">
								<li class="menu-item"><a href="<?=site_url('projects/archived')?>"><img src="<?=asset_url('icons/archive.svg')?>" /> ARCHIVED PROJECTS</a></li>
								<li class="menu-item"><a href="<?=site_url('projects/deleted')?>"><img src="<?=asset_url('icons/trash.svg')?>" style="margin-bottom: -2px;" /> DELETED PROJECTS</a></li>
							</ul>
						</nav>
					</div>

					<div class="members-section public-link">
						<a href="#"><i class="fa fa-link" aria-hidden="true"></i> https://revisionaryapp.com/<?=User::ID()->userName?></a>
						<a href="#" class="privacy">
							<i class="fa fa-globe" aria-hidden="true"></i> <i class="fa fa-caret-down" aria-hidden="true"></i>
						</a>
					</div>

				</div>

				<div class="col xl-right">

				</div>
			</div>

			<?php require 'static/frontend/filter-bar.php' ?>


			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=$order == "custom" ? "sortable" : ""?>">

			<?php

			// Exclude other category types
			$db->where('cat_type', 'project');

			// Exclude other users
			$db->where('cat_user_ID', currentUserID());


			$projectCategories = $db->get('categories p', null, '');


			// Add the uncategorized item
			array_unshift($projectCategories , array(
				'cat_ID' => 0,
				'cat_name' => 'Uncategorized'
			));


			// THE CATEGORY LOOP
			foreach ($projectCategories as $projectCategory) {

				// Filters
				if (
					$catFilter != "" &&
					$catFilter != "mine" &&
					$catFilter != "shared" &&
					$catFilter != permalink($projectCategory['cat_name'])
				) continue;


				// Category Bar
				if (
					$projectCategory['cat_name'] != "Uncategorized" &&
					(
						$catFilter == "" ||
						$catFilter == "mine"
					)
				)
					echo '<div id="'.permalink($projectCategory['cat_name']).'" class="col xl-1-1 cat-separator" draggable="true">'.$projectCategory['cat_name'].'</div>';



				// PROJECTS QUERY

				// Bring the shared ones
				$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
				$db->joinWhere("shares s", "s.share_type", "project");
				$db->joinWhere("shares s", "s.share_to", currentUserID());


				// Bring the category connection
				$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_cat_project_ID", "LEFT");
				$db->joinWhere("project_cat_connect cat_connect", "cat_connect.project_cat_connect_user_ID", currentUserID());


				// Bring the category info
				$db->join("categories cat", "cat_connect.project_cat_ID = cat.cat_ID", "LEFT");
				$db->joinWhere("categories cat", "cat.cat_user_ID", currentUserID());


				// Filters
				if ($catFilter == "")
					$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
				elseif ($catFilter == "mine")
					$db->where('user_ID = '.currentUserID());
				elseif ($catFilter == "shared")
					$db->where('share_to = '.currentUserID());
				else
					$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');




				// Exclude deleted and archived
				$db->where('project_deleted', 0);
				$db->where('project_archived', 0);


				// Exclude other categories
				if ($projectCategory['cat_name'] != "Uncategorized")
					$db->where('cat.cat_name', $projectCategory['cat_name']);
				else
					$db->where('cat.cat_name IS NULL');


				// Bring the order info
				$db->join("sorting o", "p.project_ID = o.sort_object_ID", "LEFT");
				$db->joinWhere("sorting o", "o.sort_type", "project");
				$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


				// Sorting !!! - Order options will be applied
				$db->orderBy("share_ID", "desc");
				$db->orderBy("cat_name", "asc");
				$db->orderBy("project_name", "asc");


/*
				// Order Projects
				if ($order == "name") $db->orderBy("project_name", "asc");
				if ($order == "date") $db->orderBy("project_created", "asc");
*/


				$projects = $db->get('projects p', null, '');


				// THE PROJECT LOOP
				foreach ($projects as $project) {

				?>

						<div class="col block" draggable="true">

							<div class="box xl-center" style="background-image: url(<?=asset_url('images/projects/'.permalink($project['project_name']).'.png')?>);">

								<div class="wrap overlay xl-flexbox xl-between xl-5 members">
									<div class="col xl-4-12 xl-left xl-top people">

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/joey.png')?>);"></picture>
										</a>
		<!--								<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);"></picture>
										</a>
		-->

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
										<a href="<?=site_url('project/'.permalink($project['project_name']))?>">
											<div class="page-count">10 <br>Pages</div>

											<i class="fa fa-search" aria-hidden="true" style="font-size: 120px;"></i>
										</a>
									</div>
									<div class="col xl-6-12 xl-left share">
										<a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
									</div>
									<div class="col xl-6-12 xl-right actions">
										<a href="#"><i class="fa fa-archive" aria-hidden="true"></i></a>
										<a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
									</div>
								</div>

							</div>

							<div class="wrap xl-flexbox xl-middle">
								<div class="col xl-8-12 xl-left">
									<a href="<?=site_url('project/'.permalink($project['project_name']))?>" class="box-name invert-hover"><?=$project['share_ID'] != "" ? '<i class="fa fa-share-alt" aria-hidden="true"></i> '.$project['project_name'] : $project['project_name']?></a></div>
								<div class="col xl-4-12 xl-right date"><?=date("d M Y", strtotime($project['project_created']))?></div>
							</div>

						</div>

				<?php

				} // END OF THE PROJECT LOOP


			} // END OF THE CATEGORY LOOP


			if ($catFilter != "shared") {
				?>

					<!-- Add New Block -->
					<div class="col block">

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