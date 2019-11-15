/*jshint multistr: true */
// FUNCTIONS:
// DB: Run the internalizator
function checkPageStatus(phase_ID, page_ID, queue_ID, processID, loadingProcessID) {


	// If being force reinternalizing, update the URL
	removeQueryArgFromCurrentUrl('redownload');
	removeQueryArgFromCurrentUrl('ssr');
	removeQueryArgFromCurrentUrl('new');
	removeQueryArgFromCurrentUrl('secondtry');


	// Get the up-to-date pins
	var statusCheckRequest = ajax('internalize-status',
	{
		'phase_ID'		: phase_ID,
		'page_ID'		: page_ID,
		'queue_ID'		: queue_ID,
		'processID'		: processID

	}).done(function(result) {

		var data = result.data.final; console.log('RESULTS: ', result);


		// LOG
		$.each(result.data, function(key, value){

			// Append the log !!!
			if (key != "final")	console.log(key + ': ', value);

		});


		// Update the proggress bar
		var width = data.processPercentage;
		editProcess(loadingProcessID, width);


		// Finish the process if done
		if (width == 100)
			endProcess(loadingProcessID);


		// Print the current status
		$('#loading-info').text( Math.round(width) + '% ' + data.processDescription + '...');


		// Print the error message when stops before completion
		if (data.status == "not-running" &&	data.processStatus != "ready") {
			$('#loading-info').text( 'Error');
			editProcess(loadingProcessID, 0);
		}


		// If successfully downloaded
		if (width == 100 && data.processStatus == "ready") {

			// Update the global page URL
			page_URL = data.phaseUrl + '?v=' + data.internalized;
			console.log('PAGE URL: ', page_URL);


			// Redirects
			if (
				( page_URL.startsWith("http://") && currentUrl().startsWith("https://") ) ||
				( page_URL.startsWith("https://") && currentUrl().startsWith("http://") )
			) location.reload();


			// Update the iframe url
			$('#the-page').attr('src', page_URL);


			// Run the inspector
			runTheInspector();

		}


		// Restart if not done
		if (data.status != "not-running" && data.processStatus != "ready") {

			setTimeout(function() {

				checkPageStatus(phase_ID, page_ID, queue_ID, processID, loadingProcessID);

			}, 1000);

		}


	}).fail(function() {


		// Abort the latest request if not finalized
		if(statusCheckRequest && statusCheckRequest.readyState != 4) {
			console.log('Latest status check request aborted');
			statusCheckRequest.abort();
		}

		setTimeout(function() {

			checkPageStatus(phase_ID, page_ID, queue_ID, processID, loadingProcessID);

		}, 1000);


	});


}


// Initiate the inspector
function runTheInspector() {



	// CLOSE ALL THE OPEN TABS
	$('.opener').each(function() {

		toggleTab( $(this), true );

	});



	// WHEN IFRAME DOCUMENT READY !!! ?
	$('#the-page').contents().ready(function() {


	});


	// WHEN IFRAME HAS LOADED
	$('#the-page').on('load', function() {


		console.log('IFRAME DOCUMENT LOADED!', canAccessIFrame( $(this) ));


		// If we have access on this iframe (CORS Check)
		if ( canAccessIFrame($(this)) ) {


			// Iframe element
		    iframe = $('#the-page').contents();
			iframeLoaded = true;


			// After coming back to the real page
			if (page_redirected) {


				console.log('LOAD PAGE REOPENED');
				page_redirected = false;

				setTimeout(function() { // Does not work sometimes, and needs improvement !!!

					// Remove the overlay
					$('#wait').hide();

					// Show the pins
					$('#pins').css('opacity', '');


					iframe.scrollTop(oldScrollOffset_top);
					iframe.scrollLeft(oldScrollOffset_left);
					oldScrollOffset_top = oldScrollOffset_left = 0;


					console.log('LOAD PAGE REOPEN COMPLETE', page_redirected);

				}, 2000);


			} else {


				// UPDATE INITIAL CURSOR TYPE

				// Check for the client settings
				var clientPinType = get_client_cache(user_ID + '_currentPinType');
				var clientPinPrivate = get_client_cache(user_ID + '_currentPinPrivate');

				if (clientPinType != null && clientPinPrivate != null) {
					currentPinType = clientPinType;
					currentPinPrivate = clientPinPrivate;
				}

				switchPinType(currentPinType, currentPinPrivate);


			}



			// PINS:
			// Get latest pins and apply them to the page
			Pins = [];
			getPins(true, openPin);
			openPin = null;




			// REDIRECT DETECTION
			documentChild = $(this).prop("contentWindow").document;
	        childWindow = $(this).prop("contentWindow");

	        $(documentChild).ready(function() {
				$(childWindow).on('beforeunload', function() {


					console.log('REDIRECTING DETECTED...');


					// If pin window open
					if (pinWindowOpen) {

						// Register the open pin
						openPin = pinWindow().attr('data-pin-id');
						console.log('AFTER REDIRECT, OPEN PIN ID #' + openPin);

						// Close pin window
						closePinWindow(false);

					}


					// Stop Autorefresh
					stopAutoRefresh();


					$('#wait').show();

					// Show the pins
					$('#pins').css('opacity', '0');

					oldScrollOffset_top = scrollOffset_top;
					oldScrollOffset_left = scrollOffset_left;


					return;

				});
	        });


		} else { // IF NO ACCESS OF IFRAME


			console.log('*** LOAD REDIRECTING BACK TO...', page_URL);

			//window.frames["the-page"].location = page_URL;
			$('#the-page').attr('src', page_URL);
			page_redirected = true;
			iframeLoaded = false;


			return;

		}


		console.log('Load Complete', canAccessIFrame( $(this) ));



		// SITE STYLES
		iframeElement('body').append(' \
		<style> \
			/* Auto-height edited images */ \
			img[data-revisionary-showing-content-changes="1"] { height: auto !important; } \
			iframe { pointer-events: none !important; } \
			* { -webkit-user-select: none !important; -moz-user-select: none !important;user-select: none !important; } \
			.revisionary-show { position: absolute !important; width: 0 !important; height: 0 !important; display: inline-block !important; } \
		</style> \
		');



	    // Update the title
		if ( iframeElement('title').length ) {
			$('title').text( "Revise Page: " + iframeElement('title').text() );
		}



		// If new downloaded site, ask whether or not it's showing correctly
		if ( $('.ask-showing-correctly').length ) $('.ask-showing-correctly').addClass('open');



		// MOUSE ACTIONS:
		var mouseDownOnContentEdit = false;
		var scrollTimer, scrollFlag = false;
	    iframe.on('mousemove', function(e) { // Detect the mouse moves in frame


		    // Mouse coordinates according to the iframe container
		    containerX = e.clientX * iframeScale;
		    containerY = e.clientY * iframeScale;
		    //console.log('Container: ', containerX, containerY);


		    // Follow the mouse cursor
			$('.mouse-cursor').css({
				left:  containerX,
				top:   containerY
			});



			// FOCUSING:
		    // Focused Element is the mouse pointed element as default
	        focused_element = $(e.target);

	        focused_element_index = focused_element.attr('data-revisionary-index');
	        focused_element_has_index = focused_element_index != null ? true : false;
		    focused_element_index = focused_element_index != null ? focused_element_index : 0;
	        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
			focused_element_html = focused_element.html();
	        focused_element_children = focused_element.children();
	        focused_element_grand_children = focused_element_children.children();
			focused_element_pin = pinElement(focused_element_index, true);
			focused_element_live_pin = $('#pins > pin[data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"]');
			focused_element_edited_parents = focused_element.parents('[data-revisionary-index][data-revisionary-content-edited]');
			focused_element_has_edited_child = focused_element.find('[data-revisionary-index][data-revisionary-content-edited]').length;



			// Work only if cursor is active
			if (cursorActive && !hoveringPin) {



				// REFOCUS WORKS:
				// Re-focus if only child element has no child and has content: <p><b focused>Lorem ipsum</b></p>
				if (
					focused_element_text == "" && // Focused element has no content
					focused_element_children.length == 1 && // Has only one child
					focused_element_grand_children.length == 0 && // No grand child
					focused_element_children.first().text().trim() != "" // Grand child should have content
				) {

					// Re-focus to the child element
					focused_element = focused_element_children.first();


					//console.log('REFOCUS - Only child element has no child and has content: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class'));

				}


				// Re-focus to the edited element if this is child of it: <p data-edited="1" focused><b>Lorem
				if (focused_element_edited_parents.length) {

					// Re-focus to the parent edited element
					focused_element = focused_element_edited_parents.first();


					//console.log('REFOCUS - Already edited closest parent: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class'));

				}


				// Update refocused sub elements
		        focused_element_index = focused_element.attr('data-revisionary-index');
		        focused_element_has_index = focused_element_index != null ? true : false;
		        focused_element_index = focused_element_index != null ? focused_element_index : 0;
		        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
				focused_element_html = focused_element.html();
		        focused_element_children = focused_element.children();
		        focused_element_grand_children = focused_element_children.children();
				focused_element_pin = pinElement(focused_element_index, true);
				focused_element_live_pin = $('#pins > pin[data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"]');
				focused_element_edited_parents = focused_element.parents('[data-revisionary-index][data-revisionary-content-edited]');
				focused_element_has_edited_child = focused_element.find('[data-revisionary-index][data-revisionary-content-edited]').length;



				// EDITABLE CHECKS:
				hoveringText = false;
		        focused_element_editable = false;
		        focused_element_html_editable = false;


				// Directly editable:
				// Check element text editable: <p>Lorem ipsum dolor sit amet...
		        if (
			        easy_html_elements.indexOf( focused_element.prop('tagName').toUpperCase() ) != -1 && // In easy HTML elements?
		        	focused_element_text.trim() != "" && // If not empty
		        	focused_element.html() != "&nbsp;"  && // If really not empty
		        	focused_element_children.length == 0 // If doesn't have any child
		        ) {

					hoveringText = true;
					focused_element_editable = true; // Obviously Text Editable
					focused_element_html_editable = true;
					//console.log( '* Obviously Text Editable: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );
					//console.log( 'Focused Element Text: ' + focused_element_text );

				}


				// Image editable:
				// Check element image editable: <img src="#">...
				hoveringImage = false;
		        if ( focused_element.prop('tagName').toUpperCase() == "IMG" ) {

					hoveringImage = true;
					focused_element_editable = true; // Obviously Image Editable
					//console.log( '* Obviously Image Editable: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );
					//console.log( 'Focused Element Image: ' + focused_element.prop('src') );

				}


				// Check if element has children but doesn't have grand children: <p>Lorem ipsum <a href="#">dolor</a> sit amet...
				if (
					focused_element_children.length > 0 && // Has child
					focused_element_grand_children.length == 0 && // No grand child
					focused_element_text.trim() != "" && // And, also have to have text
					focused_element.html() != "&nbsp;" // And, also have to have text
				) {


					// Also check the children's tagname
					var hardToEdit = true;
					focused_element_children.each(function() {

						// In easy HTML elements?
						if ( easy_with_br.indexOf( $(this).prop('tagName').toUpperCase() ) != -1 ) hardToEdit = false;

					});

					if (!hardToEdit) {

						hoveringText = true;
						focused_element_editable = true;
						focused_element_html_editable = true;
						//console.log( '* Text Editable (No Grand Child): ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );
						//console.log( 'Focused Element Text: ' + focused_element_text );

					}

				}


				// Chech if element has only one grand child and it doesn't have any child: <p>Lorem ipsum <a href="#"><strong>dolor</strong></a> sit amet...
				if (
					focused_element_children.length > 0 && // Has child
					focused_element_grand_children.length > 0 && // Has grand child
					focused_element_text.trim() != "" && // And, also have to have text
					focused_element.html() != "&nbsp;" // And, also have to have text
				) {


					// Also check the children's tagname
					var easyToEdit = false;
					focused_element_children.each(function() {

						var child = $(this);
						var grandChildren = child.children();


						if (
							easy_with_br.indexOf( child.prop('tagName').toUpperCase() ) != -1 && // Child is easy to edit
							grandChildren.length == 1 && // Grand child has no more than 1 child !!! ???
							easy_with_br.indexOf( grandChildren.first().prop('tagName').toUpperCase() ) != -1 // And that guy is easy to edit as well
						)

							easyToEdit = true;

					});

					if (easyToEdit) {

						hoveringText = true;
						focused_element_editable = true;
						focused_element_html_editable = true;
						//console.log( '* Text Editable (One Grand Child): ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );
						//console.log( 'Focused Element Text: ' + focused_element_text );

					}


				}


				// Check the submit buttons: <input type="submit | reset">... // !!!
				hoveringButton = false;
		        if (
		        	focused_element.prop('tagName').toUpperCase() == "INPUT" &&
		        	(
						focused_element.attr("type") == "text" ||
						focused_element.attr("type") == "email" ||
						focused_element.attr("type") == "url" ||
						focused_element.attr("type") == "tel" ||
						focused_element.attr("type") == "submit" ||
						focused_element.attr("type") == "reset"
		        	)
		        ) {

					hoveringButton = true;
					hoveringText = true;
					focused_element_editable = true; // Obviously Image Editable
					//console.log( '* Button Editable: ' + focused_element.prop('tagName').toUpperCase() );
					//console.log( 'Focused Button Text: ' + focused_element.attr('value') );

				}



				// PREVENTIONS:
				// Check if it doesn't have any element index: <p data-revisionary-index="16">...
				if (focused_element_editable && !focused_element_has_index) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					//console.log( '* Element editable but NO INDEX: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );

				}


				// If focused element has edited child, don't focus it
				if (focused_element_has_edited_child > 1 ) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					//console.log( '* Element editable but there are edited #'+focused_element_has_edited_child+' children: ' + focused_element.prop('tagName').toUpperCase() + '.' + focused_element.attr('class') );

				}




				// // See what am I focusing
				// console.log("###############################");
				// console.log("CURRENT FOCUSED: ", focused_element.prop('tagName').toUpperCase(), focused_element_index );
				// console.log("CURRENT FOCUSED EDITABLE: ", focused_element_editable, focused_element_html_editable );
				// //console.log("CURRENT FOCUSED PIN PRIVATE?: ", focused_element_pin.attr('data-pin-private') );
				// console.log("HOVERING ON A TEXT?: ", hoveringText );
				// console.log("HOVERING ON AN IMAGE?: ", hoveringImage );
				// console.log("HOVERING ON A BUTTON?: ", hoveringButton );
				// console.log("###############################");




				// Clean Other Outlines
				removeOutline();

				// Reset the pin opacity
				$('#pins > pin').css('opacity', '');



				// REACTIONS:
				focused_element_has_live_pin = focused_element_live_pin.length ? true : false;



				// If current element already has a live pin
				if ( focused_element_live_pin.length ) {


					// Point to the pin
					$('#pins > pin:not([data-revisionary-index="'+ focused_element_index +'"])').css('opacity', '0.2');


					// Update the cursor
					changePinNumber( focused_element_live_pin.text() );


					switchCursorType( focused_element_live_pin.attr('data-pin-type'), focused_element_live_pin.attr('data-pin-private'), true);
					outline( focused_element, focused_element_live_pin.attr('data-pin-private'), focused_element_live_pin.attr('data-pin-type') );
					//console.log('This element already has a live pin.');


				} else {


					// Re-update the cursor number
					currentPinNumber = $('#pins > pin').length + 1;
					changePinNumber(currentPinNumber);


					// Editable check
					if (focused_element_editable && currentPinType == "live") {


						switchCursorType('live');
						outline(focused_element, currentPinPrivate, currentPinType);


					} else {


						// If not editable, switch back to the standard pin
						switchCursorType('standard');

						if (focused_element_has_index)
							outline(focused_element, currentPinPrivate, (focused_element_editable && currentPinType == "live" ? "live" : "standard"));


					}



				}



			} // If cursor active


		}).on('click', function(e) { // Detect the mouse clicks in frame !!!! Does not prevent the mouse click events that's already registered


			console.log('MOUSE CLICKED');


			// Prevent clicking something
			e.preventDefault();
			e.stopPropagation();
			return false;


		}).on('mousedown', function(e) { // Detect the mouse clicks in frame


			//console.log('MOUSE DOWN');


			// While editing a content on page
			mouseDownOnContentEdit = cursorActive && focused_element_has_live_pin ? true : false;


			//$('#the-page').css('pointer-events', 'none');


		}).on('mouseup', function(e) { // Detect the mouse clicks in frame


			//console.log('MOUSE CLICKED SOMEWHERE', focused_element_index);


			// If cursor is active
			if (cursorActive) {


				// If focused element has a live pin
				if (focused_element_has_live_pin) {

					// Open the new pin window if already open one or clicking an image editable
					if (
						pinWindowOpen ||
						focused_element_pin.attr('data-pin-modification-type') == "image" ||
						focused_element.attr('data-revisionary-showing-content-changes') == "0"
					)
						openPinWindow( focused_element_pin.attr('data-pin-id') );


				} else {


					// Add a pin and open a pin window
					if ( !mouseDownOnContentEdit )
						putPin(focused_element_index, e.pageX, e.pageY, currentCursorType, currentPinPrivate);


				}


			} else {


				// Close the pin window if open and not cursor active and not content editable
				if ( pinWindowOpen && !iframeElement(focused_element_index).is('[contenteditable]') && !shifted )
					closePinWindow(true);


			}


			// Re-enable iframe
			//$('#the-page').css('pointer-events', '');


			// Prevent clicking something
			e.preventDefault();
			e.stopPropagation();
			return false;


		}).on('scroll', function(e) { // Detect the scroll to re-position pins


			//console.log('SCROLLIIIIIIIING');


			// Add scrolling class to the body
			if (!scrollFlag) {
				scrollFlag = true;
				$('body').addClass('scrolling');
			}
			clearTimeout(scrollTimer);
			scrollTimer = setTimeout(function() {
				$('body').removeClass('scrolling');
				scrollFlag = false;
			}, 200);


		    // Re-Locate all the pins
			relocatePins();


		}).on('keydown', function(e) { // Detect the scroll to re-position pins


			if (e.shiftKey) shifted = true;


			if (shifted && !pinWindowOpen && currentPinType != "browse") {

				shiftToggle = true;
				console.log('SHIFTED');

				currentPinTypeWas = currentPinType;
				toggleCursorActive(true); // Force close
				currentPinType = "browse";

			}


		}).on('keyup', function(e) { // Detect the scroll to re-position pins


			if (shifted && shiftToggle && !pinWindowOpen && currentPinType == "browse") {

				shiftToggle = false;
				console.log('UNSHIFTED');

				currentPinType = currentPinTypeWas;
				toggleCursorActive(false, true); // Force Open

			}


			shifted = false;


		});


		// Detect changes on page text
		var doChangeOnPage = {};
		iframe.on('input', '[contenteditable="true"][data-revisionary-index]', function(e) {


			var element_index = $(this).attr('data-revisionary-index');
			var pin_ID = pinElement('[data-pin-type="live"][data-revisionary-index="'+element_index+'"]').attr('data-pin-id');
			var changedElement = $(this);
			var modification = changedElement.html();


			// If edited element is a submit or reset input button
			if (
	        	changedElement.prop('tagName').toUpperCase() == "INPUT" &&
	        	(
	        		changedElement.attr("type") == "text" ||
	        		changedElement.attr("type") == "email" ||
	        		changedElement.attr("type") == "url" ||
	        		changedElement.attr("type") == "tel" ||
	        		changedElement.attr("type") == "submit" ||
	        		changedElement.attr("type") == "reset"
	        	)
	        ) {
				modification = changedElement.val();
			}


			//console.log('REGISTERED CHANGES', modification);


			// Stop the auto-refresh
			stopAutoRefresh();


			// Update the element, pin and pin window status
			updateAttributes(pin_ID, 'data-revisionary-content-edited', "1");
			updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', "1");


			// Instant apply the change on pin window
			pinWindow(pin_ID).find('.content-editor .edit-content.changes').html(modification);


			// If differences tab is open
			if ( pinWindow(pin_ID).hasClass('show-differences') ) {


				var originalContent = pinWindow(pin_ID).find('.content-editor .edit-content.original').html();
				var changedContent = pinWindow(pin_ID).find('.content-editor .edit-content.changes').html();


				// Difference check
				var diffContent = diffCheck(originalContent, changedContent);


				// Add the differences content
				pinWindow(pin_ID).find('.content-editor .edit-content.differences').html( diffContent );

			}


			// Remove unsent job
			if (doChangeOnPage[element_index]) clearTimeout(doChangeOnPage[element_index]);

			// Send changes to DB after 1 second
			doChangeOnPage[element_index] = setTimeout(function(){

				saveChange(pin_ID, modification);

			}, 1000);

			//console.log('Content changed.');


		}).on('focus', '[contenteditable="true"][data-revisionary-index]', function(e) { // When clicked an editable text


			// Remove all the other focus outlines
			iframeElement('.revisionary-focused').removeClass('revisionary-focused');
			removeOutline();


			// Outline this focused element
			outline(focused_element, focused_element_live_pin.attr('data-pin-private'));
			focused_element.addClass('revisionary-focused');


			// Open the new pin window if already open
			if (
				pinWindowOpen &&
				focused_element_live_pin != null && focused_element_live_pin.length &&
				pinWindow().attr('data-revisionary-index') != focused_element_index
			)
				openPinWindow( focused_element_live_pin.attr('data-pin-id') );


		}).on('blur', '[contenteditable="true"][data-revisionary-index]', function(e) { // When clicked an editable text


			iframeElement('.revisionary-focused').removeClass('revisionary-focused');
			removeOutline();


		}).on('paste', '[contenteditable]', function(e) { // When pasting rich text


			e.preventDefault();

			var plain_text = (e.originalEvent || e).clipboardData.getData('text/plain');

			if(typeof plain_text !== 'undefined')
				document.getElementById("the-page").contentWindow.document.execCommand('insertText', false, plain_text);

			console.log('PASTED: ', plain_text);


		}).on('click', 'a[href]', function(e) { // When pasting rich text


			var link = $(this).attr('href');
			var absoluteLink = urlStandardize( $(this).prop('href') );


			// Record the clicked link
			if (
				currentPinType == "browse" &&
				!link.startsWith('#') &&
				!link.startsWith('javascript:') // jshint ignore:line
			) {


				// Encoded Link
				var encodedLink = encodeURIComponent(absoluteLink);


				// New page link
				var newPageLink = "/projects/?add_new=true&pinmode=browse&page_width="+ page_width +"&page_height="+ page_height +"&project_ID=" + project_ID + "&page-url=" + encodedLink;


				// Search in my pages registered
				var pageFound = myPages.find(function(page) {
					return urlStandardize(page.page_url, true) == urlStandardize(absoluteLink, true) && page.project_ID == project_ID ? true : false;
				});


				// If the page has already been downloaded, go revising that page
				if (pageFound) {

					newPageLink = "/page/" + pageFound.page_ID + "?pinmode=browse";

					console.log('ALREADY DOWNLOADED!!!', newPageLink);

				}


				// Remove current page if no pins added
				if ( queryParameter(currentUrl(), 'new') == "page" && Pins.length == 0 ) {


					// Remove the page and then go to the link
					doAction('remove', 'page', page_ID, 'redirect', newPageLink);


				} else {


					// Redirect
					window.open(newPageLink, "_self");


				}


				e.preventDefault();
				e.stopPropagation();
				return false;
			}


		});


		$(window).on('resize', function(e) { // Detect the window resizing to re-position pins

			//console.log('RESIZIIIIIIIING');


			// Add scrolling class to the body
			if (!scrollFlag) {
				scrollFlag = true;
				$('body').addClass('scrolling');
			}
			clearTimeout(scrollTimer);
			scrollTimer = setTimeout(function() {
				$('body').removeClass('scrolling');
				scrollFlag = false;
			}, 200);


		    // Re-Locate all the pins
		    relocatePins();

		});


	});


}


