<?php
function print_screen_selector() {
	global $db, $screen_data;
?>

<h3 style="margin-bottom: 0">Screen Size <i class="fa fa-question-circle tooltip" data-tooltip="Add your screen size that you wish to edit your site." aria-hidden="true"></i></h3>
<ul class="selected-screens">
	<li>
		<input type="hidden" name="screens[]" value="11"/>
		<input type="hidden" name="page_width" value="1440"/>
		<input type="hidden" name="page_height" value="900"/>
		<i class="fa fa-window-maximize" aria-hidden="true"></i> <span>Current Screen (<span class="screen-width">1440</span> x <span class="screen-height">900</span>)</span>
		<a href="#" class="remove-screen" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
	</li>
</ul>
<span class="new_dropdown">

	<a href="#" class="add-screen"><i class="fa fa-plus" aria-hidden="true"></i> ADD ANOTHER SCREEN</a>
	<ul class="xl-left screen-adder">
		<?php
		foreach ($screen_data as $screen_cat) {
		?>

		<li class="screen-cat" <?=$screen_cat['screen_cat_name'] == "Custom" ? "style='display:none;'" : ""?>>

			<a href="#">
				<i class="fa <?=$screen_cat['screen_cat_icon']?>" aria-hidden="true"></i> <?=$screen_cat['screen_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
			</a>
			<ul class="addable xl-left screen-add">
				<?php
				foreach ($screen_cat['screens'] as $screen) {
				?>
				<li class="screen" <?=$screen['screen_ID'] == 11 ? "style='display:none;'" : ""?>>
					<a href="#"
						data-screen-id="<?=$screen['screen_ID']?>"
						data-screen-width="<?=$screen['screen_width']?>"
						data-screen-height="<?=$screen['screen_height']?>"
						data-screen-cat-name="<?=$screen_cat['screen_cat_name']?>"
						data-screen-cat-icon="<?=$screen_cat['screen_cat_icon']?>"
					>
						<?=$screen['screen_name']?> (<span class="<?=$screen['screen_ID'] == 11 ? "screen-" : "screen-"?>width"><?=$screen['screen_width']?></span> x <span class="<?=$screen['screen_ID'] == 11 ? "screen-" : "screen-"?>height"><?=$screen['screen_height']?></span>)
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

		</li>

		<?php
		}
		?>
	</ul>

</span>

<?php
}
?>



<?php
function page_members() {
?>



<h3><i class="fa fa-share-alt"></i> Page Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access only this page." aria-hidden="true"></i></h3>
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


				<h4 class="section-title xl-left">Page Info</h4>

				<h3><i class="fa fa-link"></i> First Page URL <i class="fa fa-question-circle tooltip" data-tooltip="Enter the URL you want to revise" aria-hidden="true"></i></h3>
				<input type="url" name="page-url" placeholder="https://example.com/..." tabindex="1" autofocus required/>
				<small class="design-uploader">or <a href="#" data-tooltip="Coming soon...">Upload</a> your page design <i class="fa fa-question-circle tooltip" data-tooltip="Coming soon..." aria-hidden="true"></i></small>


				<?=print_screen_selector()?><br/><br/>



				<a href="#" class="option-toggler more-options"><i class="fa fa-sliders-h"></i> More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>
				<a href="#" class="option-toggler less-options"><i class="fa fa-sliders-h"></i> Less Options <i class="fa fa-caret-up" aria-hidden="true"></i><hr/></a>


				<!-- More Options -->
				<div class="more-options-wrapper">



					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this page like Home, About, ..." aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="2"/>


					<?=page_members()?><br/><br/>




					<h4 class="section-title xl-left">Project Info</h4>

					<h3>Project Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this project." aria-hidden="true"></i></h3>
					<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..." tabindex="3" />




					<h3><i class="fa fa-share-alt xl-hidden"></i> Project Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access this project with all the pages in it." aria-hidden="true"></i></h3>
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
				<small class="design-uploader">or <a href="#" data-tooltip="Coming soon...">Upload</a> your page design <i class="fa fa-question-circle tooltip" data-tooltip="Coming soon..." aria-hidden="true"></i></small>


				<?=print_screen_selector()?>


				<br/>
				<br/>
				<a href="#" class="option-toggler more-options"><i class="fa fa-sliders-h"></i> More Options (Optional) <i class="fa fa-caret-down" aria-hidden="true"></i></a>
				<a href="#" class="option-toggler less-options"><i class="fa fa-sliders-h"></i> Less Options <i class="fa fa-caret-up" aria-hidden="true"></i><hr/></a>


				<!-- More Options -->
				<div class="more-options-wrapper">


					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this page like Home, About, ..." aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="2"/>


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