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
						<h1 class="dropdown-opener bullet bigger-bullet"><?=strtoupper($_url[1])?></h1>
						<nav class="dropdown higher">
							<ul class="projects-menu xl-left">
								<li class="menu-item"><a href="<?=site_url('pages/'.$_url[1].'/archived')?>"><img src="<?=asset_url('icons/archive.svg')?>" /> ARCHIVED PAGES</a></li>
								<li class="menu-item"><a href="<?=site_url('pages/'.$_url[1].'/deleted')?>"><img src="<?=asset_url('icons/trash.svg')?>" style="margin-bottom: -2px;" /> DELETED PAGES</a></li>
							</ul>
						</nav>
					</div>

					<div class="members-section">

						<a class="member-selector" href="#">
							<i class="fa fa-share-alt" aria-hidden="true"></i>
						</a>

						<span class="people">

							<a href="#">
								<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/ike.png);"><div class="notif-no">3</div></picture>
							</a>

							<a href="#">
								<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/joey.png);"><div class="notif-no">1</div></picture>
							</a>

							<a href="#">
								<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/matt.png);"></picture>
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

				// Order categories
				if (isset($_GET['order'])) {
					if ($_GET['order'] == "name") ksort($pagesWithCats);
				}

				$count = 0;
				foreach ($pagesWithCats as $pageCat => $pages) {


					// Filter
					if ( isset($_url[2]) && permalink($pageCat) != $_url[2] ) continue;


					// Category Separator
					if ( $pageCat != "Uncategorized" )
						echo '<div id="'.permalink($pageCat).'" class="col xl-1-1 cat-separator" draggable="true">'.$pageCat.'</div>';


					// Order Pages
					if (isset($_GET['order'])) {
						if ($_GET['order'] == "name") sort($pages);
					}


					// List the pages under the category
					foreach ($pages as $pageName) {
				?>
					<div class="col block" draggable="true">

						<div class="box xl-center" style="background-image: url(<?=asset_url('images/pages/'.$count.'.png')?>);">

							<div class="wrap overlay xl-flexbox xl-between xl-5 members">
								<div class="col xl-4-12 xl-left xl-top people">

									<a href="#">
										<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/ike.png);"></picture>
									</a>

									<a href="#">
										<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/joey.png);"></picture>
									</a>
	<!--								<a href="#">
										<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/images/avatars/matt.png);"></picture>
									</a>
	-->

								</div>
								<div class="col xl-8-12 xl-right xl-top pins">

									<pin class="live">13
										<div class="notif-no">3</div>
										<div class="pin-title">Live</div>
									</pin>
									<pin class="standard">7
										<div class="pin-title">Standard</div>
									</pin>
									<pin class="private">4
										<div class="pin-title">Private</div>
									</pin>

								</div>
								<div class="col xl-1-1 xl-center modes" style="position: relative;">
										<a href="<?=site_url('revise/23423142')?>"><i class="fa fa-desktop" aria-hidden="true"></i></a>
										<a href="<?=site_url('revise/53346468')?>"><i class="fa fa-mobile" aria-hidden="true"></i></a>
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
							<div class="col xl-8-12 xl-left"><a href="<?=site_url('pages/twelve12')?>" class="box-name invert-hover"><?=$count == 0 ? '<img src="'.asset_url('icons/shared.svg').'" /> '.$pageName : $pageName?></a></div>
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
								New Page
								<div class="plus-icon" style="font-family: Arial; font-size: 90px; line-height: 80px;">+</div>
							</div>
						</a>

					</div>

				</div>



			</div><!-- .blocks -->



			<div class="wrap xl-1 xl-center">
				<div class="col" style="margin-bottom: 60px;">
					PROJECT STATISTICS here<br/>
					<b>Created:</b> 19 Feb 2016, 2:54 PM<br/>
					<b>Modified:</b> 10 Nov 2016, 8:25 AM
				</div>
			</div>



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>