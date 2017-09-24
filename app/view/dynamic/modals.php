<div id="add-new" class="popup-window xl-center scrollable-content">
	<h2>Add New <?=ucfirst($dataType)?></h2>
	<h5 class="to">To <b>Main Pages</b> Section</h5>

	<form action="" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>


		<?php

		if ($dataType == "project") {

		?>

		<div class="wrap xl-center">
			<div class="col xl-5-7">


				<a href="#" class="option-toggler less-options">Less Options <i class="fa fa-caret-up" aria-hidden="true"></i></a>


				<h4 class="section-title xl-left">Project Info</h4>

				<h3>Site Name <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
				<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..." required/>


				<a href="#" class="option-toggler more-options">More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>


				<!-- More Options -->
				<div class="more-options-wrapper">


					<h3>Project Members <i class="fa fa-question-circle tooltip" data-tooltip="Sharing to emails that are not users is not working right now." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.User::ID()->userName)?>">
							<picture class="profile-picture" <?=User::ID()->printPicture()?>>
								<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							</picture>
						</a>

						<ul class="shares project user">

						</ul>


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>

						<input class="share-email" type="email" name="project-share-email" data-type="project" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


						<ul class="shares project email">

						</ul>


					</div>
					<br/>


					<h4 class="section-title xl-left">Page Info</h4>

					<h3>First Page URL <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
					<input type="url" name="page-url" placeholder="https://example.com/..." required disabled/>


					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." required disabled/>


					<h3 style="margin-bottom: 0">Devices <i class="fa fa-question-circle tooltip" data-tooltip="Custom device option is not working right now." aria-hidden="true"></i></h3>
					<ul class="selected-devices">
						<li>
							<input type="hidden" name="devices[]" value="4"/>
							<i class="fa fa-laptop" aria-hidden="true"></i> <span>Current Screen (1400 x 900)</span>
							<a href="#" class="remove-device" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</li>
					</ul>
					<span class="dropdown-container">

						<span class="dropdown-opener add-device">ADD DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></span>

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
											<ul class="device-add">
												<?php
												$db->where('device_cat_ID', $device_cat['device_cat_ID']);
												$db->orderBy('device_order', 'asc');
												$devices = $db->get('devices');
												foreach ($devices as $device) {
												?>
												<li>
													<a href="#"
														data-device-id="<?=$device['device_ID']?>"
														data-device-width="<?=$device['device_width']?>"
														data-device-height="<?=$device['device_height']?>"
														data-device-cat-name="<?=$device_cat['device_cat_name']?>"
														data-device-cat-icon="<?=$device_cat['device_cat_icon']?>"
													>
														<?=$device['device_name']?> (<?=$device['device_width']?>x<?=$device['device_height']?>)
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

					</span>


					<h3>Page Members <i class="fa fa-question-circle tooltip" data-tooltip="Sharing to emails that are not users is not working right now." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.User::ID()->userName)?>">
							<picture class="profile-picture" <?=User::ID()->printPicture()?>>
								<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							</picture>
						</a>

						<ul class="shares page user">

						</ul>


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>

						<input class="share-email" type="email" data-type="page" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


						<ul class="shares page email">

						</ul>


					</div>
					<br/>


				</div>



				<!-- Actions -->
				<div class="wrap xl-2 xl-center">
					<div class="col">


						<button class="cancel-button light small">Cancel</button>


					</div>
					<div class="col">


						<button class="dark small">Add</button>


					</div>
				</div>
				<br/>


			</div>
		</div>

		<?php

		} elseif ($dataType == "page") {

		?>

		<input type="hidden" name="project_ID" value="<?=$project_ID?>"/>

		<div class="wrap xl-center xl-gutter-8">
			<div class="col xl-9-10">


				<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
				<input type="text" name="page-name" placeholder="e.g. Home, About, ..." required/>


				<h3>URL <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
				<input type="url" name="page-url" placeholder="https://example.com/..." required/>


				<a href="#" class="option-toggler more-options">More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>

				<a href="#" class="option-toggler less-options">Less Options <i class="fa fa-caret-up" aria-hidden="true"></i></a>


				<!-- More Options -->
				<div class="more-options-wrapper">

					<h3 style="margin-bottom: 0">Devices <i class="fa fa-question-circle tooltip" data-tooltip="Custom device option is not working right now." aria-hidden="true"></i></h3>
					<ul class="selected-devices">
						<li>
							<input type="hidden" name="devices[]" value="4"/>
							<i class="fa fa-laptop" aria-hidden="true"></i> <span>Current Screen (1400 x 900)</span>
							<a href="#" class="remove-device" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</li>
					</ul>
					<span class="dropdown-container">

						<span class="dropdown-opener add-device">ADD DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></span>

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
											<ul class="device-add">
												<?php
												$db->where('device_cat_ID', $device_cat['device_cat_ID']);
												$db->orderBy('device_order', 'asc');
												$devices = $db->get('devices');
												foreach ($devices as $device) {
												?>
												<li>
													<a href="#"
														data-device-id="<?=$device['device_ID']?>"
														data-device-width="<?=$device['device_width']?>"
														data-device-height="<?=$device['device_height']?>"
														data-device-cat-name="<?=$device_cat['device_cat_name']?>"
														data-device-cat-icon="<?=$device_cat['device_cat_icon']?>"
													>
														<?=$device['device_name']?> (<?=$device['device_width']?>x<?=$device['device_height']?>)
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

					</span>


					<h3>Members <i class="fa fa-question-circle tooltip" data-tooltip="Sharing to emails that are not users is not working right now." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.User::ID()->userName)?>">
							<picture class="profile-picture" <?=User::ID()->printPicture()?>>
								<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							</picture>
						</a>

						<ul class="shares page user">

						</ul>


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>

						<input class="share-email" type="email" data-type="page" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


						<ul class="shares page email">

						</ul>


					</div>
					<br/>


				</div>



				<!-- Actions -->
				<div class="wrap xl-2 xl-center">
					<div class="col">


						<button class="cancel-button light small">Cancel</button>


					</div>
					<div class="col">


						<button class="dark small">Add</button>


					</div>
				</div>
				<br/>


			</div>
		</div>

		<?php

		}

		?>

	</form>


