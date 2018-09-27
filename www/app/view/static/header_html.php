<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<title><?=$page_title?></title>

		<link rel="stylesheet" href="<?=asset_url('fonts/font-awesome/css/font-awesome.css').'?v='.filemtime(dir.'fonts/font-awesome/css/font-awesome.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url('styles/style.css').'?v='.filemtime(dir.'/assets/styles/style.css')?>" media="screen">

		<?php

			// Additional CSS Files
			if (isset($additionalCSS) && is_array($additionalCSS) ) {

				foreach ($additionalCSS as $file) {
					$filePath = 'styles/'.$file;

					echo '<link rel="stylesheet" href="'.asset_url($filePath).'?v='.filemtime(dir."/assets/".$filePath).'" media="screen">';
				}

			}
		?>

		<script src="<?=asset_url('scripts/vendor/jquery-3.2.1.min.js')?>"></script>

		<?php

			// Additional JS Files
			if (isset($additionalHeadJS) && is_array($additionalHeadJS) ) {

				foreach ($additionalHeadJS as $file) {
					$filePath = 'scripts/'.$file;

					echo '<script src="'.asset_url($filePath).'?v='.filemtime(dir."/assets/".$filePath).'"></script>';
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
			<?=isset($dataType) ? "var dataType = '".$dataType."';" : ""?>
		</script>

		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="32x32">
		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="192x192">
		<link rel="apple-touch-icon-precomposed" href="<?=asset_url('images/revisionary-icon.png')?>">
		<meta name="msapplication-TileImage" content="<?=asset_url('images/revisionary-icon.png')?>">

	</head>
	<body>
