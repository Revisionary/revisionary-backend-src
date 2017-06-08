<?php
use PHPHtmlParser\Dom;

class Internalize {


	// The page ID
	public $pageId;

	// The page version
	public $pageVersion;

	// The project ID
	public $projectId;

	// The remote URL
	public $remoteUrl;

	// Current user ID
	public $userId;


	// Page Directory
	public $pageDir;

	// Page Url
	public $pageUri;

	// Page File Name
	public $pageFileName;

	// Page File
	public $pageFile;

	// Page Temporary File
	public $pageTempFile;


	// CSS files to download
	public $cssToDownload = array();

	// Fonts to download
	public $fontsToDownload = array();

	// TEMP - Delete the cache folder
	public $deleteCache = true;

	// Debug
	public $debug = false;




	// ACTIONS:
	public function __construct($pageId) {


		// SETTERS:

		// Set the page ID
        $this->pageId = $pageId;

		// Set the project ID
        $this->projectId = $this->getProjectId($pageId);

        // Set the version number
        $this->pageVersion = $this->getPageVersion($pageId);

		// Set the remote url
        $this->remoteUrl = $this->getRemoteUrl($pageId);

        // Set the user ID
        $this->userId = $this->getCurrentUserId();

        // Set the page cache directory
        $this->pageDir = dir."/assets/cache/sites/".$this->userId."/".$this->projectId."/".$this->pageId."/".$this->pageVersion."/";

        // Set the page cache directory URL
        $this->pageUri = asset_url("cache/sites/".$this->userId."/".$this->projectId."/".$this->pageId."/".$this->pageVersion."/");

        // Set the page cache file name
        $this->pageFileName = "index.html";

        // Set the page cache file
        $this->pageFile = $this->pageDir.$this->pageFileName;

        // Set the page cache file
        $this->pageTempFile = $this->pageDir."_".$this->pageFileName;




		// TEMP - DELETE THE CACHED VERSION
		function deleteDirectory($dir) {
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

		        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
		            return false;
		        }

		    }

