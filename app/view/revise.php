<?php require 'static/header_html.php' ?>

<div id="loading-overlay">
	<span><div class="gps_ring"></div> LOADING...</span>
</div>

<div id="page" class="site">
	<main>

		<div class="iframe-container">

<!-- 			<iframe src="http://localhost/wordpress" width="100%" height="100%" scrolling="auto"></iframe> -->
			<iframe src="https://www.twelve12.com" width="100%" height="100%" scrolling="auto"></iframe>

		</div>


		<div id="revise-sections">
			<div class="top-left pins">

				<div class="tab wrap open">
					<div class="col xl-1-1">
						PINS CONTENT<br/><br/><br/>
					</div>
					<div class="opener">
						<a href="#">PINS</a>
					</div>
				</div>

			</div>


			<div class="top-right share xl-right">

				<div class="tab wrap open">
					<div class="col xl-1-1">
						SHARE CONTENT<br/><br/><br/>
					</div>
					<div class="opener">
						<a href="#">SHARE</a>
					</div>
				</div>

			</div>


			<div class="bottom-left info">

				<div class="tab wrap open">
					<div class="col xl-8-12 xl-left">
						<div class="breadcrumbs">
							<a href="<?=site_url('pages/twelve12')?>" class="projects bullet bullet-white">Twelve12</a> <sep>></sep>
							<a href="<?=site_url('pages/twelve12/#main-pages')?>" class="pages bullet bullet-white">Main Pages</a> <sep>></sep>
							<a href="<?=site_url('revise/23423142')?>" class="sections bullet bullet-white">Home</a>
						</div>
						<div class="date created">Date Created: <span>1 Jul 2016 6:32 PM</span></div>
						<div class="date updated">Last Updated: <span>2 Jul 2016 1:59 PM</span></div>
					</div>
					<div class="col xl-4-12 xl-center">
						<div class="device-selector">
							<span class="bullet bullet-white">Device</span><br/>
							<i class="fa fa-laptop" aria-hidden="true"></i><br/>
						</div>
						<a href="#" class="version-selector">v0.1</a>
					</div>
					<div class="opener">
						<a href="#">INFO</a>
					</div>
				</div>


			</div>


			<div class="bottom-right help xl-right">

				<div class="tab wrap open">
					<div class="col xl-1-1">
						HELP CONTENT<br/><br/><br/>
					</div>
					<div class="opener">
						<a href="#">HELP</a>
					</div>
				</div>

			</div>
		</div>


	</main> <!-- main -->
</div> <!-- #page.site -->


<script>

</script>


<?php require 'static/footer_html.php' ?>