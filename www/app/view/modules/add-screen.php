<nav class="dropdown xl-left">
	<ul class="screen-adder">
		<?php
		foreach ($screen_data as $screen_cat) {
		?>

		<li>

			<div class="dropdown-container">
				<div class="dropdown-opener">
					<i class="fa <?=$screen_cat['screen_cat_icon']?>" aria-hidden="true"></i> <?=$screen_cat['screen_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
				</div>
				<nav class="dropdown selectable addable xl-left">
					<ul class="screen-addd">
						<?php
						foreach ($screen_cat['screens'] as $screen) {

							$screen_link = current_url("new_screen=".$screen['screen_ID']."&page_ID=".$block['page_ID']);
							$screen_label = $screen['screen_name']." (".$screen['screen_width']."x".$screen['screen_height'].")";
							if ($screen['screen_ID'] == 11) {
								$screen_link = queryArg('page_width='.$screen['screen_width'], $screen_link);
								$screen_link = queryArg('page_height='.$screen['screen_height'], $screen_link);
								$screen_label = $screen['screen_name']." (<span class='screen-width'>".$screen['screen_width']."</span>x<span class='screen-height'>".$screen['screen_height']."</span>)";
							}

							$screen_link = queryArg('nonce='.$_SESSION["new_screen_nonce"], $screen_link);
						?>
						<li>
							<a href="<?=$screen_link?>"
								class="new-screen"
								data-screen-id="<?=$screen['screen_ID']?>"
								data-screen-width="<?=$screen['screen_width']?>"
								data-screen-height="<?=$screen['screen_height']?>"
								data-screen-cat-name="<?=$screen_cat['screen_cat_name']?>"
								data-screen-cat-icon="<?=$screen_cat['screen_cat_icon']?>"
							>
								<span><?=$screen_label?></span>
							</a>
						</li>
						<?php
						}

						// Custom Screen
						if ($screen_cat['screen_cat_name'] == "Custom...") {
						?>
						<li><a href="#" data-screen-id="<?=$screen['screen_ID']?>">Add New</a></li>
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