// Update location values
function updateLocationValues() {

	// Update the values
	offset = $('#the-page').offset();

	scrollOffset_top = iframe.scrollTop();
	scrollOffset_left = iframe.scrollLeft();

	scrollX = scrollOffset_left * iframeScale;
	scrollY = scrollOffset_top * iframeScale;

	pinWindowWidth = pinWindow().outerWidth();
	pinWindowHeight = pinWindow().outerHeight();

}


// Detect colors in the page
function detectColors() {


	//console.log('Colors are being detected in the page...');


	iframeElement('body *').each(function() {


		var color = $(this).css('color');
		var bgColor = $(this).css('background-color');


		var colorCount = parseInt( page_colors[color] ) || 0;
		page_colors[color] = colorCount + 1;


		var bgColorCount = parseInt( page_colors[bgColor] ) || 0;
		page_colors[bgColor] = bgColorCount + 1;


	});


	// Order the colors
	colorsSorted = Object.keys(page_colors).sort(function(a,b){ return page_colors[b]-page_colors[a]; });
	$("input[type='color']").spectrum("option", "palette", colorsSorted);


	//console.log('Color detection complete.', colorsSorted);

}



// TABS:
// Tab Toggler
function toggleTab(opener, forceClose) {


	forceClose = assignDefault(forceClose, false);


	var sideElement = opener.parent();


	if (sideElement.hasClass('open') || forceClose) {
		sideElement.removeClass('open');
		opener.removeClass('open');
	} else {
		sideElement.addClass('open');
		opener.addClass('open');
	}

	// Update the list when opening the pins list tab
	pinsListOpen = $('#top-bar .pins').hasClass('open');
	if (pinsListOpen) updatePinsList();

}


// Update pins list in the Pins tab
function updatePinsList() {


	// Clear the list
	$('.pins-list').html('<div class="xl-center">No pins added yet.</div>');


	$(Pins).each(function(i, pin) {

		if (i == 0) $('.pins-list').html('');

		var pin_number = i + 1;

		// Add the pin to the list
		$('.pins-list').append(
			listedPinTemplate(pin_number, pin)
		);


	});

}



// OUTLINES:
// Color the element
function outline(element, private_pin, pin_type) {


	pin_type = assignDefault(pin_type, "live");


	if (iframeLoaded == false) return false;


	var block = pin_type == "live" ? false : true;

	var elementColor = private_pin == 1 ? '#FC0FB3' : '#7ED321';
	if (block) elementColor = private_pin == 1 ? '#6b95f3' : '#1DBCC9';

	var outlineWidth = '2px';
	if (block) outlineWidth = '2px';

	if (element != null) element.css('outline', outlineWidth + ' dashed ' + elementColor, 'important');

}


// Color the element
function removeOutline() {

	if (iframeLoaded == false) return false;

	// Remove outlines from iframe
	iframeElement('*:not(.revisionary-focused)').css('outline', '');

	return true;
}



// CURSOR:
// Switch to a different pin mode
function switchPinType(pinType, pinPrivate) {

	log('Switched Pin Type: ', pinType);
	log('Switched Pin Private: ', pinPrivate);


	currentPinTypeWas = currentPinType;
	currentPinPrivateWas = currentPinPrivate;

	currentPinType = pinType;
	currentPinPrivate = parseInt(pinPrivate);


	// Change the activator color and label
	if (pinType == "browse") currentPinLabel = pinModes.browse;
	if (pinType == "live") currentPinLabel = pinModes.live;
	if (pinType == "standard") currentPinLabel = pinModes.standard;
	if (currentPinPrivate == "1") currentPinLabel = pinModes['private-live'];



	// Activator updates
	activator.attr('data-pin-type', currentPinType).attr('data-pin-private', currentPinPrivate).find('.mode-label').text(currentPinLabel);



	// Hide the dropdown
	$('ul.pin-types').hide();

	// Reset the list
	$('ul.pin-types > li').removeClass('selected');

	// Select on the list
	$('ul.pin-types > li[data-pin-type="'+ currentPinType +'"][data-pin-private="'+ currentPinPrivate +'"]').addClass('selected');



	// Change the cursor color
	switchCursorType(pinType == 'live' ? 'standard' : pinType, currentPinPrivate);



	// URL update
	if (history.pushState) {
	    var newurl = queryParameter(currentUrl(), 'pinmode', (currentPinType == "live" ? "" : currentPinType));
	    newurl = queryParameter(newurl, 'privatepin', (currentPinPrivate == 1 ? "1" : ""));
	    window.history.pushState({path:newurl},'',newurl);
	}



	// Client settings
	set_client_cache(user_ID + '_currentPinType', currentPinType);
	set_client_cache(user_ID + '_currentPinPrivate', currentPinPrivate);



	// Deactivate the cursor
	if (pinType == "browse") toggleCursorActive(true);

	// Activate the cursor
	else toggleCursorActive(false, true);



	// Close the open pin window
	if (pinWindowOpen && iframeLoaded) closePinWindow();

}


// Switch to a different cursor mode
function switchCursorType(cursorType, pinPrivate, existing) {


	pinPrivate = assignDefault(pinPrivate, currentPinPrivate);
	existing = assignDefault(existing, false);


	//console.log(cursorType);


	// Showing an existing pin on cursor?
	if (existing) {

		cursor.addClass('existing');

	} else {

		cursor.removeClass('existing');

	}


	cursor.attr('data-pin-type', cursorType).attr('data-pin-private', pinPrivate);
	currentCursorType = cursorType;

}


// Toggle Inspect Mode
function toggleCursorActive(forceClose, forceOpen) {


	forceClose = assignDefault(forceClose, false);
	forceOpen = assignDefault(forceOpen, false);


	//cursor.stop();
	var cursorVisible = cursor.hasClass("active");


	// Remove outlines from iframe
	removeOutline();


	if ( (cursorActive || forceClose) && !forceOpen ) {


		// Deactivate
		activator.attr('data-pin-type', 'browse').attr('data-pin-private', '0');

		// Update the label
		$('.current-mode .mode-label').text(currentPinLabel);


		// Hide the cursor
		if (cursorVisible) cursor.removeClass('active');

		// Show the original cursor
		iframeElement('#revisionary-cursor').remove();

		// Enable all the links
	    // ...


		cursorActive = false;
		focused_element = null;

	} else {


		// Activate
		activator.attr('data-pin-type', currentPinType).attr('data-pin-private', currentPinPrivate);

		// Update the label
		$('.current-mode .mode-label').text(currentPinLabel);


		// Show the cursor
		if (!cursorVisible && !pinWindowOpen) cursor.addClass('active');

		// Hide the original cursor
		if ( !iframeElement('#revisionary-cursor').length )
			iframeElement('body').append('<style id="revisionary-cursor"> * { cursor: crosshair !important; } </style>');

		// Disable all the links
	    // ...


		cursorActive = true;

	}


	// Close the open pin window
	if (pinWindowOpen && iframeLoaded) closePinWindow();

}


// Hide cursor !!!
function hideCursor() {

}



// AUTO REFRESH:
// Start auto-refresh
function startAutoRefresh(interval) {


	interval = assignDefault(interval, autoRefreshInterval);


	console.log('AUTO-REFRESH PINS STARTED');

	autoRefreshTimer = setInterval(function() {

		console.log('Auto checking the pins...');


		// Abort the latest request if not finalized
		if(autoRefreshRequest && autoRefreshRequest.readyState != 4) {
			console.log('Latest request aborted');
			autoRefreshRequest.abort();
		}


		// Get the up-to-date pins
		getPins();


	}, interval);

}


