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
						<a href="#"><i class="fa fa-link" aria-hidden="true"></i> https://revisionaryapp.com/bilaltas</a>
						<a href="#" class="privacy">
							<i class="fa fa-globe" aria-hidden="true"></i> <i class="fa fa-caret-down" aria-hidden="true"></i>
						</a>
					</div>

				</div>

				<div class="col xl-right">

				</div>
			</div>

			<?php require 'static/frontend/filter-bar.php' ?>



			<div class="wrap dpl-xl-gutter-30 blocks xl-6 <?=isset($_GET['order']) && $_GET['order'] != "custom" ? "" : "sortable"?>">

			<?php

				// Order categories
				if (isset($_GET['order'])) {
					if ($_GET['order'] == "name") ksort($projectsWithCats);
				}

				$count = 0;
				foreach ($projectsWithCats as $projectCat => $projects) {


					// Filter
					if ( isset($_url[1]) && permalink($projectCat) != $_url[1] ) continue;


					// Category Separator
					if ( $projectCat != "Mine" && $projectCat != "Shared" )
						echo '<div id="'.permalink($projectCat).'" class="col xl-1-1 cat-separator" draggable="true">'.$projectCat.'</div>';


					// Order Projects
					if (isset($_GET['order'])) {
						if ($_GET['order'] == "name") sort($projects);
					}


					// List the projects under the category
					foreach ($projects as $projectName) {
				?>
					<div class="col block" draggable="true">

						<div class="box xl-center" style="background-image: url(<?=asset_url('images/projects/'.permalink($projectName).'.png')?>);">

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
									<a href="<?=site_url('project/twelve12')?>">
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
							<div class="col xl-8-12 xl-left"><a href="<?=site_url('project/twelve12')?>" class="box-name invert-hover"><?=$projectCat == "Shared" ? '<img src="'.asset_url('icons/shared.svg').'" /> '.$projectName : $projectName?></a></div>
							<div class="col xl-4-12 xl-right date">19 Feb 2016</div>
						</div>

					</div>
			<?php
					$count++;
					}

				}
			?>

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


			</div> <!-- .blocks -->


		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>