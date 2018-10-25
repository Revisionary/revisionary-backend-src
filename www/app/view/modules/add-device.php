<nav class="dropdown xl-left">
	<ul class="device-adder">
		<?php
		$db->orderBy('device_cat_order', 'asc');
		$device_cats = $db->get('device_categories');
		foreach ($device_cats as $device_cat) {
		?>

		<li>

			<div class="dropdown-container">
				<div class="dropdown-opener">
					<i class="fa <?=$device_cat['device_cat_icon']?>" aria-hidden="true"></i> <?=$device_cat['device_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
				</div>
				<nav class="dropdown selectable addable xl-left">
					<ul class="device-addd">
						<?php
						$db->where('device_cat_ID', $device_cat['device_cat_ID']);
						$db->where('device_user_ID', 1);
						$db->orderBy('device_order', 'asc');
						$devices = $db->get('devices');
						foreach ($devices as $device) {

							$device_link = current_url("new_device=".$device['device_ID']."&page_ID=".$block['page_ID']);
							$device_label = $device['device_name']." (".$device['device_width']."x".$device['device_height'].")";
							if ($device['device_ID'] == 11) {
								$device_link = queryArg('page_width='.$device['device_width'], $device_link);
								$device_link = queryArg('page_height='.$device['device_height'], $device_link);
								$device_label = $device['device_name']." (<span class='screen-width'>".$device['device_width']."</span>x<span class='screen-height'>".$device['device_height']."</span>)";
							}

							$device_link = queryArg('nonce='.$_SESSION["new_device_nonce"], $device_link);
						?>
						<li>
							<a href="<?=$device_link?>"
								class="new-device"
								data-device-id="<?=$device['device_ID']?>"
								data-device-width="<?=$device['device_width']?>"
								data-device-height="<?=$device['device_height']?>"
								data-device-cat-name="<?=$device_cat['device_cat_name']?>"
								data-device-cat-icon="<?=$device_cat['device_cat_icon']?>"
							>
								<span><?=$device_label?></span>
							</a>
						</li>
						<?php
						}

						// Custom Device
						if ($device_cat['device_cat_name'] == "Custom...") {
						?>
						<li><a href="#" data-device-id="<?=$device['device_ID']?>">Add New</a></li>
						<?php
						}
						?>
					</ul>
				</nav>

			</div>

		</li>

		<?php
		}
		?>
	</ul>
</nav>