// Stop auto-refresh
function stopAutoRefresh() {

	console.log('AUTO-REFRESH PINS STOPPED');

	if (autoRefreshRequest) autoRefreshRequest.abort();

	clearInterval(autoRefreshTimer);

}



// PINS:
// DB: Get up-to-date pins and changes
function getPins(firstRetrieve, goToPin) {


	firstRetrieve = assignDefault(firstRetrieve, false);
	goToPin = assignDefault(goToPin, null);


	console.log('GETTING PINS...');


	// Record the old pins
	var oldPins = Pins;


	// Send the Ajax request
	autoRefreshRequest = ajax('pins-get', {

		'phase_ID' : phase_ID,
		'device_ID'	  : device_ID

	}).done(function( result ) {


		var data = result.data; console.log('RESPONSE: ', data);


		// If not logged in, refresh this page to go to the login page
		if (data.status == "not-logged-in" || data.status == "no-access") location.reload();


		// Update the global pins list
		Pins = updateOriginals(result.pins, oldPins);


	    // Update incomplete pin count
	    updatePinCount();



		console.log('OLD PINS: ', oldPins);
		console.log('NEW PINS: ', Pins);



		// If different than current pins, do the changes
		if ( !isEqual(Pins, oldPins) ) {


			console.log('There are some updates...');


			// Revert all the changes first
			revertChanges(oldPins);


			// Apply the new Pins, loading overlay will be removed after this task
			applyPins();


		} else {


			console.log('No changes found');
			autoRefreshRequest = null;


		}


		if (firstRetrieve) {


			console.log('FIRST RETRIEVE!', window.location.hash);


			// Hide the loading overlay
			$('#loading').fadeOut();


			// Page is ready now
			page_ready = true;
			$('body').addClass('ready');


			// Detect colors in the page
			detectColors();


			// Get the selected pin to scroll
			if (window.location.hash) {

				var goToPin_ID = parseInt( window.location.hash.replace('#', '') );
				console.log('Going to the Pin #', goToPin_ID);

				scrollToPin(goToPin_ID, true);

			}


		}


		// If goToPin entered
		if (goToPin != null) {


			console.log('Auto opening pin window for Pin #' + goToPin);
			if (!pinWindowOpen) openPinWindow(goToPin, true);

			// If pin not found, show a notice !!!

		}


		// Start auto refresh if not already started
		if (!autoRefreshTimer && processCount == 0) startAutoRefresh();


	});

}


// DB: Put a pin to cordinates
function putPin(element_index, pinX, pinY, cursorType, pinPrivate) {


	// Stop auto-refresh pins
	stopAutoRefresh();



	// PIN LOCATION:
	// Element info
	var selectedElement = iframeElement(element_index);
	var elementOffset = selectedElement.offset();
	var elementTop = elementOffset.top;
	var elementLeft = elementOffset.left;


	// Float value
	pinX = parseFloat(pinX).toFixed(5);
	pinY = parseFloat(pinY).toFixed(5);


	// The coordinates by the element
	elementPinX = parseFloat(pinX - elementLeft).toFixed(5);
	elementPinY = parseFloat(pinY - elementTop).toFixed(5);


	console.log('Left: ' + elementLeft, ' Top: ' + elementTop );
	console.log('PinX: ' + pinX, ' PinY: ' + pinY);
	console.log('TO REGISTER', elementPinX, elementPinY, selectedElement.prop('tagName'));
	//console.log('Put the Pin #' + currentPinNumber, pinX, pinY, cursorType, pinPrivate, element_index);




	// MODIFICATION CHECK:
	// Detect modification info
	var modificationType = null;
	var modificationOriginal = null;

	if (cursorType == "live") {


		// Add edited status to the DOM
		selectedElement.attr('data-revisionary-content-edited', "0");

		modificationType = selectedElement.prop('tagName').toUpperCase() == 'IMG' ? "image" : "html";
		if (modificationType == "html") selectedElement.attr('contenteditable', "true");

		modificationOriginal = modificationType == "html" ? htmlentities( selectedElement.html(), "ENT_QUOTES") : selectedElement.prop('src');


	}



	// CREATE THE PIN:
	var temporaryPinID = makeID();


	// Prepare the new pin info
	var newPinInfo = {
		pin_ID: temporaryPinID,
		pin_complete: 0,
		pin_element_index: parseInt(element_index),
		pin_modification: null,
		pin_modification_original: modificationOriginal,
		pin_modification_type: modificationType,
		pin_css: null,
		pin_private: parseInt(pinPrivate),
		pin_type: cursorType,
		pin_x: elementPinX.toString(),
		pin_y: elementPinY.toString(),
		user_ID: parseInt(user_ID),
		project_ID: parseInt(project_ID),
		page_ID: parseInt(page_ID),
		phase_ID: parseInt(phase_ID),
		device_ID: null
	};


	// Add it to the pins global
	Pins.push(newPinInfo);
	var pinIndex = Pins.indexOf(newPinInfo);


	// Add the temporary pin to the DOM
	$('#pins').append(
		pinTemplate(currentPinNumber, newPinInfo, true)
	);



	// Open the pin window
	openPinWindow(temporaryPinID, true);



	// Start the process
	var newPinProcessID = newProcess(null, "newPinProcess");

	// Add pin to the DB
    ajax('pin-add',
    {
		'pin_x' 	 			 : elementPinX,
		'pin_y' 	 			 : elementPinY,
		'pin_type' 	 			 : cursorType,
		'pin_modification_type' : modificationType == null ? "{%null%}" : modificationType,
		'pin_private'			 : pinPrivate,
		'pin_element_index' 	 : element_index,
		'pin_phase_ID'		 : phase_ID,
		'pin_device_ID'			 : device_ID

	}).done(function(result){


		console.log(result.data);

		var realPinID = result.data.real_pin_ID; //console.log('REAL PIN ID: '+realPinID);
		var newPin = pinElement('[data-pin-id="'+ temporaryPinID +'"]');
		if (openPin != null) openPin = realPinID;


		// Update the pin ID
		if (typeof Pins[pinIndex] !== 'undefined') Pins[pinIndex].pin_ID = realPinID;
		newPin.attr('data-pin-id', realPinID).removeAttr('temporary');


		if (pinWindowOpen) {

			// Remove the loading text on pin window
			pinWindow('[data-pin-id="'+ temporaryPinID +'"]').attr('data-pin-id', realPinID).removeAttr('temporary');
			window.location.hash = "#" + realPinID;
			pinWindow(realPinID).removeClass('loading');

		}


		// Stick the pin
		stickPin(realPinID);


		// Make draggable
		makeDraggable(newPin);


		// Finish the process
		endProcess(newPinProcessID);


	});


	// Increase the pin number
	changePinNumber(parseInt( currentPinNumber ) + 1);


    // Update incomplete pin count
    updatePinCount();

}


// DB: Remove a pin
function removePin(pin_ID) {


	// Bring the pin info
	var pin = getPin(pin_ID);
	if (!pin) return false;
	var pinIndex = Pins.indexOf(pin);


    // Add pin to the DB
    console.log('Remove the pin #' + pin_ID + ' from DB!!');


    // Add removing message
    pinWindow(pin_ID).addClass('removing');


	// Revert the changes
	revertCSS(pin_ID);
	revertChange(pin_ID);


	// Delete from the list
	Pins.splice(pinIndex, 1);


	// Close the pin window
	if ( isPinWindowOpen(pin_ID) ) closePinWindow();


	// Remove the pin from DOM
	pinElement(pin_ID).remove();


	// Remove the notification
	delete Notifications[pin_ID];


	// Re-Index the pin counts
	reindexPins();


    // Update incomplete pin count
    updatePinCount();


	// Unhover
	hoveringPin = false;



	// Start the process
	removePinProcess[pin_ID] = newProcess(null, "removePin"+pin_ID);

    ajax('pin-remove',
    {
		'type'	  	: 'pin-remove',
		'pin_ID'	: pin_ID

	}).done(function(result){

		console.log("PIN REMOVED: ", result.data);


		// Finish the process
		endProcess(removePinProcess[pin_ID]);


	});


}


// DB: Make Device Specific
function makeDeviceSpecific(pin_ID, device_ID) {


	console.log('Make the pin #' + pin_ID + ' stick to device #'+ device_ID +' on DB!!');


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var pinIndex = Pins.indexOf(pin);
	var element_index = pin.pin_element_index;


	// Prevent double clicking
	pinWindow(pin_ID).find('.device-specific').addClass('loading');


	// Update from the Pins global
	Pins[pinIndex].device_ID = parseInt(device_ID);



	// Start the process
	var devicespecificPinProcessID = newProcess(null, "devicespecificPinProcess");


    // Update pin from the DB
	ajax('pin-devicespecific', {

		'pin_ID' 	 : pin_ID,
		'device_ID'	 : device_ID,

	}).done(function(result) {

		console.log("PIN #"+pin_ID+" MADE ONLY FOR DEVICE #" + device_ID, result.data);


		// Update the button
		pinWindow(pin_ID).find('.device-specific').removeClass('loading').addClass('active');


		// Finish the process
		endProcess(devicespecificPinProcessID);

	});

}


// DB: Make pin for all devices
function makeForAllDevices(pin_ID) {


	console.log('Make the pin #' + pin_ID + ' for all devices on DB!!');


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var pinIndex = Pins.indexOf(pin);
	var element_index = pin.pin_element_index;


	// Prevent double clicking
	pinWindow(pin_ID).find('.device-specific').addClass('loading');


	// Update from the Pins global
	Pins[pinIndex].device_ID = null;



	// Start the process
	var deviceForAllPinProcessID = newProcess(null, "deviceForAllPinProcess");


    // Update pin from the DB
	ajax('pin-deviceall', {

		'pin_ID' 	 : pin_ID

	}).done(function(result) {

		console.log("PIN #"+pin_ID+" MADE FOR ALL DEVICES", result.data);


		// Update the button
		pinWindow(pin_ID).find('.device-specific').removeClass('loading').removeClass('active');


		// Finish the process
		endProcess(deviceForAllPinProcessID);

	});

}


// DB: Complete/Incomplete a pin
function completePin(pin_ID, complete, imgData) {


	imgData = assignDefault(imgData, null);



    console.log( (complete ? 'Complete' : 'Incomplete') +' the pin #' + pin_ID + ' on DB!!');



	var pin = getPin(pin_ID);
	var pinIndex = Pins.indexOf(pin);
	var element_index = pin.pin_element_index;


	// Update from the Pins global
	Pins[pinIndex].pin_complete = complete ? 1 : 0;


	// Update the pin & pin window status
	pinElement(pin_ID).attr('data-pin-complete', (complete ? '1' : '0'));
	pinWindow(pin_ID).attr('data-pin-complete', (complete ? '1' : '0'));


	// Mark as completed pin notification
	pinWindow(pin_ID).attr('data-new-notification', (complete ? 'complete' : 'incomplete'));


    // Update incomplete pin count
    updatePinCount();



	// Start the process
	var completePinProcessID = newProcess(null, "pin"+(complete ? 'Complete' : 'Incomplete'));


    // Update pin from the DB
	ajax('pin-complete', {

		'pin_ID' 	   : pin_ID,
		'complete'	   : (complete ? 'complete' : 'incomplete')

	}).done(function(result) {

		console.log("PIN COMPLETED: ", result.data);


		// Finish the process
		endProcess(completePinProcessID);

	});


}


// DB: Convert pin
function convertPin(pin_ID, targetPin) {


	// New values
	var pinType = targetPin.attr('data-pin-type');
	var pinPrivate = targetPin.attr('data-pin-private');
	var pinLabel = targetPin.next().text();
	var element_index = parseInt( pinElement(pin_ID).attr('data-revisionary-index') );


	console.log('Convert PIN #'+ pin_ID +' to: ', pinType, 'Private: ' + pinPrivate);


	// Update from the Pins global
	var pin = getPin(pin_ID);
	var pinIndex = Pins.indexOf(pin);


	// If the new type is standard, reset the modifications
	if (pinType == "standard") {

		revertChange(pin_ID);

		Pins[pinIndex].pin_modification_type = null;
		Pins[pinIndex].pin_modification = null;
		Pins[pinIndex].pin_modification_original = null;

		pinElement(pin_ID).attr('data-pin-modification-type', 'null');
		pinWindow(pin_ID).attr('data-pin-modification-type', 'null');

		// Remove outlines from iframe
		removeOutline();

	}

	Pins[pinIndex].pin_type = pinType;
	Pins[pinIndex].pin_private = parseInt(pinPrivate);


	// Update the pin status
	updateAttributes(pin_ID, 'data-pin-type', pinType);
	updateAttributes(pin_ID, 'data-pin-private', pinPrivate);


	// Update the pin type section label
	pinWindow(pin_ID).find('pin.chosen-pin')
		.attr('data-pin-type', pinType)
		.attr('data-pin-private', pinPrivate);
	pinWindow(pin_ID).find('.pin-label').text(pinLabel);


	// If it's a live pin, change the element outline color
	if (pinType == "live") outline( iframeElement(element_index), pinPrivate );



	// Start the process
	var convertPinProcessID = newProcess(null, "convertPinProcess");


	// Save it on DB
	ajax('pin-convert',
	{
		'pin_ID' 	    : pin_ID,
		'pin_type'		: pinType,
		'pin_private'   : pinPrivate

	}).done(function(result) {

		console.log('PIN CONVERTED: ', result.data);


		// Finish the process
		endProcess(convertPinProcessID);

	});

}


// Scroll to a pin
function scrollToPin(pin_ID, openWindow, noDelay) {


	openWindow = assignDefault(openWindow, false);
	noDelay = assignDefault(noDelay, false);


	console.log('SCROLL TO PIN #' + pin_ID);

	var delay = noDelay ? 0 : 500;


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var element_index = pin.pin_element_index;


	iframeElement('html, body').stop();
	if (pinAnimationTimeout) clearTimeout(pinAnimationTimeout);

	pinAnimationTimeout = setTimeout(function() {


		// Get the locations
		var pinLocation = locationsByElement(element_index, pin.pin_x, pin.pin_y, true);



		console.log('SCROLLING TO PIN #' + pin_ID, pinLocation );



		pinAnimation = iframeElement('html, body').stop().animate({


			scrollTop: parseInt( pinLocation.y / iframeScale ) - ($('.iframe-container').height() / 2) + 22.5
			//scrollLeft: parseInt( pinLocation.x ) - ($('.iframe-container').width() / 2) + 22.5


		}, delay, 'swing').promise().then(function() {


			if (openWindow && !pinWindowOpen) openPinWindow(pin_ID);


		});


	}, delay);


	return true;

}


// Incomplete Pin counts
function updatePinCount() {


	var incompletePins = Pins.filter(function(pin) {
		return pin.pin_complete == 0;
	});

	var completePins = Pins.filter(function(pin) {
		return pin.pin_complete == 1;
	});


	if (incompletePins.length > 0) $('.pins .button .notif-no').removeClass('hide').removeClass('green').text(incompletePins.length);
	else if (completePins.length > 0 && incompletePins.length == 0) $('.pins .button .notif-no').removeClass('hide').addClass('green').text(completePins.length);
	else $('.pins .button .notif-no').addClass('hide');

	return incompletePins.length;

}


// Re-Index Pins
function reindexPins() {


    $('#pins > pin').each(function(i) {

	    var pin = $(this);

		pin.text(i+1);

    });


    // Update the current pin number on cursor
    changePinNumber(currentPinNumber - 1);


}


// Get pin number
function getPinNumber(pin_ID) {

	var pin = getPin(pin_ID);
	var pinIndex = Pins.indexOf(pin);

	return pinIndex + 1;

}


