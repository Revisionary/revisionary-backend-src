<?php

	$title = "projects";
	$url_prefix = "projects";


	// If PAGES page
	if ($_url[0] == "project") {

		$title = "pages";
		$url_prefix = "project/".$_url[1];

	}

?>


<div class="wrap xl-flexbox xl-middle xl-3">
	<div class="col xl-left">
		<?php
		if ($title == "pages") {
		?>

		<!-- Return to projects -->
		<a href="<?=site_url('projects')?>" class="invert-hover" style="letter-spacing: 2.5px;">< PROJECTS</a>

		<?php
		}
		?>

	</div>

	<div class="col xl-center title">

		<div class="dropdown-container" style="display: inline-block;">
			<h1 class="dropdown-opener bullet bigger-bullet"><?=$title == "pages" ? Project::ID($_url[1])->projectName : strtoupper($title)?></h1>
			<nav class="dropdown higher">
				<ul class="projects-menu xl-left">
					<li class="menu-item <?=$catFilter == "" ? "selected" : ""?>">
						<a href="<?=site_url($url_prefix)?>">
							<i class="fa fa-th" aria-hidden="true"></i>
							ALL <?=strtoupper($title)?>
						</a>
					</li>
					<li class="menu-item <?=$catFilter == "archived" ? "selected" : ""?>">
						<a href="<?=site_url($url_prefix.'/archived')?>">
							<i class="fa fa-archive" aria-hidden="true"></i>
							ARCHIVED <?=strtoupper($title)?>
						</a>
					</li>
					<li class="menu-item <?=$catFilter == "deleted" ? "selected" : ""?>">
						<a href="<?=site_url($url_prefix.'/deleted')?>">
							<i class="fa fa-trash" aria-hidden="true"></i>
							DELETED <?=strtoupper($title)?>
						</a>
					</li>
				</ul>
			</nav>
		</div>

		<div class="under-main-title <?=$title == "projects" ? "public-link" : ""?>">

			<?php

			if ($title == "projects") {

			?>
			<a href="<?=site_url(User::ID()->userName)?>">
				<i class="fa fa-link" aria-hidden="true"></i> https://revisionaryapp.com/<?=User::ID()->userName?>
			</a>
			<a href="#" class="privacy">
				<i class="fa fa-globe" aria-hidden="true"></i> <i class="fa fa-caret-down" aria-hidden="true"></i>
			</a>
			<?php

			} else {

			?>

			<a class="member-selector" href="#">
				<i class="fa fa-share-alt" aria-hidden="true"></i>
			</a>

			<span class="people">


				<!-- Owner -->
				<a href="<?=site_url(User::ID( Project::ID( $_url[1])->getProjectInfo('user_ID') )->userName)?>">
					<picture class="profile-picture" style="background-image: url(<?=User::ID( Project::ID($_url[1])->getProjectInfo('user_ID') )->userPicUrl?>);"><div class="notif-no">3</div></picture>
				</a>

				<?php
				foreach ($projectShares as $share) {
				?>

				<!-- Other shared people -->
				<a href="<?=site_url(User::ID($share['share_to'])->userName)?>">
					<picture class="profile-picture" style="background-image: url(<?=User::ID($share['share_to'])->userPicUrl?>);"><div class="notif-no">1</div></picture>
				</a>

				<?php
				}
				?>


			</span>

			<?php

			}

			?>
		</div>

	</div>

	<div class="col xl-right">

	</div>
</div>