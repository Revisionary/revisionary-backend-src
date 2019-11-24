<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<h1 class="xl-center"><?= strtoupper($page_title)?></h1>

		<?php require view('modules/pricing-table'); ?>

	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>