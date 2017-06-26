			<!-- Toolbar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<span class="search"><img src="<?=asset_url('icons/search.svg')?>" /></span>
				<?php

					// PROJECTS PAGE
					if ($_url[0] == "projects") {

						$catFilter = isset($_url[1]) ? $_url[1] : '';
				?>
					<a class="<?=$catFilter == "" ? "selected" : ""?>" href="<?=site_url('projects')?>">ALL</a>
					<a class="<?=$catFilter == "mine" ? "selected" : ""?>" href="<?=site_url('projects/mine')?>">MINE</a>
					<a class="<?=$catFilter == "shared" ? "selected" : ""?>" href="<?=site_url('projects/shared')?>">SHARED WITH ME</a>

				<?php
					// Exclude other category types
					$db->where('cat_type', 'project');

					// Exclude other users
					$db->where('cat_user_ID', currentUserID());


					$projectCatLinks = $db->get('categories', null, 'cat_name');

					foreach ($projectCatLinks as $projectCatLink) {
						echo '<a class="'.($catFilter == permalink($projectCatLink['cat_name']) ? "selected" : "").'" href="'.site_url( 'projects/'.permalink($projectCatLink['cat_name']) ).'">'.strtoupper($projectCatLink['cat_name']).'</a>';
					}

				?>
					<a href="#"><span style="font-family: Arial; font-weight: bold;">+</span></a>

				<?php

					// PROJECT PAGE
					} elseif ($_url[0] == "project") {

						$catFilter = isset($_url[2]) ? $_url[2] : '';
				?>

					<a class="<?=$catFilter == "" ? "selected" : ""?>" href="<?=site_url('project/'.$_url[1])?>">ALL</a>
					<a class="<?=$catFilter == "mine" ? "selected" : ""?>" href="<?=site_url('project/'.$_url[1].'/mine')?>">MINE</a>
					<a class="<?=$catFilter == "shared" ? "selected" : ""?>" href="<?=site_url('project/'.$_url[1].'/shared')?>">SHARED WITH ME</a>


				<?php
					// Exclude other category types
					$db->where('cat_type', $project_ID);

					// Exclude other users
					$db->where('cat_user_ID', currentUserID());


					$catLinks = $db->get('categories', null, 'cat_name');

					foreach ($catLinks as $catLink) {
						echo '<a class="'.($catFilter == permalink($catLink['cat_name']) ? "selected" : "").'" href="'.site_url( 'project/'.$_url[1].'/'.permalink($catLink['cat_name']) ).'">'.strtoupper($catLink['cat_name']).'</a>';
					}

				?>
					<a href="#"><span style="font-family: Arial; font-weight: bold;">+</span></a>
				<?php
					}
				?>


				</div>

				<div class="col xl-3-12 xl-right inline-guys">

					<?php
						// If pages shown
						if ($_url[0] == "project") {

					?>
					<div class="dropdown-container">
						<span class="dropdown-opener">DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown xl-center lower">
							<ul class="device-selector">
								<li class="selected"><a href="#" data-device="5">All</a></li>
								<li><a href="#" data-device="4">Desktop</a></li>
								<li><a href="#" data-device="4">iPhone</a></li>
								<li><a href="#" data-device="3">iPad</a></li>
							</ul>
						</nav>
					</div>
					<?php
						}
					?>

					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener">SIZE <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown xl-center lower">
							<ul class="size-selector">
								<li class="selected"><a href="#" data-column="6">6 Column</a></li>
								<li><a href="#" data-column="5">5 Column</a></li>
								<li><a href="#" data-column="4">4 Column</a></li>
								<li><a href="#" data-column="3">3 Column</a></li>
								<li><a href="#" data-column="2">2 Column</a></li>
							</ul>
						</nav>
					</div>

					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener">SORT <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown xl-center lower">
							<ul class="order-selector">
								<li <?=!isset($_GET['order']) || $_GET['order'] == "custom" ? ' class="selected"' : ""?>><a href="?" data-order="custom">Custom</a></li>
								<li <?=isset($_GET['order']) && $_GET['order'] == "name" ? ' class="selected"' : ""?>><a href="?order=name" data-order="name">By Name</a></li>
								<li <?=isset($_GET['order']) && $_GET['order'] == "date" ? ' class="selected"' : ""?>><a href="?order=date" data-order="date">By Date</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>