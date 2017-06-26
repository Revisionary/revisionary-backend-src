<?php require 'static/header_html.php' ?>
<?php require 'static/frontend/parts/header_frontend.php' ?>

	<div id="content" class="wrap xl-1 container">
		<div class="col">

			<!-- Title -->
			<div class="wrap xl-flexbox xl-middle xl-3">
				<div class="col xl-left">
					<a href="<?=site_url('projects')?>" class="invert-hover" style="letter-spacing: 2.5px;">< PROJECTS</a>
				</div>

				<div class="col xl-center title">

					<div class="dropdown-container" style="display: inline-block;">
						<h1 class="dropdown-opener bullet bigger-bullet"><?=Project::ID($_url[1])->projectName?></h1>
						<nav class="dropdown higher">
							<ul class="projects-menu xl-left">
								<li class="menu-item"><a href="<?=site_url('project/'.$_url[1].'/archived')?>"><img src="<?=asset_url('icons/archive.svg')?>" /> ARCHIVED PAGES</a></li>
								<li class="menu-item"><a href="<?=site_url('project/'.$_url[1].'/deleted')?>"><img src="<?=asset_url('icons/trash.svg')?>" style="margin-bottom: -2px;" /> DELETED PAGES</a></li>
							</ul>
						</nav>
					</div>

					<div class="members-section">

						<a class="member-selector" href="#">
							<i class="fa fa-share-alt" aria-hidden="true"></i>
						</a>

						<span class="people">

							<a href="#">
								<picture class="profile-picture" style="background-image: url(<?=cache_url('user-2/ike.png')?>?>);"><div class="notif-no">3</div></picture>
							</a>

							<a href="#">
								<picture class="profile-picture" style="background-image: url(<?=cache_url('user-5/joey.png')?>);"><div class="notif-no">1</div></picture>
							</a>

							<a href="#">
								<picture class="profile-picture" style="background-image: url(<?=cache_url('user-4/matt.png')?>);"></picture>
							</a>

						</span>

					</div>

				</div>

				<div class="col xl-right">

				</div>
			</div>

			<?php require 'static/frontend/filter-bar.php' ?>

			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=isset($_GET['order']) && $_GET['order'] != "custom" ? "" : "sortable"?>" draggable="true">

			<?php

			// Exclude other category types
			$db->where('cat_type', $project_ID);

			// Exclude other users
			$db->where('cat_user_ID', currentUserID());


			$pageCategories = $db->get('categories', null, '');


			// Add the uncategorized item
			array_unshift($pageCategories , array(
				'cat_ID' => 0,
				'cat_name' => 'Uncategorized'
			));


			$page_count = 0;
			foreach ($pageCategories as $pageCategory) {


				// Filters
				if (
					$catFilter != "" &&
					$catFilter != "mine" &&
					$catFilter != "shared" &&
					$catFilter != "deleted" &&
					$catFilter != "archived" &&
					$catFilter != permalink($pageCategory['cat_name'])
				) continue;


				// Category Bar
				if (
					$pageCategory['cat_name'] != "Uncategorized" &&
					(
						$catFilter == "" ||
						$catFilter == "mine"
					)
				)
					echo '<div id="'.permalink($pageCategory['cat_name']).'" class="col xl-1-1 cat-separator" draggable="true">'.$pageCategory['cat_name'].'</div>';



				// PAGES QUERY

				// Bring the shared ones
				$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
				$db->joinWhere("shares s", "s.share_type", "page");
				$db->joinWhere("shares s", "s.share_to", currentUserID());


				// Bring the category connection
				$db->join("page_cat_connect cat_connect", "p.page_ID = cat_connect.page_cat_page_ID", "LEFT");
				$db->joinWhere("page_cat_connect cat_connect", "cat_connect.page_cat_connect_user_ID", currentUserID());


				// Bring the category info
				$db->join("categories cat", "cat_connect.page_cat_ID = cat.cat_ID", "LEFT");
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
				$db->where('page_deleted', ($catFilter == "deleted" ? 1 : 0));
				$db->where('page_archived', ($catFilter == "archived" ? 1 : 0));


				// Exclude the other project pages
				$db->where('project_ID', $project_ID);


				// Exclude other categories
				if ($pageCategory['cat_name'] != "Uncategorized")
					$db->where('cat.cat_name', $pageCategory['cat_name']);
				else
					$db->where('cat.cat_name IS NULL');


				// Bring the order info
				$db->join("sorting o", "p.page_ID = o.sort_object_ID", "LEFT");
				$db->joinWhere("sorting o", "o.sort_type", "page");
				$db->joinWhere("sorting o", "o.sorter_user_ID", currentUserID());


				// Sorting !!! - Order options will be applied
				$db->orderBy("share_ID", "desc");
				$db->orderBy("cat_name", "asc");
				$db->orderBy("page_name", "asc");


/*
				// Order Pages !!!
				if ($order == "name") $db->orderBy("page_name", "asc");
				if ($order == "date") $db->orderBy("page_created", "asc");
*/


				$pages = $db->get('pages p', null, '');


				// List the pages under the category
				foreach ($pages as $page) {
			?>
				<div class="col block" draggable="true">

					<div class="box xl-center" style="background-image: url(<?=asset_url('images/pages/'.$page['page_pic'])?>);">

						<div class="wrap overlay xl-flexbox xl-between xl-5 members">
							<div class="col xl-4-12 xl-left xl-top people">

								<a href="#">
									<picture class="profile-picture" style="background-image: url(<?=cache_url('user-2/ike.png')?>);"></picture>
								</a>

								<a href="#">
									<picture class="profile-picture" style="background-image: url(<?=cache_url('user-5/joey.png')?>);"></picture>
								</a>
<!--								<a href="#">
									<picture class="profile-picture" style="background-image: url(<?=cache_url('user-4/matt.png')?>);"></picture>
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
							<div class="col xl-1-1 xl-center modes" style="position: relative;">
									<a href="<?=site_url('revise/'.$page['page_ID'])?>"><i class="fa fa-desktop" aria-hidden="true"></i></a>
									<a href="<?=site_url('revise/'.$page['page_ID'])?>"><i class="fa fa-mobile" aria-hidden="true"></i></a>
									<a href="#"><span style="font-family: Arial;">+</span></a>
							</div>
							<div class="col xl-4-12 xl-left share">
								<a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
							</div>
							<div class="col xl-4-12 xl-center version">
								<a href="#">v0.1</a>
							</div>
							<div class="col xl-4-12 xl-right actions">
								<a href="#"><i class="fa fa-archive" aria-hidden="true"></i></a>
								<a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
							</div>
						</div>

					</div>

					<div class="wrap xl-flexbox xl-middle">
						<div class="col xl-8-12 xl-left">
							<a href="<?=site_url('project/'.permalink($page['page_name']))?>" class="box-name invert-hover"><?=$page['share_ID'] != "" ? '<i class="fa fa-share-alt" aria-hidden="true"></i> '.$page['page_name'] : $page['page_name']?></a>
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

			if ($page_count == 0) echo "<div class='col xl-1-1 xl-center'>No pages found here</div>";


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
						<b>Created:</b> 19 Feb 2016, 2:54 PM<br/>
						<b>Modified:</b> 10 Nov 2016, 8:25 AM
					</div>
				</div>
			</div>



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>