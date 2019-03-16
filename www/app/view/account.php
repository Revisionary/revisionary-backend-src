<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="alerts">

		<?php if ( isset($_GET['noaccess']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('noaccess');</script>
			Looks like you don't have access to this project.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalidurl']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalidurl');</script>
			The URL you entered is invalid.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['addprojecterror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('addprojecterror');</script>
			Your project couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['addpageerror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('addpageerror');</script>
			Your page couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['adddeviceerror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('adddeviceerror');</script>
			Your device couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalid']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalid');</script>
			We couldn't find the project you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['projectdoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('projectdoesntexist');</script>
			You don't have access to this project.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invaliddevice']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invaliddevice');</script>
			We couldn't find the device you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['devicedoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('devicedoesntexist');</script>
			You don't have access to this device.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['pagedoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('pagedoesntexist');</script>
			You don't have access to this page.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalidpage']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalidpage');</script>
			We couldn't find the page you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>





		<?php } elseif ( isset($_GET['status']) && $_GET['status'] == "successful" ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('status');</script>
			New category successfully added.

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



				</div>

				<div class="col xl-6-12 xl-center title">

					<h1>My Account</h1><br/><br/><br/>

				</div>

				<div class="col xl-3-12 xl-right" data-tooltip="In development...">

					<b>Usage:</b> 8 MB of 25 MB (Basic Account)

				</div>
			</div>


			<!-- Filter Bar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

					<a href="<?=site_url('projects')?>" class="invert-hover" style="letter-spacing: 2.5px;"><i class="fa fa-arrow-left"></i> PROJECTS</a>

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

							<picture class="profile-picture larger avatar-changer" <?=$userInfo['printPicture']?>>
								<span <?=$userInfo['userPic'] != "" ? "class='has-pic'" : ""?>><?=$userInfo['nameAbbr']?></span>
								<input type="file" name="image" id="filePhoto" data-max-size="3145728">
							</picture>

							<br/>
							<br/>

							<div class="level-wrapper" data-tooltip="Current Plan allows maximum X MB of files (from Y Projects, Z Pages, T Live Pins)">
								<div class="user-level tooltip">
									<?=$userInfo['userLevelName']?>
								</div>
								<a href="<?=site_url('upgrade')?>">Upgrade to PRO</a>
							</div>

						</div>
						<div class="col" data-tooltip="In development...">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Name</span>
								<span class="col"><input class="full" type="text" value="<?=$userInfo['fullName']?>" placeholder="Your full name"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Email</span>
								<span class="col"><input class="full" type="email" value="<?=$userInfo['email']?>" placeholder="Your email"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Job Title</span>
								<span class="col"><input class="full" type="text" value="" placeholder="Your job title"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Department</span>
								<span class="col"><input class="full" type="text" value="" placeholder="Your department"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Company</span>
								<span class="col"><input class="full" type="text" value="" placeholder="Your company name"/></span>
							</label><br/>


							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" value="Update"/></span>
							</div><br/>


						</div>
					</div>


				<?php } ?>



				</div>
			</div>




		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>