// Change the pin number on cursor
function changePinNumber(pinNumber) {

	cursor.text(pinNumber);
	currentPinNumber = pinNumber;

}


// Relocate a pin
function relocatePin(pin_ID, x, y) {


	// Pin info
	var pin = getPin(pin_ID);
	if (!pin) return false;
	var pinIndex = Pins.indexOf(pin);

	var element_index = pin.pin_element_index;
	var element = iframeElement(element_index);


	// Use the registered locations if not specified
	x = assignDefault(x, pin.pin_x);
	y = assignDefault(y, pin.pin_y);


	// Get the locations
	var pinLocation = locationsByElement(pin.pin_element_index, x, y);


	// If not on the screen
	if ( !element.is(':visible') ) {


		//console.log('Element invisible');

		// Make the pin smaller and undraggable
		pinElement(pin_ID).addClass('hidden');
		disableDraggable( pinElement(pin_ID) );


	} else if ( pinElement(pin_ID).hasClass('hidden') ) {

		pinElement(pin_ID).removeClass('hidden');
		enableDraggable( pinElement(pin_ID) );

	}


	// Do not relocate if hovering that pin
	if ( pinElement(pin_ID).is(':hover') && !pinDragging && !scrollOnPin ) return false;



	//console.log('RELOCATING PIN #' + pin_ID, pinLocation );



	// Update the global 'pins'
	Pins[pinIndex].pin_x = x;
	Pins[pinIndex].pin_y = y;


	pinElement(pin_ID)
		.attr('data-pin-x', x)
		.attr('data-pin-y', y)
		.css('left', pinLocation.x)
		.css('top', pinLocation.y);



	if (pinWindowOpen) relocatePinWindow(pin_ID, pinLocation);

}


// Relocate the pin window
function relocatePinWindow(pin_ID, pinLocation) {


	pin_ID = assignDefault(pin_ID, parseInt(pinWindow().attr('data-pin-id')));
	pinLocation = assignDefault(pinLocation, {
		x: parseInt( pinElement('[data-pin-id="'+ pin_ID +'"]').css('left') ),
		y: parseInt( pinElement('[data-pin-id="'+ pin_ID +'"]').css('top') )
	});
	//var pinLocation = locationsByElement(pin.pin_element_index, pin.pin_x, pin.pin_y);


	// Pin info
	var pin = getPin(pin_ID);
	if (!pin) return false;


	// Check current pin window ID
	if ( pinWindow().attr('data-pin-id') != pin_ID ) return false;


	// Update the location and size values
	updateLocationValues();



	//console.log('RELOCATING PIN WINDOW #' + pin_ID, pinLocation );



	// Pin window location
    var scrolled_window_x = offset.left + pinLocation.x + 50;
    var scrolled_window_y = offset.top + pinLocation.y + 50;


	var spaceWidth = offset.left + iframeWidth + offset.left - 15;
	var spaceHeight = offset.top + iframeHeight + offset.top - 15 - $('#top-bar').height();


    var new_scrolled_window_x = scrolled_window_x < spaceWidth - pinWindowWidth ? scrolled_window_x : spaceWidth - pinWindowWidth;
    var new_scrolled_window_y = scrolled_window_y < spaceHeight - pinWindowHeight ? scrolled_window_y : spaceHeight - pinWindowHeight;


	// X: Change the side of the window
	if (
		scrolled_window_x >= spaceWidth - pinWindowWidth &&
		scrolled_window_y >= spaceHeight - pinWindowHeight
	) {

		//console.log('OUCH!');
		new_scrolled_window_x = scrolled_window_x - pinWindowWidth - 55;


		// Make it stay in the viewport
		if (new_scrolled_window_x + pinWindowWidth > spaceWidth) new_scrolled_window_x = spaceWidth - pinWindowWidth;

	}

	if (new_scrolled_window_x < 10) new_scrolled_window_x = 10;



	// Y: Make the pin window stay after scrolling up
	if (scrolled_window_y > new_scrolled_window_y + pinWindowHeight) {

		//console.log('GOODBYE!');
		new_scrolled_window_y = scrolled_window_y - pinWindowHeight;

	}




	//console.log('SPACE #' + new_scrolled_window_x, new_scrolled_window_y );
	//console.log('PIN WINDOW RELOCATING #' + pin_ID, pinLocation, new_scrolled_window_x, new_scrolled_window_y );



	// Do not relocate if hovering that pin
	//if ( pinElement(pin_ID).is(':hover') && !pinDragging && !scrollOnPin ) return false;




	// Relocate the pin window
	pinWindow('[data-pin-id="'+ pin_ID +'"]').not('.moved')
		.attr('data-pin-x', pin.pin_x)
		.attr('data-pin-y', pin.pin_y)
		.css('left', new_scrolled_window_x + "px")
		.css('top', new_scrolled_window_y + "px");

}


// Get real pin location
function locationsByElement(element_index, pin_x, pin_y, noScroll) {


	noScroll = assignDefault(noScroll, false);


	var element = iframeElement(element_index);
	if (!element.length) return false;


	// Update the location and size values
	updateLocationValues();


	var elementOffset = getElementOffset(element_index);
	if (!elementOffset) return false;

	var elementTop = elementOffset.top;
	var elementLeft = elementOffset.left;
	var elementWidth = element.width();
	var elementHeight = element.height();


	// Detect the X positive exceed
	if ( elementLeft + parseFloat(pin_x) > iframeWidth / iframeScale) pin_x = elementWidth;


	// Detect the X negative exceed
	if ( elementLeft + parseFloat(pin_x) < 0 ) pin_x = 0;


	// The coordinates by the element
	var elementPinX = elementLeft + parseFloat(pin_x);
	var elementPinY = elementTop + parseFloat(pin_y);


	// With the iframe scale
	elementPinX = parseFloat(elementPinX) * iframeScale;
	elementPinY = parseFloat(elementPinY) * iframeScale;


	// Middle of the pin
	elementPinX = elementPinX - (45 / 2);
	elementPinY = elementPinY - (45 / 2);


	// Scroll
	if (!noScroll) {

		elementPinX = elementPinX - scrollX;
		elementPinY = elementPinY - scrollY;

	}


	return {
		x : elementPinX,
		y : elementPinY
	};

}


// Get element offset
function getElementOffset(element_index) {


	var selectedElement = iframeElement(element_index);
	if (!selectedElement.length) return false;


	var pin = getPin(element_index, true);
	if (!pin) return false;
	var pin_ID = pin.pin_ID;



	//console.log('ELEMENT OFFSET: ', selectedElement.offset() );
	//console.log('VISIBILITY: ', selectedElement.is(':visible') );



	// Check if hidden
	if ( selectedElement.css('display') == 'none' ) {


		// Check the cache first
		//if ( hiddenElementOffsets[element_index] !== undefined ) console.log('0. Element Offset for element #' + element_index, hiddenElementOffsets[element_index]);
		if ( hiddenElementOffsets[element_index] !== undefined ) return hiddenElementOffsets[element_index];


		// Disabled temporarily
		disableCSS(pin_ID);
		selectedElement.addClass('revisionary-show');

		hiddenElementOffsets[element_index] = selectedElement.offset();

		selectedElement.removeClass('revisionary-show');
		activateCSS(pin_ID);


		//console.log('1. Element Offset for element #' + element_index, hiddenElementOffsets[element_index]);
		return hiddenElementOffsets[element_index];

	}


	// If not on the screen
	else if ( !selectedElement.is(':visible') ) {

		// console.log('Element invisible');

		// Temporary location
		var parentElement = selectedElement.parents(':visible');
		var parentOffset = parentElement.offset();
		parentOffset.top = parentOffset.top + parentElement.height() - 25;

		return parentOffset;

	}

	else if ( pinElement(pin_ID).hasClass('hidden') ) {

		// pinElement(pin_ID).removeClass('hidden');
		// enableDraggable( pinElement(pin_ID) );

	}


	//console.log('2. Element Offset for element #' + element_index, selectedElement.offset());
	return selectedElement.offset();

}


// Re-Locate Pins
function relocatePins() {


	// Relocate all the pins
    $('#pins > pin:visible').each(function() {

	    var pin_ID = parseInt($(this).attr('data-pin-id'));
	    relocatePin(pin_ID);

    });


}


// Activate Pins Drag
function makeDraggable(pin) {


	pin = assignDefault(pin, $('#pins > pin:not([temporary]):not(.hidden)'));


	// Make pins draggable
	pin.draggable({
		containment: ".iframe-container",
		iframeFix: true,
		scroll: false,
		snap: false,
		snapMode: "outer",
		snapTolerance: 10,
		stack: "#pins > pin",
		cursor: "move",
		opacity: 0.35,
		start: function( event, ui ) {


			//console.log('STARTED!');


		},
		drag: function( event, ui ) {


			// Stop auto refresh
			stopAutoRefresh();



			//console.log('PIN: ', ui.position.left, ui.position.top);



			// Get the pin ID
			var pin_ID = parseInt($(this).attr('data-pin-id'));



			// PIN LOCATION:
			// Element info
			var element_index = parseInt($(this).attr('data-revisionary-index'));


			var elementOffset = getElementOffset(element_index);
			var elementTop = elementOffset.top;
			var elementLeft = elementOffset.left;


			// Get the final positions
			var pinX = (ui.position.left + scrollX + 45 / 2 ) / iframeScale;
			var pinY = (ui.position.top + scrollY + 45 / 2 ) / iframeScale;


			// Float value
			pinX = parseFloat(pinX).toFixed(5);
			pinY = parseFloat(pinY).toFixed(5);


			// The coordinates by the element
			elementPinX = parseFloat(pinX - elementLeft).toFixed(5);
			elementPinY = parseFloat(pinY - elementTop).toFixed(5);



			// console.log('Left: ' + elementLeft, ' Top: ' + elementTop );
			// console.log('PinX: ' + pinX, ' PinY: ' + pinY);
			// console.log('TO REGISTER', elementPinX, elementPinY);



			// Position difference check
			var leftDest = Math.abs(ui.originalPosition.left - ui.position.left);
			var topDest = Math.abs(ui.originalPosition.top - ui.position.top);

			if (leftDest + topDest > 4) {
				pinDragging = true;

				//console.log('DRAGGING!!!');

			}


			// If pin window open, attach it to the pin
			relocatePin(pin_ID, elementPinX, elementPinY);


		},
		stop: function( event, ui ) {


			//console.log('STOPPED.');


			var pinWasDragging = pinDragging;
			pinDragging = false;


			// Get the pin
			focusedPin = $(this);
			var pin_ID = focusedPin.attr('data-pin-id');



			// Get the updated positions
			var pinX = parseFloat(focusedPin.attr('data-pin-x')).toFixed(5);
			var pinY = parseFloat(focusedPin.attr('data-pin-y')).toFixed(5);


			console.log('RELOCATING: ', pinX, pinY);


		    // Update the pin location on DB
		    //console.log('Update the new pin location on DB', pinX, pinY);


			// Start the process
			var relocateProcessID = newProcess(null, "pinRelocate");

		    $.post(ajax_url, {
				'type'	  	 : 'pin-relocate',
				'pin_ID'	 : pin_ID,
				'pin_x' 	 : pinX,
				'pin_y' 	 : pinY
			}, function(result){


				// Update the global 'pins'
				var pin = getPin(pin_ID);
				var pinIndex = Pins.indexOf(pin);

				Pins[pinIndex].pin_x = pinX;
				Pins[pinIndex].pin_y = pinY;



				// Finish the process
				endProcess(relocateProcessID);

				//console.log(result.data);

			}, 'json');


		}
	});


}


// Disable pin dragging
function disableDraggable(pin) {

	if (!pin.length) return false;
	pin.draggable( "option", "disabled", true );

}


// Enable pin dragging
function enableDraggable(pin) {

	if (!pin.length) return false;
	pin.draggable( "option", "disabled", false );

}


// Stick pin to element
function stickPin(pin_ID) {

	var pin = getPin(pin_ID);
	var element_index = pin.pin_element_index;


	iframeElement(element_index).onPositionChanged(function() {

		relocatePin(pin_ID);

	});


}



