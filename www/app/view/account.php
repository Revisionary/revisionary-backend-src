<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="alerts">

		<?php if ( isset($_GET['error']) && $_GET['error'] == "name" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Please enter your full name.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "unknown" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Sorry, your information couldn't be updated.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "invalidname" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Only letters and white space allowed on your name.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "password" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Your current password is not correct.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "passconfirm" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Password confirmation is not same with the new password you entered.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "fieldsempty" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Please don't leave fields empty.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>





		<?php } elseif ( isset($_GET['successful']) ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('successful');</script>
			Your information has been updated.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['password-changed']) ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('password-changed');</script>
			Your password successfully changed.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } ?>


	</div>

	<div id="content" class="wrap xl-1 container">
		<div class="col">


			<!-- Title -->
			<div class="wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

					<a href="<?=site_url('projects')?>" class="invert-hover" style="letter-spacing: 2.5px;"><i class="fa fa-arrow-left"></i> PROJECTS</a>

				</div>

				<div class="col xl-6-12 xl-center title">

					<h1>My Account</h1><br/><br/><br/>

				</div>

				<div class="col xl-3-12 xl-right" data-tooltip="In development...">

					<b>Usage:</b> 8 MB of 25 MB (<?=$userInfo['userLevelName']?> Account)

				</div>
			</div>


			<!-- Filter Bar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<a class="<?=$subpage == "profile" || !$subpage ? "selected" : ""?>" href="<?=site_url("account")?>">Profile</a>
					<a class="<?=$subpage == "password" ? "selected" : ""?>" href="<?=site_url("account/password")?>" data-tooltip="In development...">Password</a>
					<a class="<?=$subpage == "email" ? "selected" : ""?>" href="<?=site_url("account/email")?>" data-tooltip="In development...">Email</a>
					<a class="<?=$subpage == "billing" ? "selected" : ""?>" href="<?=site_url("account/billing")?>" data-tooltip="In development...">Billing</a>

				</div>

				<div class="col xl-3-12 xl-right inline-guys">


				</div>
			</div>


			<!-- Blocks -->
			<div class="wrap xl-center">
				<div class="col xl-6-12">


				<?php if ($subpage == "profile" || !$subpage) { ?>


					<div class="wrap xl-table xl-gutter-24">
						<div class="col xl-3-12 xl-center">

							<form id="avatar-form" action="" method="POST" enctype="multipart/form-data">
								<picture class="profile-picture larger avatar-changer" <?=$userInfo['printPicture']?> data-type="user" data-id="<?=$user_ID?>">
									<span><?=$userInfo['nameAbbr']?></span>
									<input type="file" name="image" class="avatar-upload" id="filePhoto" data-max-size="3145728">
								</picture>
							</form>

							<br/>

							<div class="level-wrapper" data-tooltip="In Development... Current Plan allows maximum X MB of files (from Y Projects, Z Pages, T Live Pins)">
								<div class="user-level tooltip">
									<?=$userInfo['userLevelName']?>
								</div>
								<a href="<?=site_url('upgrade')?>">Upgrade to PRO</a>
							</div>

						</div>
						<form method="post" action="" class="col">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Name</span>
								<span class="col"><input class="full" type="text" name="user_full_name" value="<?=$userInfo['fullName']?>" placeholder="Your full name"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Job Title</span>
								<span class="col"><input class="full" type="text" name="user_job_title" value="<?=$userInfoDB['user_job_title']?>" placeholder="Your job title"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Department</span>
								<span class="col"><input class="full" type="text" name="user_department" value="<?=$userInfoDB['user_department']?>" placeholder="Your department"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Company</span>
								<span class="col"><input class="full" type="text" name="user_company" value="<?=$userInfoDB['user_company']?>" placeholder="Your company name"/></span>
							</label><br/>


							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" name="update-submit" value="Update"/></span>
							</div><br/>


						</form>
					</div>

				<?php } elseif ($subpage == "password") { ?>


					<div class="wrap xl-table xl-gutter-24">
						<div class="col xl-3-12 xl-center">

						</div>
						<form method="post" action="" class="col">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Current Password</span>
								<span class="col"><input class="full" type="password" name="current_password" placeholder="Enter your current password"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">New Password</span>
								<span class="col"><input class="full" type="password" name="new_password" placeholder="Enter your new password"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">New Password Confirmation</span>
								<span class="col"><input class="full" type="password" name="new_password_confirmation" placeholder="Retype your new password"/></span>
							</label><br/>



							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" name="password-submit" value="Update"/></span>
							</div><br/>


						</form>
					</div>


				<?php } ?>



				</div>
			</div>




		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>