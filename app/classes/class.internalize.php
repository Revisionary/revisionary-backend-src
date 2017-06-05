<?php

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
		if (file_exists($this->pageTempFile))
			unlink($this->pageTempFile);



		// REDIRECTIONS:

		// Http to Https Redirection
		if ( substr($this->remoteUrl, 0, 8) == "https://" && !ssl) {
			header('Location: '.site_url('revise/'.$this->pageId, true)); // Force HTTPS
			die();
		}

		// Https to Http Redirection
		if ( substr($this->remoteUrl, 0, 7) == "http://" && ssl) {
			header('Location: '.site_url('revise/'.$this->pageId, false, true)); // Force HTTP
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
			header( 'Location: ' . site_url('revise/'.$this->pageId) );
			die();


		// Other
		} else {

			// Try non-ssl if the url is on SSL?
			if ( substr($this->remoteUrl, 0, 8) == "https://" ) {

				// Update the nonSSL remoteUrl on DB !!!???
				// ...


				// Refresh the page to try non-ssl
				header( 'Location: ' . site_url('revise/'.$this->pageId) );
				die();


			// If nothing works
			} else {

				header( 'Location: ' . site_url('projects/?error='.$this->remoteUrl) );
				die();

			}


		}






		// JOBS:

		// Save the remote HTML
		$savedHTML = $this->saveRemoteHTML($charset);

		// Correct the urls
		$filtred = false;
		if ($savedHTML) $filtred = $this->filterAndUpdateHTML($savedHTML);

		// Download the CSS files
		$css_downloaded = false;
		if ($filtred) {

			foreach ($this->cssToDownload as $fileName => $url) {
				$css_downloaded = $this->download_remote_file($url, $fileName, "css");

				if (!$css_downloaded) break;
			}

		}

		// Download the fonts
		$font_downloaded = false;
		if ($css_downloaded) {

			foreach ($this->fontsToDownload as $fileName => $url) {
				$font_downloaded = $this->download_remote_file($url, $fileName, "fonts");

				if (!$font_downloaded) break;
			}

		}

		// Download the JS files

		// Download the images !!!

    }




	// TEMP
    public function serveTheURL() {
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


    // Get the remote url from the Page ID
    public function getRemoteUrl($pageId) {

	    // GET IT FROM DB...
	    //$remoteUrl = "http://www.cuneyt-tas.com/kitaplar.php";
	    //$remoteUrl = "http://www.bilaltas.net";
	    $remoteUrl = "http://dev.cuneyt-tas.com";
	    $remoteUrl = "https://www.twelve12.com";

	    return $remoteUrl;
    }


    // Get the current user ID
    public function getCurrentUserId() {

		if ( userloggedIn() )
			return $_SESSION['user_ID'];

		return "guest";

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


		// Add Necessary Spaces - done for a bug
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

		$html = placeNeccessarySpaces($html);
		$html = str_get_html($html, true, false, DEFAULT_TARGET_CHARSET, false);


		// CONVERT ALL HREF ATTRIBUTES TO ABSOLUTE !!! - Correct with existing revisionary page urls ??? (target="_parent")
/*
		foreach ($html->find('*[href]') as $e) {
		    $url = $e->href;
	        $e->href = url_to_absolute($this->remoteUrl, $url);
		}
*/


		// CONVERT ALL SRC ATTRIBUTES TO ABSOLUTE
/*
		foreach ($html->find('*[src]') as $e) {
		    $url = $e->src;
	        $e->src = url_to_absolute($this->remoteUrl, $url);
		}
*/


		// CONVERT ALL SRCSET ATTRIBUTES TO ABSOLUTE
/*
		foreach ($html->find('*[srcset]') as $e) {
		    $attr = explode(',', $e->srcset);

		    $new_srcset = "";

			foreach ( $attr as $src ) {

				$url_exp = array_filter(explode(' ', trim($src)));
				$url = $url_exp[0];
				$size = $url_exp[1];

				$new_srcset .= url_to_absolute($this->remoteUrl, $url)." ".$size.(end($attr) != $src ? ", " : "");

			}

	        $e->srcset = $new_srcset;
		}
*/


		// INTERNALIZE CSS FILES - COUNT THE LOOP FOR PROGRESS BAR !!!
/*
		foreach ($html->find('link[rel="stylesheet"]') as $e) {
		    $url = $e->href;
	        if ( $url != "" ) {

				// If file is from the remote url
		        if ( substr( $url, 0, strlen( parseUrl($this->remoteUrl)['full_host'] ) ) == parseUrl($this->remoteUrl)['full_host'] )

		        	$css_file_name = basename($url);

			        //$e->outertext="<style type='text/css' data-url='".urlencode($e->href)."'>".$this->filter_css($this->remote_css($url))."</style>";
			        $this->cssToDownload["css/".$css_file_name] = $url;

			        // Change the URL
			        $e->href = $this->pageUri."css/".$css_file_name;

	        }

		}
*/


		// IN PAGE STYLES
/*
		foreach ($html->find('style') as $e) {
			$css = $e->innertext;
			$e->innertext = $this->filter_css($css);
		}
*/


		// INLINE STYLES
/*
		foreach ($html->find('*[style]') as $e) {
			$css = $e->style;
			$e->style = $this->filter_css($css);
		}
*/









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






	// DOWNLOAD STYLE FILES
	function remote_css($url) {
		$css = "";

		// CSS Url
		$remote_url = urldecode($url);

		// Check the url
		if ( get_http_response_code($remote_url) == "200" )
	    	$css .= file_get_contents($remote_url);

		//header('Content-type: text/css');
		return $css;
	}



	// DOWNLOAD FILES
	function download_remote_file($url, $fileName, $folderName = "other") {
		$fileContent = "";

		// Check the url
		if ( get_http_response_code($url) == "200" )
	    	$fileContent .= file_get_contents($url);


		if ( $folderName == "css" )
			$fileContent = $this->filter_css($fileContent);


		// SAVING:

		// Create the folder if not exists
		if ( !file_exists($this->pageDir."$folderName/") )
			mkdir($this->pageDir."$folderName/", 0755, true);
		@chmod($this->pageDir."$folderName/", 0755);

		// Save the file if not exists
		$downloaded = false;
		if ( !file_exists( $this->pageDir.$fileName ) )
			$downloaded = file_put_contents( $this->pageDir.$fileName, $fileContent, FILE_TEXT);


		// Return true if successful
		return $downloaded;

	}





	// FILTER CSS
	function filter_css($css) {

		$remote_url = $this->remoteUrl;


		// Internalize Fonts
		$css = $this->detectFonts($css);


		// All url()s
		$css = preg_replace_callback(
	        '%url\s*\(\s*[\\\'"]?(?!(((?:https?:)?\/\/)|(?:data:?:)))([^\\\'")]+)[\\\'"]?\s*\)%',
	        function ($css_urls) {
	            return "url('".url_to_absolute($this->remoteUrl, $css_urls[3])."')";
	        },
	        $css
	    );


		return $css;

	}


	// FILTER JS ??
	function filter_js() {

	}





	// INTERNALIZATION FUNCTIONS

	function detectFonts($css) {

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
	        function ($urls) {

		        $font_remote_url = url_to_absolute($this->remoteUrl, $urls[0]);

		        $parsed_url = parseUrl($font_remote_url);
		        $font_file_name = basename($parsed_url['path']);

				// Add the file to quee
	            $this->fontsToDownload["fonts/".$font_file_name] = $font_remote_url;

				// Change the URL
	            return $this->pageUri."fonts/".$font_file_name;

	        },
	        $css
	    );


	return $css;

	}

}