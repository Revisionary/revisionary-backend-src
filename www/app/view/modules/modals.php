<div id="add-new" class="popup-window xl-center scrollable-content" data-type="" data-id="" data-iamowner="" data-currentuser-id="<?=currentUserID()?>">
	<h2>Add New <span class="data-type"></span></h2>
	<h5 class="to"></h5>

	<form action="<?=site_url('projects', true)?>" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>
		<input type="hidden" name="project_ID" value="new"/>


		<div class="wrap xl-center">
			<div class="col xl-6-7">


				<h4 class="section-title xl-left">Page Info</h4>

				<h3><i class="fa fa-link"></i> First Page URL <i class="fa fa-question-circle tooltip" data-tooltip="Enter the URL you want to revise" aria-hidden="true"></i></h3>
				<input type="url" name="page-url" placeholder="https://example.com/..." tabindex="1" autofocus required/>
				<small class="design-uploader">or <a href="#" data-tooltip="Coming soon...">Upload</a> your page design <i class="fa fa-question-circle tooltip" data-tooltip="Coming soon..." aria-hidden="true"></i></small>


				<h3 style="margin-bottom: 0">Screen Size <i class="fa fa-question-circle tooltip" data-tooltip="Add your screen size that you wish to edit your site." aria-hidden="true"></i></h3>
				<ul class="no-spacing selected-screens">
					<li>
						<input type="hidden" name="screens[]" value="11"/>
						<input type="hidden" name="page_width" value="1440"/>
						<input type="hidden" name="page_height" value="900"/>
						<i class="fa fa-window-maximize" aria-hidden="true"></i> <span>Current Screen (<span class="screen-width">1440</span> x <span class="screen-height">900</span>)</span>
						<a href="#" class="remove-screen" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
					</li>
				</ul><br/>
				<span class="dropdown">

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

				</span><br/><br/>



				<a href="#" class="option-toggler more-options"><i class="fa fa-sliders-h"></i> More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>
				<a href="#" class="option-toggler less-options"><i class="fa fa-sliders-h"></i> Less Options <i class="fa fa-caret-up" aria-hidden="true"></i><hr/></a>


				<!-- More Options -->
				<div class="more-options-wrapper">



					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this page like Home, About, ..." aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="2"/>


					<h3><i class="fa fa-share-alt"></i> Page Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access only this page." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.getUserInfo()['userName'])?>">
							<picture class="profile-picture" <?=getUserInfo()['printPicture']?>>
								<span <?=getUserInfo()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserInfo()['nameAbbr']?></span>
							</picture>
						</a>

						<ul class="shares page user">

						</ul>


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>

						<input class="share-email" type="email" data-type="page" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


						<ul class="shares page email">

						</ul>


					</div><br/><br/>



					<div class="project-info">


						<h4 class="section-title xl-left">Project Info</h4>

						<h3>Project Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this project." aria-hidden="true"></i></h3>
						<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..." tabindex="3" />


						<h3><i class="fa fa-share-alt xl-hidden"></i> Project Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access this project with all the pages in it." aria-hidden="true"></i></h3>
						<div class="people">


							<!-- Owner -->
							<a href="<?=site_url('profile/'.getUserInfo()['userName'])?>">
								<picture class="profile-picture" <?=getUserInfo()['printPicture']?>>
									<span <?=getUserInfo()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserInfo()['nameAbbr']?></span>
								</picture>
							</a>

							<ul class="shares project user">

							</ul>


							<!-- Add New -->
							<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>
							<input class="share-email" type="email" name="project-share-email" data-type="project" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


							<ul class="shares project email">

							</ul>


						</div><br/>

					</div>


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





<div id="share" class="popup-window xl-center xl-5-12 scrollable-content" data-type="" data-id="" data-iamowner="" data-currentuser-id="<?=currentUserID()?>">
	<h2>Share</h2>
	<h5 class="to">The <b><span class="data-name"></b> <span class="data-type"></span></h5>



	<div class="wrap xl-center xl-gutter-8">
		<div class="col xl-9-10">




			<!-- THEAD -->
			<div class="wrap xl-table xl-gutter-24">
				<div class="col">

					<h4 style="margin-bottom: 10px;">Member</h4>

				</div>
				<div class="col xl-3-8 xl-right">

					<h4 style="margin-bottom: 10px;">Access Level</h4>

				</div>
			</div>





			<!-- MEMBERS -->
			<ul class="xl-left no-spacing members">

			</ul><br/>






			<form action="" method="post">
				<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>


				<!-- Add New -->
				<div class="wrap xl-table xl-gutter-24">
					<div class="col xl-3-8 hide-when-project">

						<h4 style="margin-bottom: 15px;">Access Level</h4>
						<span class="text-uppercase dropdown">

							<a href="#">THIS <span class="data-type"></span> <i class="fa fa-caret-down"></i></a>
							<ul class="selectable">
								<li class="selected"><a href="#">THIS <span class="data-type"></span></a></li>
								<li><a href="#">WHOLE PROJECT</a></li>
							</ul>

						</span>

					</div>
					<div class="col">

						<h4 class="xl-center" style="margin-bottom: 10px;">Add New User</h4>
						<input id="share-email" class="share-email" type="email" placeholder='Type an e-mail address and hit "Enter"...' autofocus />

					</div>
				</div><br/>


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


			</form>



		</div>
	</div>


</div>