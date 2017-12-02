<?php

	/* THE REGEX: /<body>|(?!^)\G(.*?)(?<tag><\w[^<]*?>)(?=.*<\/body>)/sig */

	$page_ID = 15;
	$version_number = 1;
	$page_file = Page::ID($page_ID, $version_number)->pageFile;

	$page_HTML = file_get_contents($page_file);

?>

<textarea>

	<?=$page_HTML?>

</textarea>