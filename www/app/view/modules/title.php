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
		if ($title == "pages" || ($title == "projects" && ($catFilter == "archived" || $catFilter == "deleted"))) {

			$goBackLink = $catFilter == "archived" || $catFilter == "deleted" ? site_url($url_prefix) : site_url('projects');

		?>

		<!-- Return to projects -->
		<a href="<?=$goBackLink?>" class="invert-hover" style="letter-spacing: 2.5px;">< PROJECT<?=$catFilter == "archived" || $catFilter == "deleted" ? "" : "S"?><?=$title == "projects" ? "S" : ""?></a>

		<?php
		}
		?>

	</div>

	<div class="col xl-center title">

		<div class="dropdown-container" style="display: inline-block;">
			<a href="<?=site_url($url_prefix)?>" class="dropdown-opener">
				<h1 class="bullet bigger-bullet <?=$title == "pages" ? 'project-title' : ''?>" <?=$title == "pages" ? 'data-id="'.$_url[1].'"' : ''?>>
					<?=$title == "pages" ? Project::ID($_url[1])->projectName : strtoupper($title)?>
				</h1>
			</a>
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

		<div class="under-main-title <?=$title == "projects" ? "public-link" : ""?>" <?=$title == "projects" ? "data-tooltip='Coming Soon...'" : ""?>>

			<?php

			if ($title == "projects") {

			?>
			<a href="<?=site_url(User::ID()->userName)?>">
				<i class="fa fa-link" aria-hidden="true"></i> https://revisionaryapp.com/<?=User::ID()->userName?>
			</a>
			<a href="#" class="privacy">
				<i class="fa fa-globe-americas"></i> <i class="fa fa-caret-down" aria-hidden="true"></i>
			</a>
			<?php

			} else {

			?>

			<a class="member-selector share-button project" href="#">
				<i class="fa fa-share-alt" data-tooltip="Share" aria-hidden="true"></i>
			</a>

			<span class="people">

				<?php
				$project_user_ID = Project::ID( $_url[1] )->getProjectInfo('user_ID');
				$project_user = User::ID($project_user_ID);
				?>

				<!-- Owner -->
				<a href="<?=site_url($project_user->userName)?>"
					data-tooltip="<?=$project_user->fullName?>"
					data-mstatus="owner"
					data-fullname="<?=$project_user->fullName?>"
					data-nameabbr="<?=substr($project_user->firstName, 0, 1).substr($project_user->lastName, 0, 1)?>"
					data-email="<?=User::ID($project_user_ID)->email?>"
					data-avatar="<?=User::ID($project_user_ID)->userPicUrl?>"
					data-userid="<?=$project_user_ID?>"
					data-unremoveable="unremoveable"
				>
					<picture class="profile-picture" <?=$project_user->printPicture()?>>
						<span <?=$project_user->userPic != "" ? "class='has-pic'" : ""?>><?=substr($project_user->firstName, 0, 1).substr($project_user->lastName, 0, 1)?></span>
					</picture>
				</a>


				<?php
				foreach ($projectShares as $share) {

					// Don't show the shared people who I didn't share to and whom shared by a person I didn't share with. :O
					if (
						$project_user_ID == currentUserID() &&
						$share['sharer_user_ID'] != currentUserID()
					) {

						$projectSharedToSharer = array_search($share['sharer_user_ID'], array_column($projectShares, 'share_to'));
						if ( $projectSharedToSharer === false ) continue;

					}

				?>

					<?php
						$shared_user_ID = $share['share_to'];

						if ( is_numeric($share['share_to']) ) {
							$shared_user = User::ID($shared_user_ID);
					?>

				<a href="<?=site_url($shared_user->userName)?>"
					data-tooltip="<?=$shared_user->fullName?>"
					data-mstatus="user"
					data-fullname="<?=$shared_user->fullName?>"
					data-nameabbr="<?=substr($shared_user->firstName, 0, 1).substr($shared_user->lastName, 0, 1)?>"
					data-email="<?=$shared_user->email?>"
					data-avatar="<?=$shared_user->userPicUrl?>"
					data-userid="<?=$shared_user_ID?>"
					data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
				>
					<picture class="profile-picture" <?=$shared_user->printPicture()?>>
						<span <?=$shared_user->userPic != "" ? "class='has-pic'" : ""?>><?=substr($shared_user->firstName, 0, 1).substr($shared_user->lastName, 0, 1)?></span>
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