<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<title><?=$page_title?></title>

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&amp;subset=latin%2Clatin-ext" type="text/css" media="all">
		<link rel="stylesheet" href="<?=asset_url('styles/normalize.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url('styles/flexiblegs-css.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url('fonts/font-awesome/css/font-awesome.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url('styles/style.css')?>" media="screen">

		<?php

			// Additional CSS Files
			if (isset($additionalCSS) && is_array($additionalCSS) ) {

				foreach ($additionalCSS as $file) {
					echo '<link rel="stylesheet" href="'.asset_url('styles/'.$file).'" media="screen">';
				}

			}
		?>

		<script src="<?=asset_url('scripts/vendor/jquery-3.2.1.min.js')?>"></script>

		<?php

			// Additional JS Files
			if (isset($additionalHeadJS) && is_array($additionalHeadJS) ) {

				foreach ($additionalHeadJS as $file) {
					echo '<script src="'.asset_url('scripts/'.$file).'"></script>';
				}

			}
		?>


		<?php

		// Generate new nonce for JS
		$_SESSION["js_nonce"] = uniqid(mt_rand(), true);

		?>
		<script>
			var ajax_url = '<?=site_url('ajax')?>';
			var nonce = '<?=$_SESSION["js_nonce"]?>';
		</script>

		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="32x32">
		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="192x192">
		<link rel="apple-touch-icon-precomposed" href="<?=asset_url('images/revisionary-icon.png')?>">
		<meta name="msapplication-TileImage" content="<?=asset_url('images/revisionary-icon.png')?>">

	</head>
	<body>
