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

					<a class="selected" href="#">Profile</a>
					<a class="" href="#">Password</a>
					<a class="" href="#">Email</a>
					<a class="" href="#">Billing</a>

				</div>

				<div class="col xl-3-12 xl-right inline-guys">


				</div>
			</div>


			<!-- Blocks -->




		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>