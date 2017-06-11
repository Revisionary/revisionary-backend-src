<?php
use PHPHtmlParser\Dom;


class Internalize {


	// The page ID
	public $pageId;

	// The remote URL
	public $remoteUrl;

	// Log Dir
	public $logDir;

	// Log File
	public $logFile;


	// CSS files to download
	public $cssToDownload = array();

	// Fonts to download
	public $fontsToDownload = array();

	// TEMP - Delete the cache folder
	public $deleteCache = false;

	// Debug
	public $debug = false;




	// SETTERS:
	public function __construct($pageId) {

		// Set the page ID
		$this->pageId = $pageId;

		// Set the remote URL
		$this->remoteUrl = Page::ID($this->pageId)->remoteUrl;
		if (isset($_GET['new_url']) && $_GET['new_url'] != "" )
			$this->remoteUrl = $_GET['new_url'];

		// Set the log file
        $this->logDir = Page::ID($this->pageId)->pageDir."logs";

		// Set the log file
        $this->logFile = $this->logDir."process.log";

    }



	// TEMP - DELETE THE CACHED VERSION !!!
	public function deleteDirectory($dir) {
	    if (!file_exists($dir)) {
	        return true;
	    }

	    if (!is_dir($dir)) {
	        return unlink($dir);
	    }

	    foreach (scandir($dir) as $item) {
	        if ($item == '.' || $item == '..') {
	            continue;
	        }

	        if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
	            return false;
	        }

	    }