// PIN WINDOW:
// Open the pin window
function openPinWindow(pin_ID, firstTime, scrollToPin) {


	// Defaults
	firstTime = assignDefault(firstTime, false);
	scrollToPin = assignDefault(scrollToPin, false);


	console.log('OPEN WINDOW PIN #' + pin_ID, firstTime);


	try {


		var pin = getPin(pin_ID);
		if (!pin) return false;
		//var pinIndex = Pins.indexOf(pin);


		var thePin = pinElement('[data-pin-id="'+pin_ID+'"]');
		var thePinType = thePin.attr('data-pin-type');
		var thePinPrivate = thePin.attr('data-pin-private');
		var thePinComplete = thePin.attr('data-pin-complete');
		var theIndex = thePin.attr('data-revisionary-index');
		var theElement = iframeElement(theIndex);
		var thePinText = thePinPrivate == '1' ? 'PRIVATE VIEW' : 'ONLY VIEW';
		var thePinModificationType = thePin.attr('data-pin-modification-type');
		var thePinModified = thePin.attr('data-revisionary-content-edited');
		var thePinShowingContentChanges = thePin.attr('data-revisionary-showing-content-changes');
		var thePinShowingStyleChanges = thePin.attr('data-revisionary-showing-style-changes');
		var thePinMine = parseInt(pin.user_ID) == parseInt(user_ID);


		// Previous state of window
		pinWindowWasOpen = pinWindowOpen;


		// Previous state of cursor
		if (!pinWindowWasOpen) cursorWasActive = cursorActive;


		// Close the previous window
		if (pinWindowOpen) closePinWindow();


		// Disable the iframe
		//$('#the-page').css('pointer-events', 'none');


		// Disable the inspector
		toggleCursorActive(true); // Force deactivate


		// Add the pin window data !!!
		pinWindow()
			.attr('data-pin-type', thePinType)
			.attr('data-pin-private', thePinPrivate)
			.attr('data-pin-complete', thePinComplete)
			.attr('data-pin-x', thePin.attr('data-pin-x'))
			.attr('data-pin-y', thePin.attr('data-pin-y'))
			.attr('data-pin-modification-type', thePinModificationType)
			.attr('data-pin-id', pin_ID)
			.attr('data-revisionary-content-edited', thePinModified)
			.attr('data-revisionary-showing-content-changes', thePinShowingContentChanges)
			.attr('data-revisionary-showing-style-changes', thePinShowingStyleChanges)
			.attr('data-revisionary-index', theIndex)
			.attr('data-pin-mine', (thePinMine ? "yes" : "no"))
			.attr('data-pin-new', (firstTime ? "yes" : "no"))
			.attr('data-new-notification', "no")
			.attr('data-has-comments', 'no');


		// Reset the differences fields
		pinWindow().removeClass('show-differences');
		pinWindow().find('span.diff-text').text('SHOW DIFFERENCE');
		pinWindow().find('.difference-switch > i').removeClass('fa-random', 'fa-pencil-alt').addClass('fa-random');


		// Update the pin type section
		pinWindow().find('pin.chosen-pin')
			.attr('data-pin-type', thePinType)
			.attr('data-pin-private', thePinPrivate);
		pinWindow().find('.pin-label').text(thePinText);


		// Device specific pin
		pinWindow().find('.device-specific').removeClass('loading').removeClass('active');
		if ( pin.device_ID != null ) pinWindow().find('.device-specific').addClass('active');



		// CSS OPTIONS:
		var styleElement = iframeElement('style[data-pin-id="'+ pin_ID +'"]');
		var pinCSS = styleElement.text();
		var isShowingCSS = styleElement.is('[media]') ? false : true;
		var options = pinWindow().find('ul.options');



		// Update the current element section
		$('.element-tag, .element-id, .element-class').text('');

		// Tag Name
		var tagName = theElement.prop('tagName').toUpperCase();
		$('.element-tag').text(tagName);

		// Classes
		var classes = "";
		var classList = theElement.attr('class');
		if (classList != null) {

			$.each(classList.split(/\s+/), function(index, className) {

				classes = classes + "."+className;

			});
			$('.element-class').text(classes);

		}

		// ID Name
		var idName = theElement.attr("id");
		if (idName != null) $('.element-id').text('#'+idName);


		// Changed status
		pinWindow().attr('data-revisionary-style-changed', (styleElement.length ? "yes" : "no"));


		// Remove changed marks
		options.find('.main-option').removeClass('changed');
		options.find('[data-edit-css][data-revisionary-style-changed]').removeAttr('data-revisionary-style-changed');


		// Temporarily activate the registered changes
		if (!isShowingCSS && pin.pin_css != null) activateCSS(pin_ID);


		// Update the CSS properties
		var properties = options.find('[data-edit-css]');
		$(properties).each(function(i, propertyElement) {


			// Get the property name and values
			var property = $(propertyElement).attr('data-edit-css');
			var value = theElement.css(property);

			if (typeof value === 'undefined') return true;


			// Display exception
			if (property == "display" && value != "none") value = 'block';


			// Color exception
			if ( property.includes("color") ) {
				$('input[type="color"][data-edit-css="'+ property +'"]').spectrum("set", value);
			}


			// Background Image
			if (property == "background-image")
				value = value.replace('url(','').replace(')','').replace(/\"/gi, "");


			// Update the main options
			options.attr('data-'+property, value);


			// Update the choices
			options.find('a[data-edit-css="'+ property +'"]').removeClass('active');
			options.find('a[data-edit-css="'+ property +'"][data-value="'+ value +'"]').addClass('active');


			// Inputs
			options.find('input[data-edit-css="'+ property +'"]').val(value).trigger('change');


			// Check for the changed status
			var editedSign = '<i class="fa fa-circle edited-sign particular"></i>';
			if ( pinCSS.includes(property + ":" + value) || pinCSS.includes(property + ":url(" + value + ")") )
				$(propertyElement).attr('data-revisionary-style-changed', 'yes').parents('.main-option').addClass('changed');

		});


		// Temporarily activate the registered changes
		if (!isShowingCSS && pin.pin_css != null) disableCSS(pin_ID);

		
		// Open tabs
		if (!firstTime) {

			// Close all first
			pinWindow().find('.section-title').addClass('collapsed');

			// Content
			if ( thePinModified == "1" ) pinWindow().find('.content-editor .section-title, .image-editor .section-title').removeClass('collapsed');

			// Styles
			if (styleElement.length) pinWindow().find('.visual-editor .section-title').removeClass('collapsed');

			// Always open the comments
			pinWindow().find('.comments .section-title').removeClass('collapsed');

		}



		// If on 'Live' mode
		if (thePinType == 'live') {


			// Update the pin type section
			thePinText = thePinPrivate == '1' ? 'PRIVATE LIVE' : 'LIVE EDIT';
			pinWindow().find('.pin-label').text(thePinText);


			// TEXT
			if ( thePinModificationType == "html" ) {

				var originalContent = "";
				var changedContent = "";


				// If it's untouched DOM
				if ( iframeElement(theIndex).is(':not([data-revisionary-showing-content-changes])') ) {

					originalContent = iframeElement(theIndex).html();


					// If edited element is a submit or reset input button
					if (
			        	iframeElement(theIndex).prop('tagName').toUpperCase() == "INPUT" &&
			        	(
			        		iframeElement(theIndex).attr("type") == "text" ||
			        		iframeElement(theIndex).attr("type") == "email" ||
			        		iframeElement(theIndex).attr("type") == "url" ||
			        		iframeElement(theIndex).attr("type") == "tel" ||
			        		iframeElement(theIndex).attr("type") == "submit" ||
			        		iframeElement(theIndex).attr("type") == "reset"
			        	)
			        ) {
						originalContent = iframeElement(theIndex).val();
					}


					// Default change editor
					changedContent = originalContent;
				}


				// Add the original HTML content to the window
				else if (pin && pin.pin_modification_original != null)
					originalContent = html_entity_decode(pin.pin_modification_original);


				// Get the changed content
				if (pin && pin.pin_modification != null)
					changedContent = html_entity_decode(pin.pin_modification);


				// Add the original HTML content
				pinWindow().find('.content-editor .edit-content.original').html( originalContent );


				// Add the changed HTML content
				pinWindow().find('.content-editor .edit-content.changes').html( changedContent );


			}


			// IMAGE
			if ( thePinModificationType == "image" && theElement.prop('tagName').toUpperCase() == "IMG" ) {

				var originalImageSrc = "";
				var changedImageSrc = "";


				// If it's untouched DOM
				if ( iframeElement(theIndex).is(':not([data-revisionary-showing-content-changes])') ) {

					originalImageSrc = iframeElement(theIndex).prop('src');

					// Default image preview
					changedImageSrc = originalImageSrc;
				}


				// Add the original image URL
				else if (pin && pin.pin_modification_original != null)
					originalImageSrc = pin.pin_modification_original;


				// Get the new image
				if (pin && pin.pin_modification != null)
					changedImageSrc = pin.pin_modification;



				// Add the original image
				pinWindow().find('.image-editor .edit-content.original img.original-image').attr('src', originalImageSrc);


				// Add the new image
				pinWindow().find('.image-editor .edit-content.changes img.new-image').attr('src', changedImageSrc);
				pinWindow().find('.image-editor a.image-url').attr('href', changedImageSrc);

			}



		}


		// Live pin error check
		if (
			(thePinModificationType == "image" && theElement.prop('tagName').toUpperCase() != "IMG") ||
			(thePinModificationType == "html" && theElement.prop('tagName').toUpperCase() == "IMG")
		) {

			// Act like a standard pin (Hide content changers)
			pinWindow().attr('data-pin-type', 'standard');

		}


		// Add the temporary attribute at the first time adding pin
		pinWindow().removeAttr('temporary');
		if (firstTime) pinWindow().attr('temporary', '');


		// Remove the removing text
		pinWindow().removeClass('removing');


		// Relocate the window
		relocatePinWindow(pin_ID);


		// Reveal it
		pinWindow().addClass('active');
		pinWindowOpen = true;
		window.location.hash = "#" + pin_ID;


		// If the new pin registered, remove the loading message
		if ( $.isNumeric(pin_ID) )
			pinWindow().removeClass('loading');



		// COMMENTS
		// If new pin added
		if (firstTime)
			$('.pin-comments').html('<div class="xl-center">Add your comment:</div>'); // Write a message

		// If this is an already registered pin
		else
			getComments(pin_ID); // Bring the comments


		// Clean the existing comment in the input
		$('#pin-window .comment-input').val('');



		// Show the pin
		$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');


	} catch (e) {

		console.log('PIN WINDOW OPENING ERROR: ', e);

	}

}


// Close pin window
function closePinWindow(removePinIfEmpty) {


	removePinIfEmpty = assignDefault(removePinIfEmpty, true);


	// Don't close the pin window if it's currently adding a
	if ( pinWindow().hasClass('loading') ) return false;


	var pin_ID = pinWindow().attr('data-pin-id');


	if (pinWindowOpen) console.log('CLOSE PIN WINDOW #' + pin_ID);


	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;

	// Hide it
	pinWindow().removeClass('active');
	pinWindowOpen = false;
	history.pushState("", document.title, window.location.pathname + window.location.search);


	// Close the Popline
	$('.popline').fadeOut();


	// Close the colorpicker
	$("input[type='color']").spectrum("hide");


	// Abort the latest request if not finalized
	if(commentsGetRequest && commentsGetRequest.readyState != 4) {
		console.log('Latest comments request aborted');
		commentsGetRequest.abort();
	}


	// Add the loading text after loading
	pinWindow().addClass('loading');


	// Remove the moved class
	pinWindow().removeClass('moved');


	// Reset the pin opacity
	$('#pins > pin').css('opacity', '');


	// Delete if no change made
	var pinRemoved = false;

	// Removed by clicking "Remove" button
	if ( pinWindow(pin_ID).hasClass('removing') ) pinRemoved = true;

	if (
		removePinIfEmpty &&
		!pinRemoved &&
		pinWindow(pin_ID).find('.comment-input').val() == "" &&
		pinWindow(pin_ID).attr('data-pin-new') == "yes" &&
		pinWindow(pin_ID).attr('data-revisionary-content-edited') == "0" &&
		pinWindow(pin_ID).attr('data-revisionary-style-changed') == "no" &&
		pinWindow(pin_ID).attr('data-has-comments') == "no" &&
		pinWindow(pin_ID).attr('temporary') != ""
	) {

		console.log('REMOVE THIS PIN', pin_ID);

		// Remove the pin
		removePin(pin_ID);
		pinRemoved = true;

	}


	// Notify the users if this was a new pin
	if ( pinWindow(pin_ID).attr('data-pin-new') == "yes" && !pinRemoved ) {

		newPinNotification(pin_ID);

	}


	// Notify the users if new comment added
	else if ( pinWindow(pin_ID).attr('data-new-notification') == "comment") {

		newCommentNotification(pin_ID);

	}


	// Notify the users if this was completed
	else if ( pinWindow(pin_ID).attr('data-new-notification') == "complete") {

		completeNotification(pin_ID);

	}


	// Notify the users if this was incompleted
	else if ( pinWindow(pin_ID).attr('data-new-notification') == "incomplete") {

		inCompleteNotification(pin_ID);

	}


	// Delete the before image
	delete Notifications[pin_ID];


	if (cursorWasActive && !hoveringPin) toggleCursorActive(false, true); // Force Open


	// Enable the iframe
	//$('#the-page').css('pointer-events', '');


	// Relocate pin after closing the pin window
	relocatePin(pin_ID);


}


// Toggle pin window
function togglePinWindow(pin_ID) {

	if ( isPinWindowOpen(pin_ID) ) closePinWindow();
	else openPinWindow(pin_ID);

}



// MODIFICATIONS:
// Update originals
function updateOriginals(pinsList, oldPinsList) {


	pinsList = assignDefault(pinsList, []);


	$(pinsList).each(function(i, pin) {


		// Skip standard and unmodified pins
		if ( pin.pin_type != "live" || pin.pin_modification_original != null ) return true;


		var theOriginal = null;
		var pin_ID = pin.pin_ID;
		var oldPin = oldPinsList.find(function(p) { return p.pin_ID == pin_ID ? true : false; });
		var element = iframeElement(pin.pin_element_index);


		// Check from the existing Pins global
		if (oldPin && oldPin.pin_modification_original != null)
			theOriginal = oldPin.pin_modification_original;


		// Check if it's an untouched dom
		else if ( element.is(':not([data-revisionary-showing-content-changes="1"])') ) {

			if (pin.pin_modification_type == "html") {

				theOriginal = htmlentities(element.html(), "ENT_QUOTES");


				// If edited element is a submit or reset input button
				if (
		        	element.prop('tagName').toUpperCase() == "INPUT" &&
		        	(
		        		element.attr("type") == "text" ||
		        		element.attr("type") == "email" ||
		        		element.attr("type") == "url" ||
		        		element.attr("type") == "tel" ||
		        		element.attr("type") == "submit" ||
		        		element.attr("type") == "reset"
		        	)
		        ) {
					theOriginal = htmlentities( element.val(), "ENT_QUOTES" );
				}


			} else if (pin.pin_modification_type == "image") {

				theOriginal = element.prop('src');

			}

		}


		pinsList[i].pin_modification_original = theOriginal;

	});


	return pinsList;

}


// Revert changes
function revertChanges(pinsList) {


	console.log('REVERTING ALL THE CHANGES', pinsList);


	$(pinsList).each(function(i, pin) {

		var pin_ID = pin.pin_ID;
		var changedElement = iframeElement(pin.pin_element_index);


		// Revert the style changes
		if ( pin.pin_css != null ) {

			var isShowingOriginalStyles = changedElement.is('[data-revisionary-showing-style-changes="no"]');
			revertCSS(pin_ID);
			if (isShowingOriginalStyles) changedElement.attr('temp-revisionary-showing-style-changes', "no");

		}


		// Revert the content changes, skip standard and unmodified pins
		if ( pin.pin_modification != null ) {

			var isShowingOriginalContent = changedElement.is('[data-revisionary-showing-content-changes="0"]');
			
			revertChange(pin_ID);

			if (isShowingOriginalContent) changedElement.attr('temp-revisionary-showing-content-changes', "0");
		
		}


	});


}


// Apply the pins
function applyPins() {


	console.log('APPLYING PINS...');


	// Update the location and size values
	updateLocationValues();


	// Empty the pins
	$('#pins').html('');


	// Add the pins again
	$(Pins).each(function(i, pin) {

		var pin_number = i + 1;


		// Add the pin to the list
		$('#pins').append(
			pinTemplate(pin_number, pin)
		);


		// Stick pin to element
		stickPin(pin.pin_ID);

	});


	// Update the cursor number with the existing pins
	currentPinNumber = $('#pins > pin').length + 1;
	changePinNumber(currentPinNumber);


	// Make pins draggable
	makeDraggable();


	// Update the pins list tab
	updatePinsList();


	// Apply changes
	applyChanges();

}


// Apply changes
function applyChanges() {


	$(Pins).each(function(i, pin) {


		// Find the element
		var pin_ID = pin.pin_ID;

		var element_index = pin.pin_element_index;
		var changedElement = iframeElement(element_index);


		console.log('APPLYING PIN: ', i, pin);


		// CSS CODES:
		if ( pin.pin_css != null ) updateCSS(pin_ID);


		// MODIFICATIONS:
		if ( pin.pin_modification != null ) updateChange(pin_ID);


	});


	// Remove temporary attributes
	iframeElement('[temp-revisionary-showing-content-changes]').removeAttr('temp-revisionary-showing-content-changes');
	iframeElement('[temp-revisionary-showing-style-changes]').removeAttr('temp-revisionary-showing-style-changes');


	console.log('CHANGES APPLIED');


	autoRefreshRequest = null;


	// Hide the loading overlay
	$('#loading').fadeOut();


	// Re-Locate all the pins
	relocatePins();


	// Page is ready now
	page_ready = true;
	$('body').addClass('ready');


}


// DB: Save a modification
function saveChange(pin_ID, modification) {


    // Add pin to the DB
    //console.log( 'Save modification for the pin #' + pin_ID + ' on DB!!');


    // Update from the Pins global
	var pin = getPin(pin_ID);
	var pinIndex = Pins.indexOf(pin);
	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(element_index);
	var change = modification == "{%null%}" ? null : htmlentities(modification, "ENT_QUOTES");


	//console.log('ORIGINAL:', Pins[pinIndex].pin_modification_original);
	//console.log('CHANGE:', change);


	// Register the change only if different than the original
	if (Pins[pinIndex].pin_modification_original == change) {

		//console.log('NO CHANGE');

		change = null;
		modification = "{%null%}";

	}


	// Start the process
	var modifyPinProcessID = newProcess(null, "modifyPinProcess");

	// Update from DB
    ajax('pin-modify', {

		'modification' 	 	: modification,
		'pin_ID'			: pin_ID

	}).done(function(result){


		var data = result.data; console.log(data);
		var filtered_modification = data.modification;


		// Update the global
		Pins[pinIndex].pin_modification = filtered_modification; //console.log('FILTERED: ', filtered_modification);


		// Update the status
		updateChange(pin_ID, modification);


		// Finish the process
		endProcess(modifyPinProcessID);

	});


}


// Update a modification
function updateChange(pin_ID, modification) {


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(element_index);

	modification = assignDefault(modification, pin.pin_modification);



	if (modification == null || modification == "{%null%}") {

		revertChange(pin_ID);

	} else {

		var isShowingOriginalContent = pinElement(pin_ID).is('[data-revisionary-showing-content-changes="0"]') || changedElement.is('[temp-revisionary-showing-content-changes="0"]');


		// Apply the change, if it was showing changes
		if (!isShowingOriginalContent) {


			// If the type is HTML content change
			if ( pin.pin_modification_type == "html" ) {


				//console.log('MODIFICATION ORIG:', pin.pin_modification);


				// Apply the change
				var newHTML = html_entity_decode(modification);
				changedElement.html( newHTML ) ;


				// If edited element is a submit or reset input button
				if (
					changedElement.prop('tagName').toUpperCase() == "INPUT" &&
					(
						changedElement.attr("type") == "text" ||
						changedElement.attr("type") == "email" ||
						changedElement.attr("type") == "url" ||
						changedElement.attr("type") == "tel" ||
						changedElement.attr("type") == "submit" ||
						changedElement.attr("type") == "reset"
					)
				) {
					changedElement.val(newHTML);
				}


				//console.log('MODIFICATION DECODED:', newHTML);


			// If the type is image change
			} else if ( pin.pin_modification_type == "image" ) {


				// Apply the change
				var newSrc = modification; //console.log('NEW', newHTML);
				changedElement.attr('src', newSrc).removeAttr('srcset');


			}

		}


		// Add the contenteditable attribute to the live elements
		if (pin.pin_modification_type == "html")
			changedElement.attr('contenteditable', isShowingOriginalContent ? "false" : "true");


		// Update info
		updateAttributes(pin_ID, 'data-revisionary-content-edited', "1");
		updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', isShowingOriginalContent ? "0" : "1");


	}


}


// Revert single pin changes
function revertChange(pin_ID) {


	console.log('REVERTING CONTENT FOR PIN: ', pin_ID);


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(element_index);

	//var isShowingOriginalContent = 



	// If the type is HTML content change
	if ( pin.pin_modification_type == "html" ) {


		// Revert the change on DOM
		var oldHTML = html_entity_decode(pin.pin_modification_original); //console.log('NEW', newHTML);
		changedElement.html( oldHTML );


		// If edited element is a submit or reset input button
		if (
			changedElement.prop('tagName').toUpperCase() == "INPUT" &&
			(
				changedElement.attr("type") == "text" ||
				changedElement.attr("type") == "email" ||
				changedElement.attr("type") == "url" ||
				changedElement.attr("type") == "tel" ||
				changedElement.attr("type") == "submit" ||
				changedElement.attr("type") == "reset"
			)
		) {
			changedElement.val(oldHTML);
		}


		// Add the original HTML content
		pinWindow(pin_ID).find('.content-editor .edit-content.changes').html( oldHTML );


	// If the type is image change
	} else if ( pin.pin_modification_type == "image" ) {


		// Revert the change on DOM
		var oldSrc = pin.pin_modification_original; //console.log('NEW', newHTML);
		changedElement.attr('src', oldSrc);


		// Add the original HTML content
		pinWindow(pin_ID).find('.image-editor .edit-content.changes .new-image').attr('src', oldSrc);


	}


	// Update the element, pin and pin window status
	updateAttributes(pin_ID, 'data-revisionary-content-edited', "0");
	updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', "1");
	changedElement
		.removeAttr('data-revisionary-content-edited')
		.removeAttr('data-revisionary-showing-content-changes')
		.removeAttr('contenteditable');


}


// DB: Reset Content changes
function resetContent(pin_ID) {


    console.log( 'Reset content changes for the pin #' + pin_ID + ' on DB!!');


	// Revert the changes
	revertChange(pin_ID);


	// Delete the changes
    saveChange(pin_ID, "{%null%}");

}


// Toggle content edits
function toggleChange(pin_ID) {


    // Get the pin from the Pins global
	var pin = getPin(pin_ID);
	if (!pin) return false;


	// Check if this is currently showing the changed content
	var isShowingChanges = pinElement(pin_ID).attr('data-revisionary-showing-content-changes') == "1" ? true : false;



	if (pin.pin_modification_type == "html") {


		// If edited element is a submit or reset input button
		if (
			iframeElement(pin.pin_element_index).prop('tagName').toUpperCase() == "INPUT" &&
			(
				iframeElement(pin.pin_element_index).attr("type") == "text" ||
				iframeElement(pin.pin_element_index).attr("type") == "email" ||
				iframeElement(pin.pin_element_index).attr("type") == "url" ||
				iframeElement(pin.pin_element_index).attr("type") == "tel" ||
				iframeElement(pin.pin_element_index).attr("type") == "submit" ||
				iframeElement(pin.pin_element_index).attr("type") == "reset"
			)
		) {
			iframeElement(pin.pin_element_index).val( html_entity_decode( (isShowingChanges ? pin.pin_modification_original : pin.pin_modification) ) );
		}


		// Change the content on DOM
		iframeElement(pin.pin_element_index)
			.html( html_entity_decode( (isShowingChanges ? pin.pin_modification_original : pin.pin_modification) ) )
			.attr('contenteditable', (isShowingChanges ? "false" : "true"));

		// Update the element, pin and pin window status
		updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', (isShowingChanges ? "0" : "1"));


	} else if (pin.pin_modification_type == "image") {


		// Change the content on DOM
		iframeElement(pin.pin_element_index)
			.attr('src', (isShowingChanges ? pin.pin_modification_original : pin.pin_modification) );

		// Update the element, pin and pin window status
		updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', (isShowingChanges ? "0" : "1"));


	}


}


// Remove Image
function removeImage(pin_ID) {


	console.log('DELETE THE IMAGE', pin_ID);


	var pin = getPin(pin_ID);
	if (!pin) return false;
	var pinIndex = Pins.indexOf(pin);


    // Update from the Pins global
	Pins[pinIndex].pin_modification = null;


	// Reset the uploader
	$('#pin-window .uploader img.new-image').attr('src', '');
	$('#pin-window .uploader #filePhoto').val('');


	// Revert the modification for the image
	revertChange(pin_ID);


	// Remove from DB
	saveChange(pin_ID, "{%null%}");

}



// CSS:
// DB: Save CSS changes
function saveCSS(pin_ID, css) {


    console.log( 'Save CSS for the pin #' + pin_ID + ' on DB!!', css);


    // Update from the Pins global
	var pin = getPin(pin_ID);
	if (!pin) return false;
	var pinIndex = Pins.indexOf(pin);

	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(element_index);

	//var change = modification == "{%null%}" ? null : htmlentities(modification, "ENT_QUOTES");


	//console.log('CHANGE:', change);


	// Start the process
	var pinCSSProcessID = newProcess(null, "pinCSSprocess");

	// Update from DB
    ajax('pin-css', {

		'css' 	 : css,
		'pin_ID' : pin_ID

	}).done(function(result){


		var data = result.data; console.log(data);
		var cssCode = data.css_code;


		// Check the pin if still exists
		if ( !getPin(pin_ID) ) {

			console.log('PIN #'+pin_ID+' NOT FOUND!');

			// Early finish the process
			endProcess(pinCSSProcessID);

			return false;
		}


		// Update the global
		Pins[pinIndex].pin_css = cssCode; //console.log('FILTERED: ', filtered_css);


		// Update CSS
		updateCSS(pin_ID, cssCode);


		// Finish the process
		endProcess(pinCSSProcessID);


	}).fail(function(fail) {

		console.error('FAILED: ', fail);

	});


}


// Update CSS
function updateCSS(pin_ID, cssCode) {


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(element_index);

	cssCode = assignDefault(cssCode, pin.pin_css);


	// Remove changed marks if null
	if (cssCode == null) {

		revertCSS(pin_ID);

	} else {


		var isShowingOriginalStyles = pinElement(pin_ID).is('[data-revisionary-showing-style-changes="no"]') || changedElement.is('[temp-revisionary-showing-style-changes="no"]');


		// Mark the old one
		iframeElement('style[data-pin-id="'+ pin_ID +'"]').addClass('old');


		// Add the new CSS codes
		iframeElement('body').append('<style data-index="'+ element_index +'" data-pin-id="'+ pin_ID +'">[data-revisionary-index="'+ element_index +'"]{'+ cssCode +'}</style>');


		// Remove the old ones
		iframeElement('style.old[data-pin-id="'+ pin_ID +'"]').remove();


		// Disable CSS if showing original style
		if (isShowingOriginalStyles) disableCSS(pin_ID);


		// Update the info for pin, pin window and DOM element
		updateAttributes(pin_ID, 'data-revisionary-style-changed', "yes");
		updateAttributes(pin_ID, 'data-revisionary-showing-style-changes', isShowingOriginalStyles ? "no" : "yes");
		pinWindow(element_index, true).attr('data-revisionary-style-changed', "yes");
		pinWindow(element_index, true).attr('data-revisionary-showing-style-changes', isShowingOriginalStyles ? "no" : "yes");


	}


	// Relocate the pin
	relocatePin(pin_ID);


}


// DB: Reset CSS changes
function resetCSS(pin_ID) {


    console.log( 'Reset CSS for the pin #' + pin_ID + ' on DB!!');


	// Instant revert CSS
	revertCSS(pin_ID);


	// Reset the codes
    saveCSS(pin_ID, {display: "block"});

}


// Revert CSS
function revertCSS(pin_ID) {


	console.log('REMOVING CSS FOR: #', pin_ID);


	var pin = getPin(pin_ID);
	if (!pin) return false;

	var element_index = pin.pin_element_index;
	var changedElement = iframeElement(changedElement);


	// Remove styles
	iframeElement('style[data-pin-id="'+ pin.pin_ID +'"]').remove();


	// Update the info for pin, pin window and DOM element
	updateAttributes(pin_ID, 'data-revisionary-style-changed', "no");
	updateAttributes(pin_ID, 'data-revisionary-showing-style-changes', "yes");
	changedElement
		.removeAttr('data-revisionary-style-changed')
		.removeAttr('data-revisionary-showing-style-changes');


	// For all other same element pins
	pinWindow(pin.pin_element_index, true).attr('data-revisionary-style-changed', "no"); 
	pinWindow(pin.pin_element_index, true).attr('data-revisionary-showing-style-changes', "yes");
	pinWindow(pin.pin_element_index, true).find('ul.options .main-option').removeClass('changed');
	pinWindow(pin.pin_element_index, true).find('ul.options [data-edit-css][data-revisionary-style-changed]').removeAttr('data-revisionary-style-changed');

}


// Toggle content edits
function toggleCSS(pin_ID) {


    // Get the pin from the Pins global
	var pin = getPin(pin_ID);
	if (!pin) return false;


	// If pin and its CSS found
	if (pin.pin_css != null) {


		var element_index = pin.pin_element_index;
		var styleElement = iframeElement('style[data-pin-id="'+ pin_ID +'"]');
		var isShowingCSS = styleElement.is('[media]') ? false : true;


		//console.log( isShowingCSS || forceClose ? "CLOSING" : "OPENING" );


		// Toggle the styles
		if (isShowingCSS) disableCSS(pin_ID);
		else activateCSS(pin_ID);


		// Update the element, pin and pin window status
		updateAttributes(pin_ID, 'data-revisionary-showing-style-changes', (isShowingCSS ? "no" : "yes"));


	}


}


// Disable CSS
function disableCSS(pin_ID) {

	//console.log('Disabling CSS FOR: #', pin_ID);

	var pin = getPin(pin_ID);
	if (!pin) return false;

	return iframeElement('style[data-pin-id="'+ pin.pin_ID +'"]').attr('media', 'max-width: 1px;');

}


// Activate CSS
function activateCSS(pin_ID) {

	//console.log('Activating CSS FOR: #', pin_ID);

	var pin = getPin(pin_ID);
	if (!pin) return false;

	return iframeElement('style[data-pin-id="'+ pin.pin_ID +'"]').removeAttr('media');

}


// Get CSS
function getCSS(pin_ID) {

	return iframeElement('style[data-pin-id="'+ pin_ID +'"]').text();

}



// COMMENTS:
// DB: Get Comments
function getComments(pin_ID, commentsWrapper) {


	commentsWrapper = assignDefault(commentsWrapper, $('#pin-window[data-pin-id="'+ pin_ID +'"] .pin-comments'));


	// Remove dummy comments and add loading indicator
	commentsWrapper.html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Comments are loading...</span></div>');


	// Disable comment sender
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Send the Ajax request
    commentsGetRequest = ajax('comments-get', {

		'pin_ID'	: pin_ID

	}).done(function(result){

		var comments = result.comments;

		//console.log(result.data);
		//console.log('COMMENTS: ', comments);


		// Clean the loading
		commentsWrapper.html('<div class="no-comments xl-center">No comments yet.</div>');


		// Print the comments
		var previousCommenter = "";
		var previousDirectionLeft = true;
		var previousTime = "";
		var directionLeft = true;

		$(comments).each(function(i, comment) {

			var date = new Date(comment.comment_added);
			var hide = false;
			var sameTime = false;

			// Detect if the same person comment
			if (previousCommenter == comment.user_ID) {
				directionLeft = !directionLeft;
				hide = true;

				// Detect same time comments
				if (previousTime == timeSince(date)) { //console.log('TIME SINCE', timeSince(date));
					sameTime = true;
				}

			}

			// Clean it first and mark as has comments
			if ( i == 0 ) {

				commentsWrapper.html('');
				pinWindow(pin_ID).attr('data-has-comments', 'yes');

			}


			// Append the comments
			commentsWrapper.append(
				commentTemplate(comment, directionLeft, hide, sameTime)
			);


			// Record the previous commenter
			previousDirectionLeft = directionLeft;
			directionLeft = !directionLeft;
			previousCommenter = comment.user_ID;
			previousTime = timeSince(date);

		});


		// Scroll down to the latest comment
		commentsWrapper.scrollTop(9999);


		// Enable comment sender
		$('#pin-window #comment-sender input').prop('disabled', false);


		// Relocate the window
		relocatePinWindow(pin_ID);


	});


}


// DB: Send a comment
function sendComment(pin_ID, message) {

	if (message == "") return false;

	//console.log('Sending this message: ', message);



	var pin = getPin(pin_ID);
	var pinIndex = Pins.indexOf(pin);
	var element_index = pin.pin_element_index;
	var newPin = pinWindow(pin_ID).attr('data-pin-new');



	// Write sending
	var commentsWrapper = $('#pin-window[data-pin-id="'+ pin_ID +'"] .pin-comments');
	commentsWrapper.html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Sending...</span></div>');


	// Disable the inputs
	$('#pin-window #comment-sender input, #pin-window #comment-sender textarea').prop('disabled', true);


	// Mark as has comment
	pinWindow(pin_ID).attr('data-has-comments', 'yes');


	// Mark as has new notification
	pinWindow(pin_ID).attr('data-new-notification', 'comment');


	// Start the process
	var newCommentProcessID = newProcess(null, "newPinCommentProcess");



    ajax('comment-add', {

		'pin_ID'	: pin_ID,
		'message'	: message,
		'newPin'	: newPin

	}).done(function(result){


		console.log(result.data);


		// List the comments
		getComments(pin_ID);


		// Finish the process
		endProcess(newCommentProcessID);


		// Enable the inputs
		$('#pin-window #comment-sender input, #pin-window #comment-sender textarea').prop('disabled', false);


		// Clean the text in the message box and refocus
		$('#pin-window #comment-sender .comment-input').val('').focus();
		autosize.update($('#pin-window #comment-sender .comment-input'));


		//console.log('Message SENT: ', message);

	});


}


// DB: Delete a comment
function deleteComment(pin_ID, comment_ID) {

	//console.log('Deleting comment #', comment_ID);


	// Disable the inputs
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Start the process
	var deleteCommentProcessID = newProcess(null, "deleteCommentProcess");

    ajax('comment-delete',
    {

		'pin_ID'	 : pin_ID,
		'comment_ID' : comment_ID

	}).done(function(result){

		//console.log(result.data);


		// List the comments
		getComments(pin_ID);


		// Finish the process
		endProcess(deleteCommentProcessID);


		// Enable the inputs
		$('#pin-window #comment-sender input').prop('disabled', false);


		// Clean the text in the message box and refocus
		$('#pin-window #comment-sender .comment-input').val('').focus();


		//console.log('Comment #', comment_ID, ' DELETED');

	});

}



// NOTIFICATIONS:


// Send New Pin Notification
function newPinNotification(pin_ID) {


	console.log('New pin notification sending for #' + pin_ID);


	var pin = getPin(pin_ID);
	var pinNumber = getPinNumber(pin_ID);


	doAction('newNotification', 'pin', pin_ID, pinNumber, "", "");

}


// Send New Comment Notification
function newCommentNotification(pin_ID) {


	console.log('New comment notification sending for #' + pin_ID);


	var pin = getPin(pin_ID);
	var pinNumber = getPinNumber(pin_ID);


	doAction('newCommentNotification', 'pin', pin_ID, pinNumber, "", "");


	pinWindow(pin_ID).attr('data-new-notification', 'no');

}


// Complete Notification
function completeNotification(pin_ID) {


	console.log('New complete notification sending for #' + pin_ID);


	var pin = getPin(pin_ID);
	var pinNumber = getPinNumber(pin_ID);


	doAction('completeNotification', 'pin', pin_ID, pinNumber, "", "");


	pinWindow(pin_ID).attr('data-new-notification', 'no');

}


// Incomplete Notification
function inCompleteNotification(pin_ID) {


	console.log('New incomplete notification sending for #' + pin_ID);


	var pin = getPin(pin_ID);
	var pinNumber = getPinNumber(pin_ID);


	doAction('inCompleteNotification', 'pin', pin_ID, pinNumber, "", "");


	pinWindow(pin_ID).attr('data-new-notification', 'no');

}


// Update attributes
function updateAttributes(pin_ID, attribute, value) {


	// Get the pin from the Pins global
	var pin = getPin(pin_ID);
	if (!pin) return false;
	var element_index = pin.pin_element_index;


	iframeElement(element_index).attr(attribute, value);
	pinElement(pin_ID).attr(attribute, value);
	pinWindow(pin_ID).attr(attribute, value);


}



// SELECTORS:
// Find iframe element
function iframeElement(selector) {

	if (iframeLoaded == false) return false;

	var element = false;

	if ( $.isNumeric(selector) ) {

		element = iframeElement('[data-revisionary-index="'+ selector +'"]');

		if (element.length > 1) {

			element.filter(':hidden').removeAttr('data-revisionary-index');
			element = element.filter(':visible');

		}

	} else {

		element = iframe.find(selector);

	}


	return element;

}


// Find pin element
function pinElement(selector, byElementIndex) {

	byElementIndex = assignDefault(byElementIndex, false);

	if ( $.isNumeric(selector) ) {

		if (byElementIndex)
			return pinElement('[data-revisionary-index="'+ selector +'"]');
		else
			return pinElement('[data-pin-id="'+ selector +'"]');

	} else {

		return $('#pins').children(selector);

	}

}


// Get pin from the Pins global
function getPin(pin_ID, byElementIndex) {

	byElementIndex = assignDefault(byElementIndex, false);

	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID || (byElementIndex && pin.pin_element_index == pin_ID ) ? true : false; });
	if (typeof pin === 'undefined') return false;

	return pin;
}


