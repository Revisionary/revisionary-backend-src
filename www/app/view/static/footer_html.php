		<?php

			// Additional JS Files
			if (isset($additionalBodyJS) && is_array($additionalBodyJS) ) {

				foreach ($additionalBodyJS as $file) {
					$filePath = 'scripts/'.$file;

					echo '<script src="'.asset_url($filePath).'?v='.filemtime(dir."/assets/".$filePath).'"></script>';
				}

			}
		?>

	</body>
</html>