	    return rmdir($dir);
	}



	// JOBS:

	// Pull the remote HTML
	public function saveRemoteHTML() {


		// Create the log folder if not exists
		if ( !file_exists(Page::ID($this->pageId)->logDir) )
			mkdir(Page::ID($this->pageId)->logDir, 0755, true);
		@chmod(Page::ID($this->pageId)->logDir, 0755);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_html.log", "[".date("Y-m-d h:i:sa")."] - Started \r\n", FILE_APPEND);


		// Do nothing if already saved
		if ( file_exists( Page::ID($this->pageId)->pageTempFile ) ) return false;


		// For the SSL Problem
		$ContextOptions = array(
		    "ssl" => array(
		        "verify_peer" => false,
		        "verify_peer_name" => false,
		    ),
	        "http" => array (
	            "follow_location" => true // follow redirects
	        )
		);

		// Get the HTML
		$content = @file_get_contents($this->remoteUrl, FILE_TEXT, stream_context_create($ContextOptions));
		$html = $content;


		// Extract the encode
		$charset = "";
		$headers = @get_headers($this->remoteUrl, 1);
		$content_type = $headers['Content-Type'];
		if ( is_array($content_type) )
			$content_type = end($content_type);
		$parsed_content_type = explode(';', $content_type);
		if (count($parsed_content_type) > 1)
			$charset = strtoupper(substr(array_values(array_filter($parsed_content_type, function ($v) {
				return substr($v, 0, 9) === ' charset=';
			}))[0], 9));

		// Log the headers
		file_put_contents($this->logDir."/charset.log", print_r($headers, true), FILE_APPEND);


		// Correct the charset
		if ($charset != "" )
			$html = mb_convert_encoding($content, "UTF-8", $charset);



		// SAVING:

		// Create the folder if not exists
		if ( !file_exists(Page::ID($this->pageId)->pageDir) )
			mkdir(Page::ID($this->pageId)->pageDir, 0755, true);
		@chmod(Page::ID($this->pageId)->pageDir, 0755);


		// Save the file if not exists
		if ( !file_exists( Page::ID($this->pageId)->pageTempFile ) )
			$saved = file_put_contents( Page::ID($this->pageId)->pageTempFile, $html, FILE_TEXT);


		// LOG:
		if ($saved) file_put_contents( Page::ID($this->pageId)->logFile," - HTML DOWNLOADED:".$this->pageId.": '".$this->remoteUrl."' \r\n", FILE_APPEND);
		else file_put_contents( Page::ID($this->pageId)->logFile," - HTML <b>NOT</b> DOWNLOADED: '".$this->remoteUrl."' \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_html.log", "[".date("Y-m-d h:i:sa")."] - Finished \r\n", FILE_APPEND);
		rename(Page::ID($this->pageId)->logDir."_html.log", Page::ID($this->pageId)->logDir."html.log");


		$_SESSION['process'] = "Class.Internalize.php - HTML Downloaded";


		// Return the HTML if successful
		if ($saved)
			return $html;
		return false;


		// NEXT: Filter the HTML to correct URLs

	}


	// Filter the HTML to correct URLs
	public function filterAndUpdateHTML($html) {

		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_filter.log", "[".date("Y-m-d h:i:sa")."] - Started \r\n", FILE_APPEND);


		// Add Necessary Spaces - done for a bug - Don't use for now
		function placeNeccessarySpaces($contents){
			$quotes = 0; $flag = false;
			$newContents = '';
			for($i = 0; $i < strlen($contents); $i++){
			    $newContents .= $contents[$i];
			    if(is_array($contents) && $contents[$i] == '"') $quotes++;
			    if($quotes%2 == 0){
			        if(is_array($contents) && $contents[$i+1] !== ' ' && $flag == true) {
			            $newContents .= ' ';
			            $flag = false;
			        }
			    }
			    else $flag = true;
			}
			return $newContents;
		}
		//$html = placeNeccessarySpaces($html);


		// INCLUDE THE BASE
		$html = preg_replace_callback(
	        '/(?<tag><head+[^<]*?>)/i',
	        function ($urls) {

		        return $urls['tag']."<base href='".$this->remoteUrl."'>";

	        },
	        $html
	    );


		// CONVERT ALL HREF, SRC ATTRIBUTES TO ABSOLUTE  !!! - Correct with existing revisionary page urls ??? (target="_parent")
		$html = preg_replace_callback(
	        '/<(?<tagname>link|a|script|img)\s+[^<]*?(?:href|src)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) {

		        $the_url = isset($urls['value2']) ? $urls['value2'] : $urls['value'];

		        $new_url = url_to_absolute($this->remoteUrl, $the_url);

		        if (parseUrl($the_url)['host'] != "" )
		        	$new_url = url_to_absolute(parseUrl($the_url)['full_host'], $the_url);


	            return str_replace(
	            	$the_url,
	            	$new_url,
	            	$urls[0]
	            );
	        },
	        $html
	    );


	    // INTERNALIZE CSS FILES - COUNT THE LOOP FOR PROGRESS BAR !!!
		$count_css = 0;
		$html = preg_replace_callback(
	        '/<(?<tagname>link)\s+[^<]*?(?:href)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) use(&$count_css) {

		        $the_url = isset($urls['value2']) ? $urls['value2'] : $urls['value'];



				// If file is from the remote url
		        if ( parseUrl($the_url)['domain'] == parseUrl($this->remoteUrl)['domain']

		        && ( strpos($urls[0], 'rel="stylesheet"') !== false || strpos($urls[0], "rel='stylesheet'") !== false || strpos($urls[0], "rel=stylesheet") !== false )

		        ) {

			        $count_css++;
		        	$css_file_name = $count_css.".css";

					// Add the file to download list
			        $this->cssToDownload["css/".$css_file_name] = $the_url;

			        if ($this->debug) echo "internalize files: ".$the_url." - File Name: ".$css_file_name."<br>";


			        // Change the URL
					return str_replace(
		            	$the_url,
		            	Page::ID($this->pageId)->pageUri."css/".$css_file_name,
		            	$urls[0]
		            );

				}

				return $urls[0];


	        },
	        $html
	    );


		// CONVERT ALL SRCSET ATTRIBUTES TO ABSOLUTE
		$html = preg_replace_callback(
	        '/<(?:img)\s+[^<]*?(?:srcset)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) {

		        $the_url = isset($urls['value2']) ? $urls['value2'] : $urls['value'];

				$attr = explode(',', $the_url);

			    $new_srcset = "";

				foreach ( $attr as $src ) {

					$url_exp = array_filter(explode(' ', trim($src)));
					$url = $url_exp[0];
					$size = $url_exp[1];

					$new_srcset .= url_to_absolute($this->remoteUrl, $url)." ".$size.(end($attr) != $src ? ", " : "");

				}


	            return str_replace(
	            	$the_url,
	            	$new_srcset,
	            	$urls[0]
	            );
	        },
	        $html
	    );


	    // IN PAGE STYLES
		$html = preg_replace_callback(
	        '/(?<tag><style+[^<]*?>)(?<content>[^<>]++)<\/style>/i',
	        function ($urls) {

		        return $urls['tag'].$this->filter_css($urls['content'])."</style>";

	        },
	        $html
	    );


	    // INLINE STYLES
		$html = preg_replace_callback(
	        '/<(?:[a-z0-9]*)\s+[^<]*?(?:style)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) {

		        $the_url = isset($urls['value2']) ? $urls['value2'] : $urls['value'];


	            return str_replace(
	            	$the_url,
	            	$this->filter_css($the_url),
	            	$urls[0]
	            );
	        },
	        $html
	    );




		// SAVING:

		// Save the file if not exists
		if ( file_exists( Page::ID($this->pageId)->pageTempFile ) )
			$updated = file_put_contents( Page::ID($this->pageId)->pageTempFile, $html, FILE_TEXT);


		// LOG:
		if ($updated) file_put_contents( Page::ID($this->pageId)->logFile," - HTML FILTRED \r\n", FILE_APPEND);
		else file_put_contents( Page::ID($this->pageId)->logFile," - HTML <b>NOT</b> FILTRED \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_filter.log", "[".date("Y-m-d h:i:sa")."] - Finished \r\n", FILE_APPEND);
		rename(Page::ID($this->pageId)->logDir."_filter.log", Page::ID($this->pageId)->logDir."filter.log");


		// Return the HTML if successful
		if ($updated)
			return $html;
		return false;


		// NEXT: Detect the files that needs to be internalized

	}


	// Download the CSS Files
	public function downloadCssFiles() {


		// INIT LOG:
		file_put_contents( Page::ID($this->pageId)->logFile," - CSS DOWNLOAD STARTED {TOTAL:".count($this->cssToDownload)."} \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_css.log", "[".date("Y-m-d h:i:sa")."] - Started {TOTAL:".count($this->cssToDownload)."} \r\n", FILE_APPEND);


		// Download them
		$css_downloaded_has_error = false;
		foreach ($this->cssToDownload as $fileName => $url) {
			$css_downloaded = $this->download_remote_file($url, $fileName, "css");


			// LOG:
			if ($css_downloaded) file_put_contents( Page::ID($this->pageId)->logFile," -- CSS DOWNLOADED: '".$url."' -> '".$fileName."' \r\n", FILE_APPEND);
			else file_put_contents( Page::ID($this->pageId)->logFile," -- CSS <b>NOT</b> DOWNLOADED: '".$url."' -> '".$fileName."' \r\n", FILE_APPEND);


			// Specific Log
			if ($css_downloaded)
				file_put_contents( Page::ID($this->pageId)->logDir."_css.log", "[".date("Y-m-d h:i:sa")."] - Downloaded: '".$url."' -> '".$fileName."' \r\n", FILE_APPEND);


			if (!$css_downloaded) $css_downloaded_has_error = true;

		}


		// FINISH LOG:
		if (!$css_downloaded_has_error) file_put_contents( Page::ID($this->pageId)->logFile," - CSS DOWNLOAD FINISHED \r\n", FILE_APPEND);
		else file_put_contents( Page::ID($this->pageId)->logFile," - CSS DOWNLOAD FINISHED <b>WITH ERRORS</b> \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_css.log", "[".date("Y-m-d h:i:sa")."] - Finished \r\n", FILE_APPEND);
		rename(Page::ID($this->pageId)->logDir."_css.log", Page::ID($this->pageId)->logDir."css.log");


		// Return true if no error
		return !$css_downloaded_has_error;

	}


	// Download the font Files
	public function downloadFontFiles() {


		// INIT LOG:
		file_put_contents( Page::ID($this->pageId)->logFile," - FONT DOWNLOAD STARTED {TOTAL:".count($this->fontsToDownload)."} \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_font.log", "[".date("Y-m-d h:i:sa")."] - Started {TOTAL:".count($this->fontsToDownload)."} \r\n", FILE_APPEND);


		$font_downloaded_has_error = false;
		foreach ($this->fontsToDownload as $fileName => $url) {
			$font_downloaded = $this->download_remote_file($url, $fileName, "fonts");

			// LOG:
			if ($font_downloaded) file_put_contents( Page::ID($this->pageId)->logFile," -- FONT DOWNLOADED: '".$url."' -> '".$fileName."' \r\n", FILE_APPEND);
			else file_put_contents( Page::ID($this->pageId)->logFile," -- FONT <b>NOT</b> DOWNLOADED: '".$url."' -> '".$fileName."' \r\n", FILE_APPEND);


			// Specific Log
			if ($font_downloaded)
				file_put_contents( Page::ID($this->pageId)->logDir."_font.log", "[".date("Y-m-d h:i:sa")."] - Downloaded: '".$url."' \r\n", FILE_APPEND);


			if (!$font_downloaded) $font_downloaded_has_error = true;

		}


		// FINISH LOG:
		// LOG:
		if (!$font_downloaded_has_error) file_put_contents( Page::ID($this->pageId)->logFile," - FONT DOWNLOAD FINISHED \r\n", FILE_APPEND);
		else file_put_contents( Page::ID($this->pageId)->logFile," - FONT DOWNLOAD FINISHED <b>WITH ERRORS</b> \r\n", FILE_APPEND);


		// Specific Log
		file_put_contents( Page::ID($this->pageId)->logDir."_font.log", "[".date("Y-m-d h:i:sa")."] - Finished \r\n", FILE_APPEND);
		rename(Page::ID($this->pageId)->logDir."_font.log", Page::ID($this->pageId)->logDir."font.log");


		// Return true if no error
		return !$font_downloaded_has_error;

	}





	// DOWNLOAD FILES
	function download_remote_file($url, $fileName, $folderName = "other") {
		$fileContent = "";


		// Check the url
		if ( get_http_response_code($url) == "200" )
	    	$fileContent .= @file_get_contents($url, FILE_BINARY);


		if ( $folderName == "css" )
			$fileContent = $this->filter_css($fileContent, $url);

		if ($this->debug) echo "download_remote_file: ".$url."<br>";


		// SAVING:

		// Create the folder if not exists
		if ( !file_exists(Page::ID($this->pageId)->pageDir."$folderName/") )
			mkdir(Page::ID($this->pageId)->pageDir."$folderName/", 0755, true);
		@chmod(Page::ID($this->pageId)->pageDir."$folderName/", 0755);

		// Save the file if not exists
		$downloaded = false;
		if ( !file_exists( Page::ID($this->pageId)->pageDir.$fileName ) )
			$downloaded = file_put_contents( Page::ID($this->pageId)->pageDir.$fileName, $fileContent, FILE_BINARY);


		// Return true if successful
		return $downloaded;

	}



	// FILTERS:

	// FILTER CSS
	function filter_css($css, $url = "") {

		if (empty($url))
			$url = $this->remoteUrl;

		// Internalize Fonts
		$css = $this->detectFonts($css, $url);

		if ($this->debug) echo "filter_css: ".$url."<br>";


		// All url()s
		$css = preg_replace_callback(
	        '%url\s*\(\s*[\\\'"]?(?!(((?:https?:)?\/\/)|(?:data:?:)))([^\\\'")]+)[\\\'"]?\s*\)%',
	        function ($css_urls) use($url) {

	            return "url('".url_to_absolute($url, $css_urls[3])."')";
	        },
	        $css
	    );


		return $css;

	}


	// FILTER JS ??
	function filter_js() {

	}


	// DETECT FONTS
	function detectFonts($css, $url = "") {

	$pattern = <<<'LOD'
~
(?(DEFINE)
    (?<quoted_content>
        (["']) (?>[^"'\\]++ | \\{2} | \\. | (?!\g{-1})["'] )*+ \g{-1}
    )
    (?<comment> /\* .*? \*/ )
    (?<url_skip> (?: https?: | data: ) [^"'\s)}]*+ )
    (?<other_content>
        (?> [^u}/"']++ | \g<quoted_content> | \g<comment>
          | \Bu | u(?!rl\s*+\() | /(?!\*)
          | \g<url_start> \g<url_skip> ["']?+
        )++
    )
    (?<anchor> \G(?<!^) ["']?+ | @font-face \s*+ { )
    (?<url_start> url\( \s*+ ["']?+ )
)

\g<comment> (*SKIP)(*FAIL) |

\g<anchor> \g<other_content>?+ \g<url_start> \K [./]*+

( [^"'\s)}]*+ )    # url
~xs
LOD;

		$css = preg_replace_callback(
	        $pattern,
	        function ($urls) use($url) {

		        $font_remote_url = url_to_absolute($url, $urls[0]);

		        $parsed_url = parseUrl($font_remote_url);
		        $font_file_name = basename($parsed_url['path']);
		        $font_file_name_hash = $font_file_name.($parsed_url['hash'] != "" ? "#".$parsed_url['hash'] : "");

				// Add the file to quee
	            $this->fontsToDownload["fonts/".$font_file_name] = $font_remote_url;

	            if ($this->debug) echo "detectFonts: ".$url." - ".$urls[0]."<br>";

				// Change the URL
	            //return site_url("get-font/?font=".$font_file_name_hash);
	            return Page::ID($this->pageId)->pageUri."fonts/".$font_file_name_hash;

	        },
	        $css
	    );


	return $css;

	}

}