// Get the specific pin's window
function pinWindow(selector, byElementIndex) {


	selector = assignDefault(selector, "");
	byElementIndex = assignDefault(byElementIndex, false);


	if ( $.isNumeric(selector) ) {

		if (byElementIndex)
			return pinWindow('[data-revisionary-index="'+ selector +'"]');
		else
			return pinWindow('[data-pin-id="'+ selector +'"]');

	} else {

		return $('#pin-window' + selector);

	}


}


// Get the specific pin's window
function isPinWindowOpen(selector, byElementIndex) {


	selector = assignDefault(selector, "");
	byElementIndex = assignDefault(byElementIndex, false);


	var pin_window = pinWindow(selector, byElementIndex);

	return pin_window.hasClass('active');

}



// TEMPLATES:
// Pin template
function pinTemplate(pin_number, pin, temporary, size) {


	temporary = assignDefault(temporary, false);
	size = assignDefault(size, "big");


	var pinLocation = locationsByElement(pin.pin_element_index, pin.pin_x, pin.pin_y);


	return '\
		<pin \
			class="pin '+size+'" \
			'+(temporary ? "temporary" : "")+' \
			data-pin-type="'+pin.pin_type+'" \
			data-pin-private="'+pin.pin_private+'" \
			data-pin-complete="'+pin.pin_complete+'" \
			data-pin-id="'+pin.pin_ID+'" \
			data-pin-x="'+pin.pin_x+'" \
			data-pin-y="'+pin.pin_y+'" \
			data-pin-modification-type="'+pin.pin_modification_type+'" \
			data-revisionary-index="'+pin.pin_element_index+'" \
			data-revisionary-content-edited="'+ ( pin.pin_modification != null ? '1' : '0' ) +'" \
			data-revisionary-showing-content-changes="1" \
			data-revisionary-style-changed="'+ ( pin.pin_css != null ? 'yes' : 'no' ) +'" \
			data-revisionary-showing-style-changes="yes" \
			style="left: '+ pinLocation.x +'px; top: '+ pinLocation.y +'px;" \
		>'+pin_number+'</pin> \
	';

}


