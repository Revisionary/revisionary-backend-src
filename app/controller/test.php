<?php

	/* THE REGEX: '/<body[^<]*?>|(?!^)\G(.*?)(?<tag><\w[^<]*?>)(?=.*<\/body>)/si' */

	$page_ID = 15;
	$version_number = 1;
	$page_file = Page::ID($page_ID, $version_number)->pageFile;

	$page_HTML = file_get_contents($page_file);


	$countElement = 0;
	$page_HTML = preg_replace_callback(
        '/<body[^<]*?>|(?!^)\G(.*?)(?<tag><\w[^<]*?>)(?=.*<\/body>)/si',
        function ($urls) {
	        global $countElement;
	        $html_element = isset($urls['tag']) ? $urls['tag'] : $urls[0];

	        if ( !isset($urls['tag']) ) return $html_element; // If other tag has been cought, no change
	        $countElement++;



			if ( substr($html_element, -2 ) == '/>' ) { // If ends with '/>'...

				$html_element = str_replace('/>', ' data-revisionary-index="'.$countElement.'" />', $html_element);

			} elseif ( substr($html_element, -1 ) == '>' ) { // If ends with only '>'...

				$html_element = str_replace('>', ' data-revisionary-index="'.$countElement.'">', $html_element);

			}


	        echo htmlspecialchars($html_element)." <br>";

	        return $html_element;
        },
        $page_HTML
	);



?>
COUNT: <?=$countElement?>
<textarea>

	<?=$page_HTML?>

</textarea>