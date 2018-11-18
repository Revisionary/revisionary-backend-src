<?php
function print_device_selector() {
	global $db, $device_data;
?>

<h3 style="margin-bottom: 0">Screen Size <i class="fa fa-question-circle tooltip" data-tooltip="Add your device size that you wish to edit your site." aria-hidden="true"></i></h3>
<ul class="selected-devices">
	<li>
		<input type="hidden" name="devices[]" value="11"/>
		<input type="hidden" name="page-width" value="1440"/>
		<input type="hidden" name="page-height" value="900"/>
		<i class="fa fa-window-maximize" aria-hidden="true"></i> <span>Current Screen (<span class="screen-width">1440</span> x <span class="screen-height">900</span>)</span>
		<a href="#" class="remove-device" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
	</li>
</ul>
<span class="dropdown-container">

	<span class="dropdown-opener add-device"><i class="fa fa-plus" aria-hidden="true"></i> ADD ANOTHER SCREEN</span>

	<nav class="dropdown xl-left">
		<ul class="device-adder">
			<?php
			foreach ($device_data as $device_cat) {
			?>

			<li class="device-cat" <?=$device_cat['device_cat_name'] == "Custom" ? "style='display:none;'" : ""?>>

				<div class="dropdown-container">
					<div class="dropdown-opener">
						<i class="fa <?=$device_cat['device_cat_icon']?>" aria-hidden="true"></i> <?=$device_cat['device_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
					</div>
					<nav class="dropdown selectable addable xl-left">
						<ul class="device-add">
							<?php
							foreach ($device_cat['devices'] as $device) {
							?>
							<li class="device" <?=$device['device_ID'] == 11 ? "style='display:none;'" : ""?>>
								<a href="#"
									data-device-id="<?=$device['device_ID']?>"
									data-device-width="<?=$device['device_width']?>"
									data-device-height="<?=$device['device_height']?>"
									data-device-cat-name="<?=$device_cat['device_cat_name']?>"
									data-device-cat-icon="<?=$device_cat['device_cat_icon']?>"
								>
									<?=$device['device_name']?> (<span class="<?=$device['device_ID'] == 11 ? "screen-" : "device-"?>width"><?=$device['device_width']?></span> x <span class="<?=$device['device_ID'] == 11 ? "screen-" : "device-"?>height"><?=$device['device_height']?></span>)
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

<?php
}
?>



<?php
function page_members() {
?>



<h3><i class="fa fa-share-alt"></i> Page Members <i class="fa fa-question-circle tooltip" data-tooltip="Sharing to emails that are not users is not working right now." aria-hidden="true"></i></h3>
<div class="people">


	<!-- Owner -->
	<a href="<?=site_url('profile/'.getUserData()['userName'])?>">
		<picture class="profile-picture" <?=getUserData()['printPicture']?>>
			<span <?=getUserData()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData()['nameAbbr']?></span>
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


<?php
}
?>



<div id="add-new-project" class="popup-window xl-center scrollable-content">
	<h2>Add New Project</h2>
	<h5 class="to">To <b>Main Projects</b> Section</h5>

	<form action="<?=site_url('projects', true)?>" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>


		<div class="wrap xl-center">
			<div class="col xl-5-7">


				<h4 class="section-title xl-left">Project Info</h4>

				<h3>Project Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this project." aria-hidden="true"></i></h3>
				<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..." tabindex="1" autofocus required/>


				<a href="#" class="option-toggler more-options">More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>

				<a href="#" class="option-toggler less-options">Less Options <i class="fa fa-caret-up" aria-hidden="true"></i></a>


				<!-- More Options -->
				<div class="more-options-wrapper">


					<h3>Project Members <i class="fa fa-question-circle tooltip" data-tooltip="Sharing to emails that are not users is not working right now." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.getUserData()['userName'])?>">
							<picture class="profile-picture" <?=getUserData()['printPicture']?>>
								<span <?=getUserData()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData()['nameAbbr']?></span>
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

					<h3><i class="fa fa-link"></i> First Page URL <i class="fa fa-question-circle tooltip" data-tooltip="Enter the URL you want to revise" aria-hidden="true"></i></h3>
					<input type="url" name="page-url" placeholder="https://example.com/..." tabindex="2" required disabled/>
					<small class="design-uploader">or <a href="#" data-tooltip="Not working yet.">Upload</a> your page design <i class="fa fa-question-circle tooltip" data-tooltip="Not working yet." aria-hidden="true"></i></small>


					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this page like Home, About, ..." aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="3" required disabled/>


					<?=print_device_selector()?>


					<?=page_members()?>

					<br/>


				</div>


				<!-- Actions -->
				<div class="wrap xl-2 xl-center xl-flexbox">
					<div class="col">

						<button class="dark small">Add</button>

					</div>
					<div class="col xl-first">

						<button class="cancel-button light small">Cancel</button>

					</div>
				</div>
				<br/>


			</div>
		</div>

	</form>


</div>











<?php
if ( isset($project_ID) ) {
?>


<div id="add-new-page" class="popup-window xl-center scrollable-content">
	<h2>Add New Page</h2>
	<h5 class="to">To <b>Main Pages</b> Section</h5>

	<form action="<?=site_url('project/'.$project_ID, true)?>" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>
		<input type="hidden" name="project_ID" value="<?=$project_ID?>"/>

		<div class="wrap xl-center xl-gutter-8">
			<div class="col xl-9-10">


				<h3><i class="fa fa-link"></i> URL <i class="fa fa-question-circle tooltip" data-tooltip="Enter the URL you want to revise" aria-hidden="true"></i></h3>
				<input type="url" name="page-url" placeholder="https://example.com/..." tabindex="1" autofocus required/>
				<small class="design-uploader">or <a href="#" data-tooltip="Not working yet.">Upload</a> your page design <i class="fa fa-question-circle tooltip" data-tooltip="Not working yet." aria-hidden="true"></i></small>


				<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="Test message" aria-hidden="true"></i></h3>
				<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="2" required/>



				<?=print_device_selector()?>


				<br/>
				<br/>
				<a href="#" class="option-toggler more-options"><i class="fa fa-share-alt"></i> Sharing Options (Optional) <i class="fa fa-caret-down" aria-hidden="true"></i></a>

				<a href="#" class="option-toggler less-options"><i class="fa fa-share-alt"></i> Hide Sharing Options <i class="fa fa-caret-up" aria-hidden="true"></i></a>


				<!-- More Options -->
				<div class="more-options-wrapper">

					<?=page_members()?>
					<br/>

				</div>



				<!-- Actions -->
				<div class="wrap xl-2 xl-center xl-flexbox">
					<div class="col">

						<button class="dark small">Add</button>

					</div>
					<div class="col xl-first">

						<button class="cancel-button light small">Cancel</button>

					</div>
				</div>
				<br/>


			</div>
		</div>

	</form>


</div>

<?php
}
?>













<div id="share" class="popup-window xl-center xl-5-12 scrollable-content">
	<h2>Share</h2>
	<h5 class="to">The <b><?=isset($page_ID) ? Page::ID($page_ID)->getInfo('page_name') : "NAME"?></b> <span class="data-type"><?=ucfirst($dataType)?></span></h5>

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

								<?php $project_user_ID = $projectInfo['user_ID']; ?>

								<picture class="profile-picture big" <?=getUserData($project_user_ID)['printPicture']?>>
									<span <?=getUserData($project_user_ID)['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData($project_user_ID)['nameAbbr']?></span>
								</picture>

								<div>
									<span class="full-name"><?=getUserData($project_user_ID)['fullName']?></span>
									<span class="email">(<?=getUserData($project_user_ID)['email']?>)</span>
									<span class="owner-badge">Owner</span>
								</div>

							</li>



							<?php
							foreach ($projectShares as $share) {
							?>

								<?php if ( is_numeric($share['share_to']) ) { ?>

								<!-- Shared Person -->
								<li class="inline-guys member">

									<picture class="profile-picture big" <?=getUserData($share['share_to'])['printPicture']?>>
										<span <?=getUserData($share['share_to'])['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData($share['share_to'])['nameAbbr']?></span>
									</picture>

									<div>
										<span class="full-name"><?=getUserData($share['share_to'])['fullName']?></span>
										<span class="email">(<?=getUserData($share['share_to'])['email']?>)</span>
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



				<ul class="xl-left members <?=isset($page_ID) ? "page-php" : ""?>">
				<?php

					if ( isset($page_ID) ) {

						$page_user_ID = Page::ID($page_ID)->getInfo('user_ID');

				?>

						<!-- Owner -->
						<li class="inline-guys member">

							<picture class="profile-picture big" <?=getUserData($page_user_ID)['printPicture']?>>
								<span <?=getUserData($page_user_ID)['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData($page_user_ID)['nameAbbr']?></span>
							</picture>

							<div>
								<span class="full-name"><?=getUserData($page_user_ID)['fullName']?></span>
								<span class="email">(<?=getUserData($page_user_ID)['email']?>)</span>
								<span class="owner-badge">Owner</span>
							</div>

						</li>




					<?php
					foreach ($pageShares as $share) {
					?>

						<?php if ( is_numeric($share['share_to']) ) { ?>

						<!-- Shared Person -->
						<li class="inline-guys member user">

							<picture class="profile-picture big" <?=getUserData($share['share_to'])['printPicture']?>>
								<span <?=getUserData($share['share_to'])['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData($share['share_to'])['nameAbbr']?></span>
							</picture>

							<div>
								<span class="full-name"><?=getUserData($share['share_to'])['fullName']?></span>
								<span class="email">(<?=getUserData($share['share_to'])['email']?>)</span>
							</div>

							<a href="#" class="remove remove-member" data-userid="<?=$share['share_to']?>" data-id="<?=$page_ID?>"><i class="fa fa-times-circle" aria-hidden="true"></i></a>

						</li>

						<?php } else { ?>


						<li class="inline-guys member email">

							<picture class="profile-picture big" >
								<span><i class="fa fa-envelope" aria-hidden="true"></i></span>
							</picture>

							<div>
								<span class="email"><?=$share['share_to']?></span>
							</div>

							<a href="#" class="remove remove-member" data-userid="<?=$share['share_to']?>" data-id="<?=$page_ID?>"><i class="fa fa-times-circle" aria-hidden="true"></i></a>

						</li>


						<?php } ?>

					<?php
					}
					?>





				<?php } ?>

				</ul><br/>



				<!-- Add New -->
				<input id="share-email" class="share-email" type="email" data-type="<?=$dataType?>" data-id="<?=isset($page_ID) ? $page_ID : ""?>" placeholder='Type an e-mail address and hit "Enter"...' style="max-width: 75%;"/><br/><br/>





				<!-- Actions -->
				<div class="wrap xl-2 xl-center xl-flexbox">
					<div class="col">


						<button class="dark small add-member" disabled>Add</button>


					</div>
					<div class="col xl-first">


						<button class="cancel-button light small">Close</button>


					</div>
				</div>
				<br/>


			</div>
		</div>

	</form>


</div>