</div>















<div id="share" class="popup-window xl-center xl-5-12 scrollable-content">
	<h2>Share</h2>
	<h5 class="to">The <b>Name</b> <span class="data-type"><?=ucfirst($dataType)?></span></h5>

	<form action="" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="project_ID" value=""/>



		<div class="wrap xl-center xl-gutter-8">
			<div class="col xl-9-10">

				<?php if ( $dataType == "page" ) { ?>

					<div class="project-shares">
						<h4 class="xl-left">Project Members <i class="fa fa-question-circle tooltip" data-tooltip="These members below can access all the pages in this project." aria-hidden="true"></i></h4>


						<ul class="xl-left members project-php">

							<!-- Owner -->
							<li class="inline-guys member">

								<picture class="profile-picture big" <?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->printPicture()?>>
									<span <?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->firstName, 0, 1).substr(User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->lastName, 0, 1)?></span>
								</picture>

								<div>
									<span class="full-name"><?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->fullName?></span>
									<span class="email">(<?=User::ID(Project::ID( $_url[1])->getProjectInfo('user_ID'))->email?>)</span>
									<span class="owner-badge">Owner</span>
								</div>

							</li>



							<?php
							foreach ($projectShares as $share) {
							?>

								<?php if ( is_numeric($share['share_to']) ) { ?>

								<!-- Shared Person -->
								<li class="inline-guys member">

									<picture class="profile-picture big" <?=User::ID($share['share_to'])->printPicture()?>>
										<span <?=User::ID($share['share_to'])->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID($share['share_to'])->firstName, 0, 1).substr(User::ID($share['share_to'])->lastName, 0, 1)?></span>
									</picture>

									<div>
										<span class="full-name"><?=User::ID($share['share_to'])->fullName?></span>
										<span class="email">(<?=User::ID($share['share_to'])->email?>)</span>
									</div>

									<!-- <a href="#" class="remove remove-member"><i class="fa fa-times-circle" aria-hidden="true"></i></a> -->

								</li>

								<?php } else { ?>


								<li class="inline-guys member">

									<picture class="profile-picture big" >
										<span><i class="fa fa-envelope" aria-hidden="true"></i></span>
									</picture>

									<div>
										<span class="email"><?=$share['share_to']?></span>
									</div>

									<!-- <a href="#" class="remove remove-member"><i class="fa fa-times-circle" aria-hidden="true"></i></a> -->

								</li>


								<?php } ?>

							<?php
							}
							?>

						</ul><br/>
					</div>

				<?php } ?>

				<h4 class="xl-left"><span class="data-type"><?=ucfirst($dataType)?></span> Members <i class="fa fa-question-circle tooltip" data-tooltip="The members who can access this <?=$dataType?>." aria-hidden="true"></i></h4>



				<ul class="xl-left members">

				</ul><br/>



				<!-- Add New -->
				<input id="share-email" class="share-email" type="email" data-type="<?=$dataType?>" data-id="" placeholder='Type an e-mail address and hit "Enter"...' style="max-width: 75%;"/><br/><br/>





				<!-- Actions -->
				<div class="wrap xl-2 xl-center">
					<div class="col">


						<button class="cancel-button light small">Close</button>


					</div>
					<div class="col">


						<button class="dark small add-member" disabled>Add</button>


					</div>
				</div>
				<br/>


			</div>
		</div>

	</form>


</div>