			<!-- Toolbar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<div class="filter-blocks">
						<input type="text" placeholder="Type here..." class="search-in-page"/>
						<i class="fa fa-search" data-tooltip="Search in <?=$dataType?>s"></i>
					</div>

				<?php

					$url_prefix = "projects";
					$cat_type = "project";

					// If "Pages" page
					if ($_url[0] == "project") {

						$url_prefix = "project/".$_url[1];
						$cat_type = $_url[1];

					}

				?>
					<a class="<?=$catFilter == "" ? "selected" : ""?>" href="<?=site_url($url_prefix)?>">All</a>
					<a class="<?=$catFilter == "mine" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/mine')?>">Mine</a>
					<a class="<?=$catFilter == "shared" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/shared')?>">Shared With Me</a>


				<?php

					foreach ($categories as $catLink) {

						// Skip the Uncategorized category
						if ($catLink['cat_ID'] == 0) continue;


						echo '<a class="'.($catFilter == permalink($catLink['cat_name']) ? "selected" : "").'" href="'.site_url( $url_prefix.'/'.permalink($catLink['cat_name']) ).'" data-cat-id="'.$catLink['cat_ID'].'">'.$catLink['cat_name'].'</a>';

					}

				?>
					<?php



					$action_url = 'ajax?type=data-action&data-type='.$dataType.'&nonce='.$_SESSION['js_nonce'];

					if ($dataType == "page")
						$action_url .= '&project_ID='.$project_ID;

					?>
					<a href="<?=site_url($action_url.'&action=add-new-category')?>" class="action" data-actionn="add-new-category" data-tooltip="Add New Category"><span style="font-family: Arial; font-weight: bold;"><i class="fa fa-plus"></i></span></a>

				</div>

				<div class="col xl-3-12 xl-right inline-guys">

					<?php
						// If pages shown
						if ($_url[0] == "project") {
					?>
					<div class="dropdown-container">
						<span class="dropdown-opener">DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown selectable xl-left">
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
						<nav class="dropdown selectable xl-left">
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
						<nav class="dropdown selectable xl-left">
							<ul class="order-selector">
								<li <?=!isset($_GET['order']) || get('order') == "custom" ? ' class="selected"' : ""?>>
									<a href="<?=current_url('', 'order')?>" data-order="custom">Custom</a>
								</li>

								<li <?=get('order') == "name" ? ' class="selected"' : ""?>><a href="<?=current_url('order=name')?>" data-order="name">By Name</a></li>
								<li <?=get('order') == "date" ? ' class="selected"' : ""?>><a href="<?=current_url('order=date')?>" data-order="date">By Date</a></li>
							</ul>
						</nav>
					</div>


					<?php
						// If projects shown
						if ($_url[0] == "projects") {
					?>
					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener">TOOLS <i class="fa fa-caret-down" aria-hidden="true"></i></span>
						<nav class="dropdown higher xl-left lower">
							<ul class="tool-selector">
								<li data-tooltip="Coming Soon..."><a href="#">Github Integration</a></li>
								<li data-tooltip="Coming Soon..."><a href="#">BitBucket Integration</a></li>
								<li data-tooltip="Coming Soon..."><a href="#">Asana Integration</a></li>
								<li data-tooltip="Coming Soon..."><a href="#">Trello Integration</a></li>
								<li data-tooltip="Coming Soon..."><a href="#">More Apps...</a></li>

							</ul>
						</nav>
					</div>
					<?php
						}
					?>


				</div>
			</div>