// Listed pin template
function listedPinTemplate(pin_number, pin) {

	// Pin description
	var pinText = "Comment Pin";
	if (pin.pin_type == "live") pinText = "Live Edit and " + pinText;
	if (pin.pin_modification_type == "image") pinText = pinText.replace('Live Edit', 'Image Edit');
	if (pin.pin_private == "1") pinText = "Private " + pinText;

	var editSummary = "";
	if (pin.pin_modification == null && pin.pin_css == null) editSummary = '<br /><i class="edit-summary">-No change yet.-</i>';
	if (pin.pin_css != null) editSummary = '<br /><i class="edit-summary">-Some visual changes have been made.-</i>';
	if (pin.pin_modification == "") editSummary = '<br /><i class="edit-summary">-Content deleted.-</i>';
	if (pin.pin_modification_type == "html" && pin.pin_modification != null && pin.pin_modification != "") {
		var text_no_html = cleanHTML(html_entity_decode(pin.pin_modification));
		editSummary = '<br /><i class="edit-summary">'+ text_no_html +'</i>';
	}

	if (pin.pin_modification_type == "image" && pin.pin_modification != null && pin.pin_modification != "")
		editSummary = '<br /><i class="edit-summary"><img src="'+ pin.pin_modification +'" alt="" /></i>';


	return ' \
		<div class="pin '+pin.pin_type+' '+(pin.pin_complete == "1" ? "complete" : "incomplete")+'" \
			data-pin-type="'+pin.pin_type+'" \
			data-pin-private="'+pin.pin_private+'" \
			data-pin-complete="'+pin.pin_complete+'" \
			data-pin-id="'+pin.pin_ID+'" \
			data-pin-x="'+pin.pin_x+'" \
			data-pin-y="'+pin.pin_y+'" \
			data-pin-modification-type="'+pin.pin_modification_type+'" \
			data-revisionary-index="'+pin.pin_element_index+'" \
			data-revisionary-content-edited="'+( pin.pin_modification != null ? '1' : '0' )+'" \
			data-revisionary-showing-content-changes="1"> \
			<a href="#" class="pin-locator"> \
				'+ pinTemplate(pin_number, pin, false, 'mid') +' \
			</a> \
			<a href="#" class="pin-title close"> \
				'+pinText+' <i class="fa fa-caret-up" aria-hidden="true"></i> \
				'+ editSummary +' \
			</a> \
			<div class="pin-comments" data-pin-id="'+ pin.pin_ID +'"><div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Comments are loading...</span></div></div> \
		</div> \
	';

}


// Comment template
function commentTemplate(comment, left, hide, sameTime) {

	left = assignDefault(left, true);
	hide = assignDefault(hide, false);
	sameTime = assignDefault(sameTime, false);

	var date = new Date(comment.comment_modified);
	var picture = comment.user_picture;
	var printPic = picture != null ? " style='background-image: url("+ comment.user_picture +");'" : "";
	var direction = left ? "left" : "right";
	var itsMe = comment.user_ID == user_ID ? true : false;
	var linkedComment = Autolinker.link( comment.pin_comment, {
		truncate: 25,
	    newWindow: true
	} );

	return '\
			<div class="comment wrap xl-flexbox xl-top '+ (hide ? "recurring" : "") +' '+ (sameTime ? "sametime" : "") +'"> \
				<a class="col xl-2-12 xl-'+ direction +' xl-'+ (left ? "first" : "last") +' profile-image" href="#"> \
					<picture class="profile-picture big square" '+ printPic +'> \
						<span>'+ comment.user_first_name.charAt(0) + comment.user_last_name.charAt(0) +'</span> \
					</picture> \
				</a> \
				<div class="col xl-10-12 comment-inner-wrapper"> \
					<div class="wrap xl-flexbox xl-'+ direction +' xl-bottom comment-title"> \
						<a href="#" class="col xl-'+ (left ? "first" : "last") +' comment-user-name">'+comment.user_first_name+' '+comment.user_last_name+'</a> \
						<span class="col comment-date">'+timeSince(date)+' ago</span> \
					</div> \
					<div class="comment-text xl-'+ direction +'"> \
						'+nl2br(linkedComment)+' \
						'+ (itsMe ? ' <a href="#" class="delete-comment" data-comment-id="'+comment.comment_ID+'" data-tooltip="Delete this comment">&times;</a>' : '') +' \
					</div> \
				</div> \
			</div> \
	';

}



// HELPERS:
function lightOrDark(color) {

    // Variables for red, green, blue values
    var r, g, b, hsp;

    // Check the format of the color, HEX or RGB?
    if (color.match(/^rgb/)) {

        // If HEX --> store the red, green, blue values in separate variables
        color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);

        r = color[1];
        g = color[2];
        b = color[3];
    }
    else {

        // If RGB --> Convert it to HEX: http://gist.github.com/983661
        color = +("0x" + color.slice(1).replace(
        color.length < 5 && /./g, '$&$&'));

        r = color >> 16;
        g = color >> 8 & 255;
        b = color & 255;
    }

    // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
    hsp = Math.sqrt(
    0.299 * (r * r) +
    0.587 * (g * g) +
    0.114 * (b * b)
    );

    // Using the HSP value, determine whether the color is light or dark
    if (hsp>127.5) {

        return 'light';
    }
    else {

        return 'dark';
    }
}

