			<!-- Toolbar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<span class="search"><img src="<?=asset_url('icons/search.svg')?>" /></span>

				<?php

					$url_prefix = "projects";
					$cat_type = "project";

					// If "Pages" page
					if ($_url[0] == "project") {

						$url_prefix = "project/".$_url[1];
						$cat_type = $_url[1];

					}

				?>
					<a class="<?=$catFilter == "" ? "selected" : ""?>" href="<?=site_url($url_prefix)?>">ALL</a>
					<a class="<?=$catFilter == "mine" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/mine')?>">MINE</a>
					<a class="<?=$catFilter == "shared" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/shared')?>">SHARED WITH ME</a>


				<?php

					// Exclude other category types
					$db->where('cat_type', $cat_type);

					// Exclude other users
					$db->where('cat_user_ID', currentUserID());


					$catLinks = $db->get('categories', null, 'cat_name');
					foreach ($catLinks as $catLink) {

						echo '<a class="'.($catFilter == permalink($catLink['cat_name']) ? "selected" : "").'" href="'.site_url( $url_prefix.'/'.permalink($catLink['cat_name']) ).'">'.strtoupper($catLink['cat_name']).'</a>';

					}

				?>

					<a href="#"><span style="font-family: Arial; font-weight: bold;">+</span></a>

				</div>

				<div class="col xl-3-12 xl-right inline-guys">

					<?php
						// If pages shown
						if ($_url[0] == "project") {
					?>
					<div class="dropdown-container">
						<span class="dropdown-opener">DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown xl-left lower">
							<ul class="device-selector">

								<li <?= $deviceFilter == "" || $deviceFilter == "all" ? ' class="selected"' : ""?>>
									<a href="<?=current_url('', 'device')?>" data-device="5"><i class="fa" aria-hidden="true"></i> All</a>
								</li>

							<?php
							foreach($available_devices as $device) {
							?>

								<li <?=$deviceFilter == $device['device_cat_ID'] ? ' class="selected"' : ""?>>
									<a href="<?=current_url('device='.$device['device_cat_ID'])?>" data-device="4"><i class="fa <?=$device['device_cat_icon']?>" aria-hidden="true"></i> <?=$device['device_cat_name']?></a>
								</li>

							<?php
							}
							?>
							</ul>
						</nav>
					</div>
					<?php
						}
					?>

					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener">SIZE <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown xl-left lower">
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
						<nav class="dropdown xl-left lower">
							<ul class="order-selector">
								<li <?=!isset($_GET['order']) || get('order') == "custom" ? ' class="selected"' : ""?>>
									<a href="<?=current_url('', 'order')?>" data-order="custom">Custom</a>
								</li>

								<li <?=get('order') == "name" ? ' class="selected"' : ""?>><a href="<?=current_url('order=name')?>" data-order="name">By Name</a></li>
								<li <?=get('order') == "date" ? ' class="selected"' : ""?>><a href="<?=current_url('order=date')?>" data-order="date">By Date</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>