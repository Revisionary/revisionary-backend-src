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
			<a href="<?=site_url($url_prefix)?>" class="dropdown-opener"><h1 class="bullet bigger-bullet"><?=$title == "pages" ? Project::ID($_url[1])->projectName : strtoupper($title)?></h1></a>
			<nav class="dropdown selectable">
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
				<a href="<?=site_url(User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->userName)?>" data-tooltip="<?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->fullName?>">
					<picture class="profile-picture" <?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->printPicture()?>>
						<span <?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->firstName, 0, 1).substr(User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->lastName, 0, 1)?></span>
						<div class="notif-no">3</div>
					</picture>
				</a>

				<?php
				foreach ($projectShares as $share) {
				?>


				<!-- Other Shared Person -->
					<?php

						if ( is_numeric($share['share_to']) ) {

					?>
				<a href="<?=site_url(User::ID($share['share_to'])->userName)?>" data-tooltip="<?=User::ID($share['share_to'])->fullName?>">
					<picture class="profile-picture" <?=User::ID($share['share_to'])->printPicture()?>>
						<span <?=User::ID($share['share_to'])->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID($share['share_to'])->firstName, 0, 1).substr(User::ID($share['share_to'])->lastName, 0, 1)?></span>
						<div class="notif-no">1</div>
					</picture>
				</a>
					<?php

						} else {

					?>
				<a href="#"	data-tooltip="<?=$share['share_to']?>">
					<picture class="profile-picture email xl-left">
						<i class="fa fa-envelope" aria-hidden="true"></i>
					</picture>
				</a>

					<?php

						}

					?>



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