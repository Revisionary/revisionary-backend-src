<!DOCTYPE html>
<html class="no-js">
	<head>

		<script>
			document.getElementsByTagName("HTML")[0].classList.remove("no-js");
		</script>

		<meta charset="utf-8">
		<title><?=$page_title?></title>

		<link rel="stylesheet" href="<?=asset_url_nocache('fonts/font-awesome/css/all.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url_nocache('fonts/font-awesome/css/v4-shims.css')?>" media="screen">
		<link rel="stylesheet" href="<?=asset_url_nocache('styles/style.css')?>" media="screen">

		<?php

			// Generate new nonce for JS
			$_SESSION["js_nonce"] = uniqid(mt_rand(), true);


			// Generate new nonce for adding projects or pages
			$_SESSION["add_new_nonce"] = uniqid(mt_rand(), true);



			// Additional CSS Files
			if (isset($additionalCSS) && is_array($additionalCSS) ) {

				foreach ($additionalCSS as $file) {
					echo '<link rel="stylesheet" href="'.asset_url_nocache('styles/'.$file).'" media="screen">';
				}

			}
		?>

		<script src="<?=asset_url('scripts/vendor/jquery-3.4.1.min.js')?>"></script>
		<script>
			var ajax_url = '<?=site_url('ajax')?>';
			var nonce = '<?=$_SESSION["js_nonce"]?>';
			var loggedIn = <?=userloggedIn() ? "true" : "false"?>;
			<?=isset($dataType) ? "var dataType = '".$dataType."';" : ""?>
			<?=isset($dataType) && $dataType == "page" && isset($project_ID) ? "var project_ID = '".$project_ID."';" : ""?>
		</script>

		<?php

			// Additional JS Files
			if (isset($additionalHeadJS) && is_array($additionalHeadJS) ) {

				foreach ($additionalHeadJS as $file) {
					echo '<script src="'.asset_url_nocache('scripts/'.$file).'"></script>';
				}

			}
		?>

		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="32x32">
		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="192x192">
		<link rel="apple-touch-icon-precomposed" href="<?=asset_url('images/revisionary-icon.png')?>">
		<meta name="msapplication-TileImage" content="<?=asset_url('images/revisionary-icon.png')?>">

	</head>
	<body class="<?=isset($_url[0]) ? $_url[0] : ""?>">
