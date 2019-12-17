<div id="add-new" class="popup-window xl-center scrollable-content" data-type="" data-id="" data-iamowner="" data-currentuser-id="<?=currentUserID()?>">
	<h2>Add New <span class="data-type"></span></h2>
	<h5 class="to"></h5>

	<form action="<?=site_url('projects', true)?>" method="post" class="new-project-form" data-page-type="url">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>
		<input type="hidden" name="project_ID" value="new"/>


		<div class="wrap xl-center">
			<div class="col xl-6-7">


				<h4 class="section-title xl-left">Page Info</h4>

				<div class="top-option page-url">
					<h3><i class="fa fa-link"></i> <span class="first-page">First</span> Page URL <i class="fa fa-question-circle tooltip" data-tooltip="Enter the URL you want to revise" aria-hidden="true"></i></h3>
					<input type="url" name="page-url" placeholder="https://example.com/..." tabindex="1" autofocus required/>
				</div>
				<div class="top-option selected-image">
					<h3><i class="fa fa-image"></i> Selected Image</h3>
					<figure>
						<label for="reset" class="reset left-tooltip" data-tooltip="Cancel">&times;</label>
						<input id="reset" type="reset" class="xl-hidden">
						<img src="//:0">
					</figure>
				</div>


				<div class="bottom-option design-uploader">
					<small>or <label for="design-uploader"><b><u>Upload</u></b></label> your page design <i class="fa fa-question-circle tooltip bottom-tooltip" data-tooltip="Upload design images to add your comments."></i></small>
					<input type="file" name="design-upload" id="design-uploader" class="design-upload xl-hidden" accept=".gif,.jpg,.jpeg,.png" data-max-size="15000000">
				</div>
				<div class="bottom-option page-options">
					
					<div class="wrap xl-gutter-40 xl-center">
						<div class="col">
							<label class="bottom-tooltip" data-tooltip="This allows you to download the live URL and change the content."><input type="radio" name="page-type" value="url" checked>Live Mode <small>(Recommended)</small></label>
							<label class="xl-hidden"><input type="radio" name="page-type" value="image">Image Mode</label>
						</div>
						<div class="col">
							<label class="bottom-tooltip" data-tooltip="In development... This mode will take full size picture of your page you entered. You can only put comments on it." disabled><input type="radio" name="page-type" value="image" disabled>Capture Mode</label>
						</div>
					</div>
					
				</div>


				<h3 style="margin-bottom: 0">Screen Size <i class="fa fa-question-circle tooltip" data-tooltip="Add your screen size that you wish to edit your site." aria-hidden="true"></i></h3>
				<ul class="no-spacing selected-screens">
					<li>
						<input type="hidden" name="screens[]" value="11"/>
						<input type="hidden" name="page_width" value="1440"/>
						<input type="hidden" name="page_height" value="900"/>
						<i class="fa fa-window-maximize" aria-hidden="true"></i> <span>Current Window (<span class="screen-width">1440</span> x <span class="screen-height">900</span>)</span>
						<a href="#" class="remove-screen" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
					</li>
				</ul><br/>
				<span class="dropdown">

					<a href="#" class="add-screen"><i class="fa fa-plus" aria-hidden="true"></i> ADD ANOTHER SCREEN</a>
					<!-- <ul class="xl-left screen-adder">
						<?php
						foreach ($User->getScreenData() as $screen_cat) {
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
					</ul> -->

					<?php
						$blockPhase = ['phase_ID' => ""];
						require view('modules/add-screen');
					?>

				</span><br/><br/>



				<a href="#" class="option-toggler more-options"><i class="fa fa-sliders-h"></i> More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>
				<a href="#" class="option-toggler less-options"><i class="fa fa-sliders-h"></i> Less Options <i class="fa fa-caret-up" aria-hidden="true"></i><hr/></a>


				<!-- More Options -->
				<div class="more-options-wrapper">



					<h3>Page Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this page like Home, About, ... (Autogenerated from the URL by default)" aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." tabindex="2"/>


					<h3><i class="fa fa-share-alt"></i> Page Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access only this page." aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<picture data-tooltip="Owner: <?=getUserInfo()['fullName']?>" class="profile-picture" <?=getUserInfo()['printPicture']?>>
							<span><?=getUserInfo()['nameAbbr']?></span>
						</picture>

						<ul class="shares page user">

						</ul>


						<!-- Add New -->
						<a href="#" class="new-member" data-tooltip="Add New Page Member"><i class="fa fa-plus"></i></a>

						<input class="share-email" type="email" data-type="page" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


						<ul class="shares page email">

						</ul>


					</div><br/><br/>



					<div class="project-info">


						<h4 class="section-title xl-left">Project Info</h4>

						<h3>Project Name <i class="fa fa-question-circle tooltip" data-tooltip="The name that describes this project. (Autogenerated from the URL by default)" aria-hidden="true"></i></h3>
						<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..." tabindex="3" />


						<h3><i class="fa fa-share-alt"></i> Project Members <i class="fa fa-question-circle tooltip" data-tooltip="Users who can access this project with all the pages in it." aria-hidden="true"></i></h3>
						<div class="people">


							<!-- Owner -->
							<picture data-tooltip="Owner: <?=getUserInfo()['fullName']?>" class="profile-picture" <?=getUserInfo()['printPicture']?>>
								<span><?=getUserInfo()['nameAbbr']?></span>
							</picture>

							<ul class="shares project user">

							</ul>


							<!-- Add New -->
							<a href="#" class="new-member" data-tooltip="Add New Project Member"><i class="fa fa-plus"></i></a>
							<input class="share-email" type="email" name="project-share-email" data-type="project" placeholder='Type an e-mail address and hit "Enter"...' style="display: none; max-width: 75%;"/>


							<ul class="shares project email">

							</ul>


						</div><br/>

					</div>


				</div>


				<!-- Actions -->
				<div class="wrap xl-2 xl-center xl-flexbox">
					<div class="col">

						<button class="dark small submitter">Add</button>

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

							<a href="#"><span class="new-access-type">THIS <span class="data-type"></span></span> <i class="fa fa-caret-down"></i></a>
							<ul class="selectable no-delay new-access-type-selector">
								<li class="selected"><a href="#" data-type="page">THIS PAGE</a></li>
								<li><a href="#" data-type="project">WHOLE PROJECT</a></li>
							</ul>

						</span>

					</div>
					<div class="col">

						<h4 class="xl-center" style="margin-bottom: 10px;">Add New User</h4>
						<input id="share-email" class="share-email" type="email" data-add-type="" placeholder='Type an e-mail address and hit "Enter"...' autofocus />

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





<div id="video" class="popup-window xl-center xl-5-12">
	<a href="#" class="cancel-button" style="position: absolute; right: 20px; top: 20px;"><i class="fa fa-times"></i></a>

	<h2>Quick Start</h2><br>


	<iframe width="560" height="315" data-src="https://www.youtube.com/embed/a3ICNMQW7Ok" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="max-width: 100%;"></iframe>


</div>





<div id="upgrade" class="popup-window xl-11-12" data-current-plan="<?=getUserInfo()['userLevelName']?>">
	<a href="#" class="cancel-button" style="position: absolute; right: 20px; top: 20px;"><i class="fa fa-times"></i></a>

	<h2 class="xl-center">Choose Your Plan</h2>

	<?php require view('modules/pricing-table'); ?>


</div>





<div id="limit-warning" class="popup-window xl-5-12" data-current-plan="<?=getUserInfo()['userLevelName']?>" data-current-pin-mode="" data-allowed-live-pin="<?=$pinsLeft?>" data-allowed-comment-pin="<?=$commentPinsLeft?>" data-allowed-phase="<?=$phasesLeft?>">
	<a href="#" class="cancel-button" style="position: absolute; right: 20px; top: 20px;"><i class="fa fa-times"></i></a>

	<div class="xl-center">
		<p class="limit-text">
			<b>You have reached your live pin limit.</b> <br> 
			To be able to continue changing content of the page, please upgrade your account.
		</p>
	
		<div class="wrap xl-2 xl-gutter-16 xl-center">
			<div class="col">

				<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="upgrade-button" style="background-color: green;"><i class="fa fa-angle-double-up"></i> UPGRADE NOW</a>

			</div>
			<div class="col recommendation recommend-live-mode">

				<a href="#" class="upgrade-button invert" data-switch-pin-type="live" data-switch-pin-private="0"><i class="fa fa-dot-circle"></i> Continue with Live Mode</a>

			</div>
			<div class="col recommendation recommend-comment-mode">

				<a href="#" class="upgrade-button invert" data-switch-pin-type="comment" data-switch-pin-private="0"><i class="fa fa-comment"></i> Continue with Comment Mode</a>

			</div>
			<div class="col recommendation recommend-browse-mode">

				<a href="#" class="upgrade-button invert" data-switch-pin-type="browse" data-switch-pin-private="0"><i class="fa fa-mouse-pointer"></i> Continue with Browse Mode</a>

			</div>
		</div>
	</div>


</div>