		    return rmdir($dir);
		}
		if ($this->deleteCache) deleteDirectory(dir."/assets/cache/sites");





		// INTERNAL REDIRECTIONS:

		// Http to Https Redirection
		if ( substr($this->remoteUrl, 0, 8) == "https://" && !ssl) {

			$appendUrl = "";
			if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
	    		$appendUrl = "?new_url=".urlencode($_GET['new_url']);

			header('Location: '.site_url('revise/'.$this->pageId, true).$appendUrl); // Force HTTPS
			die();
		}

		// Https to Http Redirection
		if ( substr($this->remoteUrl, 0, 7) == "http://" && ssl) {

			$appendUrl = "";
			if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
	    		$appendUrl = "?new_url=".urlencode($_GET['new_url']);

			header('Location: '.site_url('revise/'.$this->pageId, false, true).$appendUrl); // Force HTTP
			die();
		}





		// Get HTTP headers
		$noProblem = false;
		$charset = "";
		$headers = @get_headers($this->remoteUrl, 1);
		$page_response = intval(substr($headers[0], 9, 3));

		// O.K.
		if ( $page_response == 200 ) {


			// Extract the encode
			$parsed_content_type = explode(';', $headers['Content-Type']);
			if (count($parsed_content_type) > 1)
				$charset = strtoupper(substr(array_values(array_filter($parsed_content_type, function ($v) {
					return substr($v, 0, 9) === ' charset=';
				}))[0], 9));


			// Allow doing the jobs!
			$noProblem = true;


		// Redirecting
		} elseif ( $page_response == 301 || $page_response == 302 ) {


			$new_location = $headers['Location'];
			if ( is_array($new_location) ) $new_location = end($new_location);


			// Update the NEW remoteUrl on DB !!!
			// ...


			// Refresh the page for preventing redirects
			header( 'Location: ' . site_url('revise/'.$this->pageId."?new_url=".urlencode($new_location)) );
			die();


		// Other
		} else {

			// Try non-ssl if the url is on SSL?
			if ( substr($this->remoteUrl, 0, 8) == "https://" ) {

				// Update the nonSSL remoteUrl on DB !!!???
				// ...


				// Refresh the page to try non-ssl
				header( 'Location: ' . site_url('revise/'.$this->pageId."?new_url=".urlencode( "http://".substr($this->remoteUrl, 8) )) );
				die();


			// If nothing works
			} else {

				header( 'Location: ' . site_url('projects/?error='.$this->remoteUrl) );
				die();

			}


		}






		// JOBS:

		// 1. Save the remote HTML
		$savedHTML = $this->saveRemoteHTML($charset);

		// 2. Correct the urls and check the files that needs to be saved
		$filtred = false;
		if ($savedHTML) $filtred = $this->filterAndUpdateHTML($savedHTML);

		// Download the CSS files
		$css_downloaded = false;
		if ($filtred) {

			foreach ($this->cssToDownload as $fileName => $url) {
				$css_downloaded = $this->download_remote_file($url, $fileName, "css");
			}

		}

		// Download the fonts
		$font_downloaded = false;
		if ($css_downloaded) {

			foreach ($this->fontsToDownload as $fileName => $url) {
				$font_downloaded = $this->download_remote_file($url, $fileName, "fonts");
			}

		}

		// Download the JS files ?

		// Download the images ?

    }




	// TEMP
    public function serveTheURL($showTheUrl = false) {

	    if ($showTheUrl) return $this->remoteUrl;

	    return $this->pageUri."_".$this->pageFileName;
    }




	// GETTERS:

    // Get the Project ID
    public function getProjectId($pageId) {

	    // GET IT FROM DB...
	    $projectId = "twelve12";

	    return $projectId;
    }


    // Get the page version
    public function getPageVersion($pageId) {

	    // GET IT FROM DB...
	    $pageVersion = "v0.1";

	    return $pageVersion;
    }


    // Get the current user ID
    public function getCurrentUserId() {

		if ( userloggedIn() )
			return $_SESSION['user_ID'];

		return "guest";

    }


    // Get the remote url from the Page ID
    public function getRemoteUrl($pageId) {

	    // GET IT FROM DB...
	    //$remoteUrl = "http://www.cuneyt-tas.com/kitaplar.php";
	    //$remoteUrl = "http://www.bilaltas.net";
	    $remoteUrl = "http://dev.cuneyt-tas.com";
	    //$remoteUrl = "https://www.twelve12.com";
	    //$remoteUrl = "https://www.google.com";

	    if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
	    	$remoteUrl = $_GET['new_url'];


	    return $remoteUrl;
    }





	// JOBS:

	// Pull the remote HTML
	public function saveRemoteHTML($charset) {


		// Do nothing if already saved
		if ( file_exists( $this->pageTempFile ) ) return false;


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


		// Correct the charset
		if ($charset != "" )
			$html = mb_convert_encoding($content, "UTF-8", $charset);



		// SAVING:

		// Create the folder if not exists
		if ( !file_exists($this->pageDir) )
			mkdir($this->pageDir, 0755, true);
		@chmod($this->pageDir, 0755);

		// Save the file if not exists
		if ( !file_exists( $this->pageTempFile ) )
			$saved = file_put_contents( $this->pageTempFile, $html, FILE_TEXT);



		// Return the HTML if successful
		if ($saved)
			return $html;
		return false;


		// NEXT: Filter the HTML to correct URLs

	}


	// Filter the HTML to correct URLs
	public function filterAndUpdateHTML($html) {


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
		            	$this->pageUri."css/".$css_file_name,
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
		if ( file_exists( $this->pageTempFile ) )
			$updated = file_put_contents( $this->pageTempFile, $html, FILE_TEXT);


		// Return the HTML if successful
		if ($updated)
			return $html;
		return false;


		// NEXT: Detect the files that needs to be internalized

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
		if ( !file_exists($this->pageDir."$folderName/") )
			mkdir($this->pageDir."$folderName/", 0755, true);
		@chmod($this->pageDir."$folderName/", 0755);

		// Save the file if not exists
		$downloaded = false;
		if ( !file_exists( $this->pageDir.$fileName ) )
			$downloaded = file_put_contents( $this->pageDir.$fileName, $fileContent, FILE_BINARY);


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
	            return $this->pageUri."fonts/".$font_file_name_hash;

	        },
	        $css
	    );


	return $css;

	}

}