function rgbToHex(orig){

	var rgb = orig.replace(/\s/g,'').match(/^rgba?\((\d+),(\d+),(\d+)/i);

	return (rgb && rgb.length === 4) ? "#" +
	("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
	("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
	("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : orig;

}

function diffCheck(originalContent, changedContent) {


	var diff = JsDiff.diffWords( cleanHTML(originalContent, true), cleanHTML(changedContent, true) );
	var diffContent = "";


	// Prepare diff data
	diff.forEach(function(part){
	  // green for additions, red for deletions
	  // grey for common parts

	  var color = part.added ? 'green' :
	    part.removed ? 'red' : 'grey';

	  var diffPart = "<span class='diff "+ color +"'>"+ part.value +"</span>";
	  diffContent += diffPart;

	});


	return diffContent;
}

function get_html_translation_table(table, quote_style) {
  //  discuss at: http://phpjs.org/functions/get_html_translation_table/
  // original by: Philip Peterson
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: noname
  // bugfixed by: Alex
  // bugfixed by: Marco
  // bugfixed by: madipta
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: T.Wild
  // improved by: KELAN
  // improved by: Brett Zamir (http://brett-zamir.me)
  //    input by: Frank Forte
  //    input by: Ratheous
  //        note: It has been decided that we're not going to add global
  //        note: dependencies to php.js, meaning the constants are not
  //        note: real constants, but strings instead. Integers are also supported if someone
  //        note: chooses to create the constants themselves.
  //   example 1: get_html_translation_table('HTML_SPECIALCHARS');
  //   returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

  var entities = {},
    hash_map = {},
    decimal;
  var constMappingTable = {},
    constMappingQuoteStyle = {};
  var useTable = {},
    useQuoteStyle = {};

  // Translate arguments
  constMappingTable[0] = 'HTML_SPECIALCHARS';
  constMappingTable[1] = 'HTML_ENTITIES';
  constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
  constMappingQuoteStyle[2] = 'ENT_COMPAT';
  constMappingQuoteStyle[3] = 'ENT_QUOTES';

  useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
  useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() :
    'ENT_COMPAT';

  if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
    throw new Error('Table: ' + useTable + ' not supported');
    // return false;
  }

  entities['38'] = '&amp;';
  if (useTable === 'HTML_ENTITIES') {
	entities['38'] = '&amp;';
	entities['60'] = '&lt;';
	entities['62'] = '&gt;';
	entities['160'] = '&nbsp;';
	entities['161'] = '&iexcl;';
	entities['162'] = '&cent;';
	entities['163'] = '&pound;';
	entities['164'] = '&curren;';
	entities['165'] = '&yen;';
	entities['166'] = '&brvbar;';
	entities['167'] = '&sect;';
	entities['168'] = '&uml;';
	entities['169'] = '&copy;';
	entities['170'] = '&ordf;';
	entities['171'] = '&laquo;';
	entities['172'] = '&not;';
	entities['173'] = '&shy;';
	entities['174'] = '&reg;';
	entities['175'] = '&macr;';
	entities['176'] = '&deg;';
	entities['177'] = '&plusmn;';
	entities['178'] = '&sup2;';
	entities['179'] = '&sup3;';
	entities['180'] = '&acute;';
	entities['181'] = '&micro;';
	entities['182'] = '&para;';
	entities['183'] = '&middot;';
	entities['184'] = '&cedil;';
	entities['185'] = '&sup1;';
	entities['186'] = '&ordm;';
	entities['187'] = '&raquo;';
	entities['188'] = '&frac14;';
	entities['189'] = '&frac12;';
	entities['190'] = '&frac34;';
	entities['191'] = '&iquest;';
	entities['192'] = '&Agrave;';
	entities['193'] = '&Aacute;';
	entities['194'] = '&Acirc;';
	entities['195'] = '&Atilde;';
	entities['196'] = '&Auml;';
	entities['197'] = '&Aring;';
	entities['198'] = '&AElig;';
	entities['199'] = '&Ccedil;';
	entities['200'] = '&Egrave;';
	entities['201'] = '&Eacute;';
	entities['202'] = '&Ecirc;';
	entities['203'] = '&Euml;';
	entities['204'] = '&Igrave;';
	entities['205'] = '&Iacute;';
	entities['206'] = '&Icirc;';
	entities['207'] = '&Iuml;';
	entities['208'] = '&ETH;';
	entities['209'] = '&Ntilde;';
	entities['210'] = '&Ograve;';
	entities['211'] = '&Oacute;';
	entities['212'] = '&Ocirc;';
	entities['213'] = '&Otilde;';
	entities['214'] = '&Ouml;';
	entities['215'] = '&times;';
	entities['216'] = '&Oslash;';
	entities['217'] = '&Ugrave;';
	entities['218'] = '&Uacute;';
	entities['219'] = '&Ucirc;';
	entities['220'] = '&Uuml;';
	entities['221'] = '&Yacute;';
	entities['222'] = '&THORN;';
	entities['223'] = '&szlig;';
	entities['224'] = '&agrave;';
	entities['225'] = '&aacute;';
	entities['226'] = '&acirc;';
	entities['227'] = '&atilde;';
	entities['228'] = '&auml;';
	entities['229'] = '&aring;';
	entities['230'] = '&aelig;';
	entities['231'] = '&ccedil;';
	entities['232'] = '&egrave;';
	entities['233'] = '&eacute;';
	entities['234'] = '&ecirc;';
	entities['235'] = '&euml;';
	entities['236'] = '&igrave;';
	entities['237'] = '&iacute;';
	entities['238'] = '&icirc;';
	entities['239'] = '&iuml;';
	entities['240'] = '&eth;';
	entities['241'] = '&ntilde;';
	entities['242'] = '&ograve;';
	entities['243'] = '&oacute;';
	entities['244'] = '&ocirc;';
	entities['245'] = '&otilde;';
	entities['246'] = '&ouml;';
	entities['247'] = '&divide;';
	entities['248'] = '&oslash;';
	entities['249'] = '&ugrave;';
	entities['250'] = '&uacute;';
	entities['251'] = '&ucirc;';
	entities['252'] = '&uuml;';
	entities['253'] = '&yacute;';
	entities['254'] = '&thorn;';
	entities['255'] = '&yuml;';
	entities['402'] = '&fnof;';
	entities['913'] = '&Alpha;';
	entities['914'] = '&Beta;';
	entities['915'] = '&Gamma;';
	entities['916'] = '&Delta;';
	entities['917'] = '&Epsilon;';
	entities['918'] = '&Zeta;';
	entities['919'] = '&Eta;';
	entities['920'] = '&Theta;';
	entities['921'] = '&Iota;';
	entities['922'] = '&Kappa;';
	entities['923'] = '&Lambda;';
	entities['924'] = '&Mu;';
	entities['925'] = '&Nu;';
	entities['926'] = '&Xi;';
	entities['927'] = '&Omicron;';
	entities['928'] = '&Pi;';
	entities['929'] = '&Rho;';
	entities['931'] = '&Sigma;';
	entities['932'] = '&Tau;';
	entities['933'] = '&Upsilon;';
	entities['934'] = '&Phi;';
	entities['935'] = '&Chi;';
	entities['936'] = '&Psi;';
	entities['937'] = '&Omega;';
	entities['945'] = '&alpha;';
	entities['946'] = '&beta;';
	entities['947'] = '&gamma;';
	entities['948'] = '&delta;';
	entities['949'] = '&epsilon;';
	entities['950'] = '&zeta;';
	entities['951'] = '&eta;';
	entities['952'] = '&theta;';
	entities['953'] = '&iota;';
	entities['954'] = '&kappa;';
	entities['955'] = '&lambda;';
	entities['956'] = '&mu;';
	entities['957'] = '&nu;';
	entities['958'] = '&xi;';
	entities['959'] = '&omicron;';
	entities['960'] = '&pi;';
	entities['961'] = '&rho;';
	entities['962'] = '&sigmaf;';
	entities['963'] = '&sigma;';
	entities['964'] = '&tau;';
	entities['965'] = '&upsilon;';
	entities['966'] = '&phi;';
	entities['967'] = '&chi;';
	entities['968'] = '&psi;';
	entities['969'] = '&omega;';
	entities['977'] = '&thetasym;';
	entities['978'] = '&upsih;';
	entities['982'] = '&piv;';
	entities['8226'] = '&bull;';
	entities['8230'] = '&hellip;';
	entities['8242'] = '&prime;';
	entities['8243'] = '&Prime;';
	entities['8254'] = '&oline;';
	entities['8260'] = '&frasl;';
	entities['8472'] = '&weierp;';
	entities['8465'] = '&image;';
	entities['8476'] = '&real;';
	entities['8482'] = '&trade;';
	entities['8501'] = '&alefsym;';
	entities['8592'] = '&larr;';
	entities['8593'] = '&uarr;';
	entities['8594'] = '&rarr;';
	entities['8595'] = '&darr;';
	entities['8596'] = '&harr;';
	entities['8629'] = '&crarr;';
	entities['8656'] = '&lArr;';
	entities['8657'] = '&uArr;';
	entities['8658'] = '&rArr;';
	entities['8659'] = '&dArr;';
	entities['8660'] = '&hArr;';
	entities['8704'] = '&forall;';
	entities['8706'] = '&part;';
	entities['8707'] = '&exist;';
	entities['8709'] = '&empty;';
	entities['8711'] = '&nabla;';
	entities['8712'] = '&isin;';
	entities['8713'] = '&notin;';
	entities['8715'] = '&ni;';
	entities['8719'] = '&prod;';
	entities['8721'] = '&sum;';
	entities['8722'] = '&minus;';
	entities['8727'] = '&lowast;';
	entities['8730'] = '&radic;';
	entities['8733'] = '&prop;';
	entities['8734'] = '&infin;';
	entities['8736'] = '&ang;';
	entities['8743'] = '&and;';
	entities['8744'] = '&or;';
	entities['8745'] = '&cap;';
	entities['8746'] = '&cup;';
	entities['8747'] = '&int;';
	entities['8756'] = '&there4;';
	entities['8764'] = '&sim;';
	entities['8773'] = '&cong;';
	entities['8776'] = '&asymp;';
	entities['8800'] = '&ne;';
	entities['8801'] = '&equiv;';
	entities['8804'] = '&le;';
	entities['8805'] = '&ge;';
	entities['8834'] = '&sub;';
	entities['8835'] = '&sup;';
	entities['8836'] = '&nsub;';
	entities['8838'] = '&sube;';
	entities['8839'] = '&supe;';
	entities['8853'] = '&oplus;';
	entities['8855'] = '&otimes;';
	entities['8869'] = '&perp;';
	entities['8901'] = '&sdot;';
	entities['8968'] = '&lceil;';
	entities['8969'] = '&rceil;';
	entities['8970'] = '&lfloor;';
	entities['8971'] = '&rfloor;';
	entities['9001'] = '&lang;';
	entities['9002'] = '&rang;';
	entities['9674'] = '&loz;';
	entities['9824'] = '&spades;';
	entities['9827'] = '&clubs;';
	entities['9829'] = '&hearts;';
	entities['9830'] = '&diams;';
	entities['338'] = '&OElig;';
	entities['339'] = '&oelig;';
	entities['352'] = '&Scaron;';
	entities['353'] = '&scaron;';
	entities['376'] = '&Yuml;';
	entities['710'] = '&circ;';
	entities['732'] = '&tilde;';
	entities['8194'] = '&ensp;';
	entities['8195'] = '&emsp;';
	entities['8201'] = '&thinsp;';
	entities['8204'] = '&zwnj;';
	entities['8205'] = '&zwj;';
	entities['8206'] = '&lrm;';
	entities['8207'] = '&rlm;';
	entities['8211'] = '&ndash;';
	entities['8212'] = '&mdash;';
	entities['8216'] = '&lsquo;';
	entities['8217'] = '&rsquo;';
	entities['8218'] = '&sbquo;';
	entities['8220'] = '&ldquo;';
	entities['8221'] = '&rdquo;';
	entities['8222'] = '&bdquo;';
	entities['8224'] = '&dagger;';
	entities['8225'] = '&Dagger;';
	entities['8240'] = '&permil;';
	entities['8249'] = '&lsaquo;';
	entities['8250'] = '&rsaquo;';
	entities['8364'] = '&euro;';
  }

  if (useQuoteStyle !== 'ENT_NOQUOTES') {
    entities['34'] = '&quot;';
  }
  if (useQuoteStyle === 'ENT_QUOTES') {
    entities['39'] = '&#39;';
  }

  // ascii decimals to real symbols
  for (decimal in entities) {
    if (entities.hasOwnProperty(decimal)) {
      hash_map[String.fromCharCode(decimal)] = entities[decimal];
    }
  }

  return hash_map;
}

function htmlentities(string, quote_style, charset, double_encode) {
  //  discuss at: http://phpjs.org/functions/htmlentities/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: nobbler
  // improved by: Jack
  // improved by: Rafał Kukawski (http://blog.kukawski.pl)
  // improved by: Dj (http://phpjs.org/functions/htmlentities:425#comment_134018)
  // bugfixed by: Onno Marsman
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //    input by: Ratheous
  //  depends on: get_html_translation_table
  //        note: function is compatible with PHP 5.2 and older
  //   example 1: htmlentities('Kevin & van Zonneveld');
  //   returns 1: 'Kevin &amp; van Zonneveld'
  //   example 2: htmlentities("foo'bar","ENT_QUOTES");
  //   returns 2: 'foo&#039;bar'

  var hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style),
    symbol = '';

  string = string == null ? '' : string + '';

  if (!hash_map) {
    return false;
  }

  if (quote_style && quote_style === 'ENT_QUOTES') {
    hash_map["'"] = '&#039;';
  }

  double_encode = double_encode == null || !!double_encode;

  var regex = new RegExp('&(?:#\\d+|#x[\\da-f]+|[a-zA-Z][\\da-z]*);|[' +
    Object.keys(hash_map)
    .join('')
    // replace regexp special chars
    .replace(/([()[\]{}\-.*+?^$|\/\\])/g, '\\$1') + ']',
    'g');

  return string.replace(regex, function (ent) {
    if (ent.length > 1) {
      return double_encode ? hash_map['&'] + ent.substr(1) : ent;
    }

    return hash_map[ent];
  });
}

function html_entity_decode(string, quote_style) {
  //  discuss at: http://phpjs.org/functions/html_entity_decode/
  // original by: john (http://www.jd-tech.net)
  //    input by: ger
  //    input by: Ratheous
  //    input by: Nick Kolosov (http://sammy.ru)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: marc andreu
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Onno Marsman
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Fox
  //  depends on: get_html_translation_table
  //   example 1: html_entity_decode('Kevin &amp; van Zonneveld');
  //   returns 1: 'Kevin & van Zonneveld'
  //   example 2: html_entity_decode('&amp;lt;');
  //   returns 2: '&lt;'

  var hash_map = {},
    symbol = '',
    tmp_str = '',
    entity = '';
  tmp_str = string.toString();

  if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
    return false;
  }

  // fix &amp; problem
  // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
  delete (hash_map['&']);
  hash_map['&'] = '&amp;';

  for (symbol in hash_map) {
    entity = hash_map[symbol];
    tmp_str = tmp_str.split(entity)
      .join(symbol);
  }
  tmp_str = tmp_str.split('&#039;')
    .join("'");

  return tmp_str;
}

function canAccessIFrame(iframe) {
    var html = null;
    try {
      // deal with older browsers
      var doc = iframe[0].contentDocument || iframe[0].contentWindow.document;
      html = doc.body.innerHTML;
    } catch(err){
      // do nothing
    }

    return(html !== null);
}

function isEqual(value, other) {

	// Get the value type
	var type = Object.prototype.toString.call(value);

	// If the two objects are not the same type, return false
	if (type !== Object.prototype.toString.call(other)) return false;

	// If items are not an object or array, return false
	if (['[object Array]', '[object Object]'].indexOf(type) < 0) return false;

	// Compare the length of the length of the two items
	var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
	var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
	if (valueLen !== otherLen) {

		console.log('####################');
		console.log('DIFFERENCE(Length):');
		console.log(valueLen);
		console.log(otherLen);
		console.log('####################');

		return false;
	}

	// Compare two items
	var compare = function (item1, item2) {

		// Get the object type
		var itemType = Object.prototype.toString.call(item1);

		// If an object or array, compare recursively
		if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
			if (!isEqual(item1, item2)) {

				console.log('####################');
				console.log('DIFFERENCE(Asd):');
				console.log(item1);
				console.log(item2);
				console.log('####################');

				return false;
			}
		}

		// Otherwise, do a simple comparison
		else {

			// If the two items are not the same type, return false
			if (itemType !== Object.prototype.toString.call(item2)) {

				console.log('####################');
				console.log('DIFFERENCE(Type):');
				console.log(itemType);
				console.log(Object.prototype.toString.call(item2));
				console.log('####################');

				return false;
			}

			// Else if it's a function, convert to a string and compare
			// Otherwise, just compare
			if (itemType === '[object Function]') {
				if (item1.toString() !== item2.toString()) {

					console.log('####################');
					console.log('DIFFERENCE(toString):');
					console.log(item1.toString());
					console.log(item2.toString());
					console.log('####################');

					return false;
				}
			} else {
				if (item1 !== item2) {

					console.log('####################');
					console.log('DIFFERENCE(Items):');
					console.log(item1);
					console.log(item2);
					console.log('####################');

					return false;
				}
			}

		}
	};

	// Compare properties
	if (type === '[object Array]') {
		for (var i = 0; i < valueLen; i++) {
			if (compare(value[i], other[i]) === false) return false;
		}
	} else {
		for (var key in value) {
			if (value.hasOwnProperty(key)) {
				if (compare(value[key], other[key]) === false) return false;
			}
		}
	}

	// If nothing failed, return true
	return true;

}

function makeID() {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

	for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));

	return text;
}

function nl2br(str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

jQuery.fn.onPositionChanged = function(trigger, millis) {


    if (millis == null) millis = 100;
    var o = $(this[0]); // Our object
	if (o.length < 1) return o;
	var element_index = o.attr('data-revisionary-index');
	//console.log('INDEX: ', element_index);

    var lastPos = null;
    var lastOff = null;
    var lastWidth = null;
    var lastOffWidth = null;

    setInterval(function() {

        if (o == null || o.length < 1) return o; // Abort if element is not exist anymore
		if ( o.css('display') == "none" ) o = o.parent(); // If this hidden element

        if (lastPos == null) lastPos = o.position();
        if (lastOff == null) lastOff = o.offset();
        if (lastWidth == null) lastWidth = o.width();
        if (lastOffWidth == null) lastOffWidth = o[0].offsetWidth;

        var newPos = o.position();
        var newOff = o.offset();
        var newWidth = o.width();
        var newOffWidth = o[0].offsetWidth;

        if (lastPos.top != newPos.top || lastPos.left != newPos.left) {

			console.log('Position changed');

            $(this).trigger('onPositionChanged', { lastPos: lastPos, newPos: newPos });
            if (typeof (trigger) == "function") trigger(lastPos, newPos);
			lastPos = o.position();

			delete hiddenElementOffsets[element_index];

		}

        if (lastOff.top != newOff.top || lastOff.left != newOff.left) {

			console.log('Offset changed');

            $(this).trigger('onPositionChanged', { lastOff: lastOff, newOff: newOff});
            if (typeof (trigger) == "function") trigger(lastOff, newOff);
			lastOff = o.offset();

			delete hiddenElementOffsets[element_index];

		}

        if (lastWidth != newWidth) {

			console.log('Size changed');

            $(this).trigger('onPositionChanged', { lastWidth: lastWidth, newWidth: newWidth});
            if (typeof (trigger) == "function") trigger(lastWidth, newWidth);
			lastWidth = o.width();

			delete hiddenElementOffsets[element_index];

		}

        if (lastOffWidth != newOffWidth) {

			console.log('Offset Size changed', lastOffWidth, newOffWidth);

            $(this).trigger('onPositionChanged', { lastOffWidth: lastOffWidth, newOffWidth: newOffWidth});
            if (typeof (trigger) == "function") trigger(lastOffWidth, newOffWidth);
			lastOffWidth = o[0].offsetWidth;

			delete hiddenElementOffsets[element_index];

        }

    }, millis);


	return o;

};