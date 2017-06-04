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

	// Page File Name
	public $pageFileName;

	// Page File
	public $pageFile;

	// Page Temporary File
	public $pageTempFile;





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
		if ($savedHTML) $filtred = $this->filterAndUpdateHTML($savedHTML);

		// Detect Files to internalize
		if ($filtred) $filesToInternalize = $this->detectFilesToInternalize($filtred);


    }




	// TEMP
    public function serveTheURL() {
	    return cache_url("sites/".$this->userId."/".$this->projectId."/".$this->pageId."/".$this->pageVersion."/_".$this->pageFileName);
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
	    $remoteUrl = "http://www.cuneyt-tas.com";

	    return $remoteUrl;
    }


    // Get the current user ID
    public function getCurrentUserId() {

		if ( userloggedIn() )
			return $_SESSION['user_ID'];

		return false;

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


		// Files to internalize
		$cssFiles = array();


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
		foreach ($html->find('*[href]') as $e) {
		    $url = $e->href;
	        $e->href = $this->relativeToAbsoluteUrl($url);
		}


		// CONVERT ALL SRC ATTRIBUTES TO ABSOLUTE
		foreach ($html->find('*[src]') as $e) {
		    $url = $e->src;
	        $e->src = $this->relativeToAbsoluteUrl($url);
		}


		// CONVERT ALL SRCSET ATTRIBUTES TO ABSOLUTE
		foreach ($html->find('*[srcset]') as $e) {
		    $attr = explode(',', $e->srcset);

		    $new_srcset = "";

			foreach ( $attr as $src ) {

				$url_exp = array_filter(explode(' ', trim($src)));
				$url = $url_exp[0];
				$size = $url_exp[1];

				$new_srcset .= $this->relativeToAbsoluteUrl($url)." ".$size.(end($attr) != $src ? ", " : "");

			}

	        $e->srcset = $new_srcset;
		}


		// IN PAGE STYLES !!!
		foreach ($html->find('style') as $e) {
			$css = $e->innertext;
			$e->innertext = filter_css($css);
		}


		// INLINE STYLES !!!
		foreach ($html->find('*[style]') as $e) {
			$css = $e->style;
			$e->style = filter_css($css);
		}


		// DETECT CSS FILES TO INTERNALIZE !!!
		foreach ($html->find('link[rel="stylesheet"]') as $e) {
		    $url = $e->href;
	        if ( $url != "" ) {

		        if ( substr( $url, 0, strlen( parseUrl($this->remoteUrl)['full_host'] ) ) == parseUrl($this->remoteUrl)['full_host'] )
			        $e->href = site_url("remote-css/".urlencode($this->relativeToAbsoluteUrl($url)) );
			        $cssFiles[] = $url;

	        }
		}









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


	public function detectFilesToInternalize($html) {


/*

		// DATA-ELEMENT-INDEX !!!
		function data_element_index($html, $i = 0) {
			global $i;


			foreach ($html->find('*') as $e) {

				if ($e->tag == "br" || $e->tag == "BR") continue;
				if ($e->tag == "script" || $e->tag == "SCRIPT") continue;

				$e->setAttribute('data-element-index', $i); $i++;
				data_element_index($e, $i);

			}

		}
		data_element_index( $html->find('body', 0) );
*/


		// ADD THE PINS ?

		return array();

	}




	// 2. Pull the remote CSS


	// 3. Pull the remote Fonts




	// REMOTE STYLE FILES
	function remote_css() {
		$css = "";

		$remote_url = urldecode($_GET['remote_css']); // CSS Url

		// Check the url
		if ( get_http_response_code($remote_url) == "200" )
	    	$css .= file_get_contents($remote_url); // CSS DATA


		$css = filter_css($css, $remote_url);


		header('Content-type: text/css');
		echo $css;
		die();

	}


	// REMOTE FONT FILES
	function remote_font() {
		$font = "";

		$remote_url = urldecode($_GET['remote_font']);

		// Check the url
		if ( get_http_response_code($remote_url) == "200" )
	    	$font .= file_get_contents($remote_url); // get css file

		header('Content-type: application/octet-stream');
		echo $font;
		die();

	}


	// GET RESPONSE CODE
	function get_http_response_code($url) {
		$headers = @get_headers(urldecode($url));

		if ($headers)
			return intval(substr($headers[0], 9, 3));
		else
			return 0;
	}





	// FILTER CSS
	function filter_css($css) {

		$remote_url = parseUrl($this->remoteUrl)['full_path'];

		// Internalize Fonts
		$css = internalize_fonts($css, $remote_url);


	    // All @imports
		$css = preg_replace_callback(
	        //'/url\(([\s])?([\"|\'])?(.*?)([\"|\'])?([\s])?\)/i',
	        '/@import\s*(url)?\s*\(?([^;]+?)\)?;/i',
	        function ($css_urls) use($remote_url) {
		        $new_url = $this->relativeToAbsoluteUrl($css_urls[2], $remote_url);


				// Remote Url Host
				$remote_host = parseUrl($remote_url)['full_host'];


				// Current Url Host
				$url_host =  parseUrl($new_url)['full_host'];


				if ( $url_host == $remote_host ) {

					return "@import url('?remote_css=".urlencode($new_url)."');";

				} else {

					return "@import url('".$new_url."');";

				}

	        },
	        $css
	    );


		// All url()s
		$css = preg_replace_callback(
	        '%url\s*\(\s*[\\\'"]?(?!(((?:https?:)?\/\/)|(?:data:?:)))([^\\\'")]+)[\\\'"]?\s*\)%',
	        function ($css_urls) use($remote_url) {
	            return "url('".$this->relativeToAbsoluteUrl($css_urls[3], $remote_url)."')";
	        },
	        $css
	    );


		return $css;

	}


	// FILTER JS ??
	function filter_js() {

	}




	// URL UPDATES - Relative to absolute converter
	function relativeToAbsoluteUrl($url) {

		$url = urldecode($url);
		$remote_url = urldecode($this->remoteUrl);



		// Remote URL Parse
		$parsed_remote_url = parseUrl($remote_url);

		// Current URL Parse
		$parsed_url = parseUrl($url);



		// URL Type: "//example.com/folder/file.jpg"
		if (
			substr( $url, 0, 2 ) == "//" && // Begins with "//"
			$parsed_url['scheme'] == "" && // No http or https
			substr( $url, 0, 2+strlen($parsed_remote_url['host']) ) == "//".$parsed_remote_url['host'] // Not external
		) {

			$updated_url = $parsed_remote_url['scheme'].":".$url;


		// URL Type: "/folder/file.jpg" or "folder/file.jpg" (All local URLs - Relative path)
		} elseif ( $parsed_url['scheme'] == '' ) {


			// URL Type: "/folder/file.jpg"
			if ( substr($url, 0, 1) == "/" && substr( $url, 0, 2 ) != "//" ) {

				$updated_url = $parsed_remote_url['full_host'].$url;


			// URL Type: "./folder/file.jpg"
			} elseif ( substr($url, 0, 2) == "./" ) {

				$updated_url = $parsed_remote_url['full_path']."/".str_replace('./', '', $url);


			// URL Type: "../folder/file.jpg"
			} elseif ( strpos($url, '../') !== false ) {

				// Exclude the path
				$updated_url = revisionary_path_excluder($parsed_remote_url['full_path'], $url);


			// URL Type: "folder/file.jpg"
			} else {

				$updated_url = $parsed_remote_url['full_path']."/".$url;

			}



		} elseif (
			($parsed_url['scheme'] == "http" || $parsed_url['scheme'] == "https") &&
			$parsed_url['full_host'] == $parsed_remote_url['full_host']
		) { // If urls have protocol and not far url


			if ( strpos($url, '../') !== false ) {

				// Exclude the path
				$updated_url = revisionary_path_excluder("", $url);

			} else {

				$updated_url = $url;

			}


		// URL Type: "http://example.com/folder/file.jpg" (External URLs, have protocol - Absolute path)
		} else {

			$updated_url = $url;

		}

		return $updated_url;

	}


	// URL PATH EXCLUDER
	function path_excluder($remote_path, $url) {

		if ( strpos(basename($remote_path), '.') !== false )
			$remote_path = str_replace(basename($remote_path), '', $remote_path);

		$full_url = $remote_path.$url;

		$exp_url = explode('/', $full_url);

		$key_to_remove = [];
		$redoit = true;
		while ($redoit == true) {
			$redoit = false;
			foreach ($exp_url as $key => $part) {
				if ($part == "..") {
					unset($exp_url[$key-1]);
					unset($exp_url[$key]);
					$exp_url = array_values($exp_url);
					$redoit = true;
					break;
				}
			}
		}


		$new_url = "";
		foreach ($exp_url as $key_new => $part_new) {
			if ( !in_array($key_new, $key_to_remove) )
				$new_url .= $part_new.(end($exp_url) != $part_new ? "/" : "");
		}


		return $new_url;

	}





	// INTERNALIZATION FUNCTIONS

	function internalize_fonts($css, $remote_url) {


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
	        function ($urls) use($remote_url) {
	            return "?remote_font=".urlencode(revisionary_update_urls($urls[0], $remote_url));
	        },
	        $css
	    );


	return $css;

	}

}