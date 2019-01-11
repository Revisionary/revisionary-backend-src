// FUNCTIONS:
// DB: Run the internalizator
function checkPageStatus(page_ID, queue_ID, processID, loadingProcessID) {


	// If being force reinternalizing, update the URL
	var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;

	if (history.replaceState) {
	    var newurl = queryParameter(currentUrl, 'redownload', "");
	    if (newurl != currentUrl) window.history.replaceState({path:newurl},'',newurl);
	}


	// Get the up-to-date pins
	var statusCheckRequest = ajax('internalize-status',
	{
		'page_ID'		: page_ID,
		'queue_ID'		: queue_ID,
		'processID'		: processID

	}).done(function(result) {

		var data = result.data.final;


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
			page_URL = data.pageUrl + '?v=' + data.internalized;


			// Update the iframe url
			$('iframe').attr('src', page_URL);


			// Run the inspector
			runTheInspector();

		}


		// Restart if not done
		if (data.status != "not-running" &&	data.processStatus != "ready") {

			setTimeout(function() {

				checkPageStatus(page_ID, queue_ID, processID, loadingProcessID);

			}, 1000);

		}


	}).fail(function() {


		// Abort the latest request if not finalized
		if(statusCheckRequest && statusCheckRequest.readyState != 4) {
			console.log('Latest status check request aborted');
			statusCheckRequest.abort();
		}

		setTimeout(function() {

			checkPageStatus(page_ID, queue_ID, processID, loadingProcessID);

		}, 1000);


	});


}


// Initiate the inspector
function runTheInspector() {


	// WHEN IFRAME DOCUMENT READY !!! ?
	$('iframe').contents().ready(function() {


	});


	// WHEN IFRAME HAS LOADED
	$('iframe').on('load', function() {



		// Iframe element
	    iframe = $('iframe').contents();


		// REDIRECT DETECTION:
		// Check if the page redirected to another page
		if (page_redirected) {

			console.log('PAGE REDIRECTED');

			setTimeout(function() { // Does not work sometimes, and needs improvement !!!

				$('#page').css('opacity', '');

				iframe.scrollTop(oldScrollOffset_top);
				iframe.scrollLeft(oldScrollOffset_left);

				//console.log('REDIRECTED', page_redirected, oldScrollOffset_top, oldScrollOffset_left);

				oldScrollOffset_top = oldScrollOffset_left = 0;

				page_redirected = false;

			}, 2000);

		}

		if ( !canAccessIFrame( $(this) ) ) {

			console.log('IFRAME UNACCESSIBLE');

			$('#page').css('opacity', '0.1');

			oldScrollOffset_top = scrollOffset_top;
			oldScrollOffset_left = scrollOffset_left;

			if (!page_redirected) window.frames["the-page"].location = page_URL;

			page_redirected = true; //console.log('REDIRECTED', page_redirected, scrollOffset_top, scrollOffset_left);

		}



		// SITE STYLES
		iframeElement('body').append(' \
		<style> \
			/* Auto-height edited images */ \
			img[data-revisionary-showing-changes="1"] { height: auto !important; } \
		</style> \
		');



		// CURSOR WORKS:
		// Close Pin Mode pinTypeSelector
		if (currentPinType == "browse") {

			toggleCursorActive(true);

		} else {

			switchPinType(currentPinType, currentPinPrivate);
			toggleCursorActive(false, true);

		}



		// PINS:
		// Get latest pins and apply them to the page
		Pins = [];
		getPins(true, true);



		// PAGE INTERACTIONS:
		// Close all the tabs
		$('.opener').each(function() {

			toggleTab( $(this), true );

		});


	    // Update the title
		if ( iframeElement('title').length ) {
			$('title').text( "Revise Page: " + iframeElement('title').text() );
		}



		// MOUSE ACTIONS:
		var mouseDownOnContentEdit = false;
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
			focused_element_edited_parents = focused_element.parents('[data-revisionary-index][data-revisionary-edited]');
			focused_element_has_edited_child = focused_element.find('[data-revisionary-index][data-revisionary-edited]').length;



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


					//console.log('REFOCUS - Only child element has no child and has content: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class'));

				}


				// Re-focus to the edited element if this is child of it: <p data-edited="1" focused><b>Lorem
				if (focused_element_edited_parents.length) {

					// Re-focus to the parent edited element
					focused_element = focused_element_edited_parents.first();


					//console.log('REFOCUS - Already edited closest parent: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class'));

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
				focused_element_edited_parents = focused_element.parents('[data-revisionary-index][data-revisionary-edited]');
				focused_element_has_edited_child = focused_element.find('[data-revisionary-index][data-revisionary-edited]').length;



				// EDITABLE CHECKS:
				hoveringText = false;
		        focused_element_editable = false;
		        focused_element_html_editable = false;


				// Check element text editable: <p>Lorem ipsum dolor sit amet...
		        if (
			        easy_html_elements.indexOf( focused_element.prop("tagName") ) != -1 && // In easy HTML elements?
		        	focused_element_text.trim() != "" && // If not empty
		        	focused_element.html() != "&nbsp;" && // If really not empty
		        	focused_element_children.length == 0 // If doesn't have any child
		        ) {

					hoveringText = true;
					focused_element_editable = true; // Obviously Text Editable
					focused_element_html_editable = true;
					//console.log( '* Obviously Text Editable: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
					//console.log( 'Focused Element Text: ' + focused_element_text );

				}


				// Check element image editable: <img src="#">...
				hoveringImage = false;
		        if ( focused_element.prop("tagName") == "IMG" ) {

					hoveringImage = true;
					focused_element_editable = true; // Obviously Image Editable
					//console.log( '* Obviously Image Editable: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
					//console.log( 'Focused Element Image: ' + focused_element.attr('src') );

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
						if (easy_with_br.indexOf( $(this).prop("tagName") ) != -1 ) hardToEdit = false;

					});

					if (!hardToEdit) {

						hoveringText = true;
						focused_element_editable = true;
						focused_element_html_editable = true;
						//console.log( '* Text Editable (No Grand Child): ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
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
							easy_with_br.indexOf( child.prop("tagName") ) != -1 && // Child is easy to edit
							grandChildren.length == 1 && // Grand child has no more than 1 child
							easy_with_br.indexOf( grandChildren.first().prop("tagName") ) != -1 // And that guy is easy to edit as well
						)

							easyToEdit = true;

					});

					if (easyToEdit) {

						hoveringText = true;
						focused_element_editable = true;
						focused_element_html_editable = true;
						//console.log( '* Text Editable (One Grand Child): ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
						//console.log( 'Focused Element Text: ' + focused_element_text );

					}


				}


				// Check the submit buttons: <input type="submit | reset">...
				hoveringButton = false;
		        if (
		        	focused_element.prop("tagName") == "INPUT" &&
		        	(
		        		focused_element.attr("type") == "submit" ||
		        		focused_element.attr("type") == "reset"
		        	)
		        ) {

					hoveringButton = true;
					focused_element_editable = true; // Obviously Image Editable
					//console.log( '* Button Editable: ' + focused_element.prop("tagName") );
					//console.log( 'Focused Button Text: ' + focused_element.attr('value') );

				}



				// PREVENTIONS:
				// Check if it doesn't have any element index: <p data-revisionary-index="16">...
				if (focused_element_editable && !focused_element_has_index) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					//console.log( '* Element editable but NO INDEX: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );

				}


				// If focused element has edited child, don't focus it
				if (focused_element_has_edited_child > 1 ) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					//console.log( '* Element editable but there are edited #'+focused_element_has_edited_child+' children: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );

				}



/*
				// See what am I focusing
				//console.log("CURRENT FOCUSED: ", focused_element.prop("tagName"), focused_element_index );
				//console.log("CURRENT FOCUSED EDITABLE: ", focused_element_editable );
*/



				// Clean Other Outlines
				removeOutline();

				// Reset the pin opacity
				$('#pins > pin').css('opacity', '');



				// LIVE REACTIONS:
				focused_element_has_live_pin = focused_element_live_pin.length ? true : false;
				//if (currentPinType == "live") {


					// Editable check
					if (focused_element_editable && currentPinType == "live") {

						switchCursorType('live');
						outline(focused_element, currentPinPrivate, currentPinType);


					} else {

						// If not editable, switch back to the standard pin
						switchCursorType('standard');
						if (focused_element_has_index) outline(focused_element, currentPinPrivate, (focused_element_editable && currentPinType == "live" ? "live" : "standard"));

					}


					// Check if the element already has a live pin
					if ( focused_element_live_pin.length ) {


						// Point to the pin
						$('#pins > pin:not([data-revisionary-index="'+ focused_element_index +'"])').css('opacity', '0.2');


						// Update the cursor
						changePinNumber( focused_element_live_pin.text() );
						switchCursorType(focused_element_live_pin.data('pin-type'), focused_element_live_pin.data('pin-private'), focused_element_live_pin.data('pin-type'));
						outline(focused_element, focused_element_live_pin.attr('data-pin-private'), focused_element_live_pin.attr('data-pin-type'));



						//console.log('This element already has a live pin.');

					} else {


						// Re-update the cursor number
						currentPinNumber = $('#pins > pin').length + 1;
						changePinNumber(currentPinNumber);


					}


				//} // If current pin type is 'live'



			} // If cursor active


		}).on('mousedown', function(e) { // Detect the mouse clicks in frame


			// While editing a content on page
			mouseDownOnContentEdit = cursorActive && focused_element_has_live_pin ? true : false;


		}).on('click', function(e) { // Detect the mouse clicks in frame


			//console.log('CLICKED SOMEWHERE', focused_element_index);


			// If cursor is active
			if (cursorActive) {

				// If focused element has a live pin
				if (focused_element_has_live_pin) {

					// Open the new pin window if already open one or clicking an image editable
					if (
						pinWindowOpen
						|| focused_element_pin.attr('data-pin-modification-type') == "image"
						|| focused_element.attr('data-revisionary-showing-changes') == "0"
					)
						openPinWindow( focused_element_pin.attr('data-pin-id') );

				} else {

					// Add a pin and open a pin window
					if ( !mouseDownOnContentEdit ) putPin(e.pageX, e.pageY);

				}


			} else {

				// Close the pin window if open and not cursor active and not content editable
				if ( pinWindowOpen && !iframeElement(focused_element_index).is('[contenteditable]') )
					closePinWindow();

			}


			// Prevent clicking something
			e.preventDefault();
			return false;

		}).on('scroll', function(e) { // Detect the scroll to re-position pins

			//console.log('SCROLLIIIIIIIING');


		    // Re-Locate the pins
		    relocatePins();

		});


		// Detect changes on page text
		var doChangeOnPage = {};
		iframe.on('input', '[contenteditable="true"][data-revisionary-index]', function(e) {


			var elementIndex = $(this).attr('data-revisionary-index');
			var pin_ID = pinElement('[data-pin-type="live"][data-revisionary-index="'+elementIndex+'"]').attr('data-pin-id');
			var changedElement = $(this);
			var modification = changedElement.html();


			//console.log('REGISTERED CHANGES', modification);


			// Stop the auto-refresh
			stopAutoRefresh();


			// Instant apply the change on pin window
			$('#pin-window.active .content-editor .edit-content.changes').html(modification);


			// If differences tab is open
			if ( pinWindowOpen && pinWindow.hasClass('show-differences') ) {


				var originalContent = pinWindow.find('.content-editor .edit-content.original').html();
				var changedContent = pinWindow.find('.content-editor .edit-content.changes').html();


				// Difference check
				var diffContent = diffCheck(originalContent, changedContent)


				// Add the differences content
				pinWindow.find('.content-editor .edit-content.differences').html( diffContent );

			}


			// Remove unsent job
			if (doChangeOnPage[elementIndex]) clearTimeout(doChangeOnPage[elementIndex]);

			// Send changes to DB after 1 second
			doChangeOnPage[elementIndex] = setTimeout(function(){

				saveChange(pin_ID, modification);

			}, 1000);

			//console.log('Content changed.');


		}).on('focus', '[contenteditable="true"][data-revisionary-index]', function(e) { // When clicked an editable text


			outline(focused_element, focused_element_live_pin.attr('data-pin-private'));
			focused_element.addClass('revisionary-focused');


			// Open the new pin window
			if (
				pinWindowOpen
				&& focused_element_live_pin != null && focused_element_live_pin.length
				&& pinWindow.attr('data-revisionary-index') != focused_element_index
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


		});


		$(window).on('resize', function(e) { // Detect the window resizing to re-position pins

			//console.log('RESIZIIIIIIIING');


		    // Re-Locate the pins
		    relocatePins();

		});


	});


}


// Tab Toggler
function toggleTab(opener, forceClose = false) {

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


// Color the element
function outline(element, private_pin, pin_type = "live") {

	var block = pin_type == "live" ? false : true;

	var elementColor = private_pin == 1 ? '#FC0FB3' : '#7ED321';
	if (block) elementColor = private_pin == 1 ? '#6b95f3' : '#1DBCC9';

	var outlineWidth = '2px';
	if (block) outlineWidth = '2px';

	if (element != null) element.css('outline', outlineWidth + ' dashed ' + elementColor, 'important');

}


// Color the element
function removeOutline() {

	// Remove outlines from iframe
	iframeElement('*:not(.revisionary-focused)').css('outline', '');

	return true;
}


// Switch to a different pin mode
function switchPinType(pinType, pinPrivate) {

	log('Switched Pin Type: ', pinType);
	log('Switched Pin Private: ', pinPrivate);


	currentPinType = pinType;
	currentPinPrivate = pinPrivate;


	// Change the activator color and label
	if (pinType == "live") currentPinLabel = pinModes.live;
	if (pinType == "standard") currentPinLabel = pinModes.standard;
	if (currentPinPrivate == "1") currentPinLabel = pinModes['private-live'];

	activator.attr('data-pin-type', currentPinType).attr('data-pin-private', currentPinPrivate).find('.mode-label').text(currentPinLabel);


	// Change the cursor color
	switchCursorType(pinType == 'live' ? 'standard' : pinType, currentPinPrivate);


	// Activate the cursor
	if (!cursorActive) toggleCursorActive(false, true);


	// Close the open pin window
	if (pinWindowOpen) closePinWindow();

}


// Switch to a different cursor mode
function switchCursorType(cursorType, pinPrivate = currentPinPrivate, existing = false) {

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
function toggleCursorActive(forceClose = false, forceOpen = false) {


	cursor.stop();
	var cursorVisible = cursor.is(":visible");


	// Remove outlines from iframe
	removeOutline();


	if ( (cursorActive || forceClose) && !forceOpen ) {


		// Deactivate
		activator.attr('data-pin-type', 'browse');

		// Update the label
		$('.current-mode .mode-label').text('BROWSE MODE');



		// Close the dropdown
		$('.pin-mode .dropdown > ul').hide();

		// Hide the cursor
		if (cursorVisible) cursor.fadeOut();

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
		if (!cursorVisible && !pinWindowOpen) cursor.fadeIn();

		// Hide the original cursor
		if ( !iframeElement('#revisionary-cursor').length )
			iframeElement('body').append('<style id="revisionary-cursor"> * { cursor: none !important; } </style>');

		// Disable all the links
	    // ...


		cursorActive = true;
		//if (pinTypeSelectorOpen) togglePinTypeSelector(true); // Force Close

	}


	// Close the open pin window
	if (pinWindowOpen) closePinWindow();

}


// Change the pin number on cursor
function changePinNumber(pinNumber) {

	cursor.text(pinNumber);
	currentPinNumber = pinNumber;

}


// DB: Get up-to-date pins and changes
function getPins(applyChanges = true, firstRetrieve = false) {


	console.log('GETTING PINS...');


	// Record the old pins
	var oldPins = Pins;


	// Send the Ajax request
	autoRefreshRequest = ajax('pins-get',
	{

		'device_ID'	: device_ID

	}).done(function( result ) {


		// Update the global pins list
		Pins = updateOriginals(result.pins, oldPins);



		console.log('OLD PINS: ', oldPins);
		console.log('NEW PINS: ', Pins);



		// If different than current pins, do the changes
		if ( !isEqual(Pins, oldPins) ) {


			console.log('There are some updates...');


			// Apply Pins, revert old pins
			if (applyChanges) applyPins(oldPins);


		} else {


			console.log('No changes found');
			autoRefreshRequest = null;


			if (firstRetrieve) {

				// Hide the loading overlay
				$('#loading').fadeOut();

				// Page is ready now
				page_ready = true;
				$('body').addClass('ready');

			}


		}


		// Start auto refresh if not already started
		if (!autoRefreshTimer && processCount == 0) startAutoRefresh();


	});

}


// Update originals
function updateOriginals(pinsList = [], oldPinsList) {


	$(pinsList).each(function(i, pin) {


		// Skip standard and unmodified pins
		if ( pin.pin_type != "live" || pin.pin_modification_original != null ) return true;


		var theOriginal = null;
		var pin_ID = pin.pin_ID;
		var oldPin = oldPinsList.find(function(p) { return p.pin_ID == pin_ID ? true : false; });


		// Check from the existing Pins global
		if (oldPin && oldPin.pin_modification_original != null)
			theOriginal = oldPin.pin_modification_original;


		// Check if it's an untouched dom
		else if ( iframeElement(pin.pin_element_index).is(':not([data-revisionary-showing-changes = "1"])') ) {

			if (pin.pin_modification_type == "html") {

				theOriginal = htmlentities(iframeElement(pin.pin_element_index).html(), "ENT_QUOTES");

			} else if (pin.pin_modification_type == "image") {

				theOriginal = iframeElement(pin.pin_element_index).attr('src');

			}

		}


		pinsList[i].pin_modification_original = theOriginal;

	});


	return pinsList;

}


// Apply the pins
function applyPins(oldPins = []) {


	console.log('APPLYING PINS...');


	// Revert the changes first
	var showingOriginal = revertChanges([], oldPins); // !!! REVERT THE CSS CHANGES


	// Empty the pins
	$('#pins').html('');


	// Add the pins
	$(Pins).each(function(i, pin) {

		var pin_number = i + 1;

		// Add the pin to the list
		$('#pins').append(
			pinTemplate(pin_number, pin.pin_ID, pin.pin_complete, pin.pin_element_index, pin.pin_modification, pin.pin_modification_type, pin.pin_private, pin.pin_type, pin.pin_x, pin.pin_y)
		);


	});


	// Update the cursor number with the existing pins
	currentPinNumber = $('#pins > pin').length + 1;
	changePinNumber(currentPinNumber);


	// Make pins draggable
	makeDraggable();


	// Relocate the pins
	relocatePins();


	// Update the pins list tab
	updatePinsList();


	// Apply changes
	applyChanges(showingOriginal);

}


// Apply changes
function applyChanges(showingOriginal = []) {


	$(Pins).each(function(i, pin) {


		// Find the element
		var element_index = pin.pin_element_index;
		var element = iframeElement(element_index);
		var thePin = pinElement(pin.pin_ID);



		// CSS CODES:
		if ( pin.pin_css != null ) updateCSS(element_index, pin.pin_css);



		// MODIFICATIONS:
		// Skip standard and unmodified pins
		if ( pin.pin_type != "live" ) return true;


		console.log('APPLYING PIN: ', i, pin);


		// Is showing changes
		var isShowingOriginal = showingOriginal.includes(element_index) ? true : false;


		// Add the contenteditable attribute to the live elements
		if (pin.pin_modification_type == "html")
			element.attr('contenteditable', (isShowingOriginal ? "false" : "true"));


		if (pin.pin_modification == null ) return true;


		// If it was showing changes
		if (!isShowingOriginal) {


			// If the type is HTML content change
			if ( pin.pin_modification_type == "html" ) {


				//console.log('MODIFICATION ORIG:', pin.pin_modification);


				// Apply the change
				var newHTML = html_entity_decode(pin.pin_modification); //console.log('NEW', newHTML);
				element.html( newHTML ).attr('contenteditable', (isShowingOriginal ? "false" : "true"));


				//console.log('MODIFICATION DECODED:', newHTML);


			// If the type is image change
			} else if ( pin.pin_modification_type == "image" ) {


				// Apply the change
				var newSrc = pin.pin_modification; //console.log('NEW', newHTML);
				element.attr('src', newSrc).removeAttr('srcset');


			}

		}



		// Update the element and pin status
		element.attr('data-revisionary-edited', "1")
			.attr('data-revisionary-showing-changes', (isShowingOriginal ? "0" : "1"));
		thePin.attr('data-revisionary-edited', "1")
			.attr('data-revisionary-showing-changes', (isShowingOriginal ? "0" : "1"));



	});


	autoRefreshRequest = null;


	console.log('CHANGES APPLIED');


	// Hide the loading overlay
	$('#loading').fadeOut();

	// Page is ready now
	page_ready = true;
	$('body').addClass('ready');


}


// Update CSS
function updateCSS(element_index, cssCodes) {


	// Mark the old one
	iframeElement('style[data-index="'+ element_index +'"]').addClass('old');


	// Add the new CSS codes
	iframeElement('body').append('<style data-index="'+ element_index +'">[data-revisionary-index="'+ element_index +'"]{'+ cssCodes +'}</style>');


	// Remove the old ones
	iframeElement('style.old[data-index="'+ element_index +'"]').remove();

}


// Revert changes
function revertChanges(element_indexes = [], pinsList = Pins) {

	if ( element_indexes.length ) console.log('REVERTING CHANGES FOR: ', element_indexes);


	var showingOriginal = [];
	$(pinsList).each(function(i, pin) {


		// If specific modifications requested to revert, skip reverting
		if ( element_indexes.length && !element_indexes.includes(pin.pin_element_index) ) return true;


		// Revert the CSS
		iframeElement('style[data-index="'+ pin.pin_element_index +'"]').remove();


		// Skip standard and unmodified pins
		if ( pin.pin_type != "live" || pin.pin_modification_original == null ) return true;


		console.log('REVERTING PIN: ', pin);



		// Find the element
		var element_index = pin.pin_element_index;
		var element = iframeElement(element_index);
		var thePin = pinElement(pin.pin_ID);


		// Is currently showing the edits?
		var isShowingOriginal = element.is('[data-revisionary-edited="1"][data-revisionary-showing-changes="0"]') ? true : false;

		// Add this element as showingOriginal element, if not currently showing changes
		if (isShowingOriginal) showingOriginal.push(element_index);


		// If the type is HTML content change
		if ( pin.pin_modification_type == "html" ) {

			// Revert the change
			var oldHTML = html_entity_decode(pin.pin_modification_original); //console.log('NEW', newHTML);
			element.html( oldHTML );

		// If the type is image change
		} else if ( pin.pin_modification_type == "image" ) {

			// Revert the change
			var oldSrc = pin.pin_modification_original; //console.log('NEW', newHTML);
			element.attr('src', oldSrc);

		}


		// Update the element and pin status
		element
			.removeAttr('data-revisionary-edited')
			.removeAttr('data-revisionary-showing-changes')
			.removeAttr('contenteditable');
		thePin.attr('data-revisionary-edited', "0").attr('data-revisionary-showing-changes', "0");


	});


	return showingOriginal;


}


// Activate Pins Drag
function makeDraggable(pin = $('#pins > pin:not([temporary])')) {



	// Make pins draggable
	pin.draggable({
		containment: ".iframe-container",
		iframeFix: true,
		scroll: false,
		snap: true,
		snapMode: "outer",
		snapTolerance: 10,
		stack: "#pins > pin",
		cursor: "move",
		opacity: 0.35,
		start: function( event, ui ) {


			//console.log('STARTED!');


		},
		drag: function( event, ui ) {


			//console.log('DRAGGING ', ui.position.top, ui.position.left);


			// Stop auto refresh
			stopAutoRefresh();


			// Fixed decimal for DB structure
			var leftDest = Math.abs(ui.originalPosition.left - ui.position.left);
			var topDest = Math.abs(ui.originalPosition.top - ui.position.top);

			console.log('DIFF: ', leftDest, topDest);


			if (leftDest + topDest > 4) {
				pinDragging = true;

				console.log('DRAGGING!!!');
			}



			// If pin window open, attach it to the pin
			relocatePins($(this), ui.position.left, ui.position.top, true);


		},
		stop: function( event, ui ) {


			//console.log('STOPPED.');


			var pinWasDragging = pinDragging;
			pinDragging = false;


			// Get the pin
			focusedPin = $(this);
			var pin_ID = focusedPin.attr('data-pin-id');


			// Get the final positions
			var pos_x = ui.position.left;
			var pos_y = ui.position.top;


			//console.log('DROPPED ', pos_x, pos_y);


			// Update the pin location attributes
			relocatePins(focusedPin, pos_x, pos_y);


			// Get the updated positions
			var pinX = parseFloat(focusedPin.attr('data-pin-x')).toFixed(5);
			var pinY = parseFloat(focusedPin.attr('data-pin-y')).toFixed(5);


			console.log('RELOCATING: ', pinX, pinY);


		    // Update the pin location on DB
		    //console.log('Update the new pin location on DB', pinX, pinY);


			// Start the process
			var relocateProcessID = newProcess();

		    $.post(ajax_url, {
				'type'	  	 : 'pin-relocate',
				'pin_ID'	 : pin_ID,
				'pin_x' 	 : pinX,
				'pin_y' 	 : pinY
			}, function(result){


				// Update the global 'pins'
				var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
				var pinIndex = Pins.findIndex(function(element, index, array) { return element == pin; });

				Pins[pinIndex].pin_x = pinX;
				Pins[pinIndex].pin_y = pinY;



				// Finish the process
				endProcess(relocateProcessID);

				//console.log(result.data);

			}, 'json');


		}
	});


}


// Re-Locate Pins
function relocatePins(pin_selector = null, x = null, y = null, onlyPinWindow = false) {


	// Update the location and size values first
	updateLocationValues();


	if ( pin_selector ) {


		pin_ID = pin_selector.attr('data-pin-id');


	    var scrolled_pin_x = x > 0 ? x : 0;
	    var scrolled_pin_y = y > 0 ? y : 0;

	    scrolled_pin_x = x < iframeWidth - 45 ? scrolled_pin_x : iframeWidth - 45;
	    scrolled_pin_y = y < iframeHeight - 45 ? scrolled_pin_y : iframeHeight - 45;

		pin_selector.css('left', scrolled_pin_x + "px");
		pin_selector.css('top', scrolled_pin_y + "px");


		var realPinX = (scrolled_pin_x / iframeScale) + scrollOffset_left;
		var realPinY = (scrolled_pin_y / iframeScale) + scrollOffset_top;


		// Update the registered pin location
	    pin_selector.attr('data-pin-x', realPinX);
		pin_selector.attr('data-pin-y', realPinY );


		// Update the registered pin window location as well, only if current pin is moving
		if ( pin_ID == pinWindow.attr('data-pin-id') ) {

			pinWindow.attr('data-pin-x', realPinX);
			pinWindow.attr('data-pin-y', realPinY);

		}


	} else if (!onlyPinWindow) {


	    $('#pins > pin').each(function() {

		    var pin = $(this);


		    var pin_x = parseInt(pin.attr('data-pin-x')) * iframeScale;
		    var pin_y = parseInt(pin.attr('data-pin-y')) * iframeScale;


		    var scrolled_pin_x = pin_x - scrollX;
		    var scrolled_pin_y = pin_y - scrollY;


		    pin.css('left', scrolled_pin_x + "px");
			pin.css('top', scrolled_pin_y + "px");

	    });


	}


	// Current pin window location
	var window_x = parseInt(pinWindow.attr('data-pin-x')) * iframeScale;
	var window_y = parseInt(pinWindow.attr('data-pin-y')) * iframeScale;


    var scrolled_window_x = window_x + offset.left - scrollX + 50;
    var scrolled_window_y = window_y + offset.top - scrollY + 50;


	var spaceWidth = offset.left + iframeWidth + offset.left - 15;
	var spaceHeight = offset.top + iframeHeight + offset.top - 15 - $('#top-bar').height();


    var new_scrolled_window_x = scrolled_window_x < spaceWidth - pinWindowWidth ? scrolled_window_x : spaceWidth - pinWindowWidth;
    var new_scrolled_window_y = scrolled_window_y < spaceHeight - pinWindowHeight ? scrolled_window_y : spaceHeight - pinWindowHeight;


	// Change the side of the window
	if (
		scrolled_window_x >= spaceWidth - pinWindowWidth &&
		scrolled_window_y >= spaceHeight - pinWindowHeight
	) {

		//console.log('OUCH!');
		new_scrolled_window_x = scrolled_window_x - pinWindowWidth - 55;

	}


	// Make the pin window stay after scrolling up
	if (scrolled_window_y > new_scrolled_window_y + pinWindowHeight) {

		//console.log('GOODBYE!');
		new_scrolled_window_y = scrolled_window_y - pinWindowHeight;

	}


	// Relocate the pin window
	pinWindow.not('.moved').css('left', new_scrolled_window_x + "px");
	pinWindow.not('.moved').css('top', new_scrolled_window_y + "px");

}


// Update location values
function updateLocationValues() {

	// Update the values
	offset = $('#the-page').offset();

	scrollOffset_top = iframe.scrollTop();
	scrollOffset_left = iframe.scrollLeft();

	scrollX = scrollOffset_left * iframeScale;
	scrollY = scrollOffset_top * iframeScale;

	pinWindowWidth = pinWindow.outerWidth();
	pinWindowHeight = pinWindow.outerHeight();

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


// Start auto-refresh
function startAutoRefresh(interval = autoRefreshInterval) {

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


// DB: Put a pin to cordinates
function putPin(pinX, pinY) {


	// Stop auto-refresh pins
	stopAutoRefresh();


	// Put it just on the pointer point
	pinX = parseFloat(pinX - 45/2).toFixed(5);
	pinY = parseFloat(pinY - 45/2).toFixed(5);


	console.log('Put the Pin #' + currentPinNumber, pinX, pinY, currentCursorType, currentPinPrivate, focused_element_index);


	// Detect modification info
	var modificationType = null;
	var modificationOriginal = null;

	if (currentCursorType == "live") {

		var editedElement = iframeElement(focused_element_index);

		// Add edited status to the DOM
		editedElement
			.attr('data-revisionary-edited', "0")
			.attr('contenteditable', "true");

		modificationType = hoveringText ? "html" : "image";
		modificationOriginal = modificationType == "html" ? htmlentities( editedElement.html(), "ENT_QUOTES") : editedElement.attr('src');

	}



	var temporaryPinID = makeID();


	// Add the temporary pin to the DOM
	$('#pins').append(
		pinTemplate(currentPinNumber, temporaryPinID, '0', focused_element_index, null, modificationType, currentPinPrivate, currentCursorType, pinX, pinY, true)
	);



	// Add it to the pins global
	var pinsIndex = Pins.length;
	Pins[pinsIndex] = {
		pin_ID: temporaryPinID,
		pin_complete: 0,
		pin_element_index: parseInt(focused_element_index),
		pin_modification: null,
		pin_modification_original: modificationOriginal,
		pin_modification_type: modificationType,
		pin_css: null,
		pin_private: currentPinPrivate,
		pin_type: currentCursorType,
		pin_x: pinX,
		pin_y: pinY
	};



	// Open the pin window
	openPinWindow(temporaryPinID, true);




    //console.log('Add pin to the DB !!!');

	// Start the process
	var newPinProcessID = newProcess();

	// Add pin to the DB
    ajax('pin-add',
    {
		'pin_x' 	 			: pinX,
		'pin_y' 	 			: pinY,
		'pin_type' 	 			: currentCursorType,
		'pin_modification_type' : modificationType == null ? "{%null%}" : modificationType,
		'pin_private'			: currentPinPrivate,
		'pin_element_index' 	: focused_element_index,
		'pin_device_ID'			: device_ID

	}).done(function(result){

		//console.log(result.data);

		var realPinID = result.data.real_pin_ID;
		var newPin = pinElement('[data-pin-id="'+ temporaryPinID +'"]');

		//console.log('REAL PIN ID: '+realPinID);


		// Update the pin ID
		newPin.attr('data-pin-id', realPinID).removeAttr('temporary');
		pinWindow.attr('data-pin-id', realPinID);
		Pins[pinsIndex].pin_ID = realPinID;


		// Remove the loading text on pin window
		pinWindow.removeClass('loading');


		// Make draggable
		makeDraggable(newPin);


		// Finish the process
		endProcess(newPinProcessID);

	});


	// Re-Locate the pins
	relocatePins();


	// Increase the pin number
	changePinNumber(parseInt( currentPinNumber ) + 1);

}


// Open the pin window
function openPinWindow(pin_ID, firstTime = false) {

	console.log('OPEN WINDOW PIN ID', pin_ID, firstTime);


/*
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);
*/


	var thePin = pinElement('[data-pin-id="'+pin_ID+'"]');
	var thePinType = thePin.attr('data-pin-type');
	var thePinPrivate = thePin.attr('data-pin-private');
	var thePinComplete = thePin.attr('data-pin-complete');
	var theIndex = thePin.attr('data-revisionary-index');
	var thePinText = thePinPrivate == '1' ? 'PRIVATE VIEW' : 'ONLY VIEW';
	var thePinModificationType = thePin.attr('data-pin-modification-type');
	var thePinModified = thePin.attr('data-revisionary-edited');
	var thePinShowingChanges = thePin.attr('data-revisionary-showing-changes');


	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;


	// Previous state of cursor
	if (!pinWindowWasOpen) cursorWasActive = cursorActive;


	// Close the previous window
	closePinWindow();


	// Disable the iframe
	//$('#the-page').css('pointer-events', 'none');


	// Disable the inspector
	toggleCursorActive(true); // Force deactivate


	// Add the pin window data !!!
	pinWindow
		.attr('data-pin-type', thePinType)
		.attr('data-pin-private', thePinPrivate)
		.attr('data-pin-complete', thePinComplete)
		.attr('data-pin-x', thePin.attr('data-pin-x'))
		.attr('data-pin-y', thePin.attr('data-pin-y'))
		.attr('data-pin-modification-type', thePinModificationType)
		.attr('data-pin-id', pin_ID)
		.attr('data-revisionary-edited', thePinModified)
		.attr('data-revisionary-showing-changes', thePinShowingChanges)
		.attr('data-revisionary-index', theIndex);


	// Reset the differences fields
	pinWindow.removeClass('show-differences');
	pinWindow.find('span.diff-text').text('SHOW DIFFERENCE');
	pinWindow.find('.difference-switch > i').removeClass('fa-random', 'fa-pencil-alt').addClass('fa-random');


	// Update the pin type section
	pinWindow.find('pin.chosen-pin')
		.attr('data-pin-type', thePinType)
		.attr('data-pin-private', thePinPrivate);
	pinWindow.find('.pin-label').text(thePinText);



	// CSS OPTIONS:
	var thePinElement = iframeElement(theIndex);
	var styleElement = iframeElement('style[data-index="'+ theIndex +'"]');
	var options = pinWindow.find('ul.options');

	// Update the current element section
	$('.element-tag, .element-id, .element-class').text('');

	// Tag Name
	var tagName = thePinElement.prop("tagName");
	$('.element-tag').text(tagName);

	// Classes
	var classes = "";
	var classList = thePinElement.attr('class');
	if (classList != null) {

		$.each(classList.split(/\s+/), function(index, className) {

			classes = classes + "."+className;

		});
		$('.element-class').text(classes);

	}

	// ID Name
	var idName = thePinElement.attr("id");
	if (idName != null) $('.element-id').text('#'+idName);


	// Display
	var display = thePinElement.css('display') != "none" ? "block" : "none";
	pinWindow.find('.edit-display > a').removeClass('active');
	pinWindow.find('.edit-display > .edit-display-'+display).addClass('active');
	options.attr('data-display', display);

	// Opacity
	var opacity = thePinElement.css('opacity');
	pinWindow.find('.edit-opacity #edit-opacity').val(opacity).trigger('change');
	options.attr('data-opacity', opacity);


	// Style Element
	options.attr('data-changed', (styleElement.length ? "yes" : "no"));



	// If on 'Live' mode
	if (thePinType == 'live') {


		// Update the pin type section
		thePinText = thePinPrivate == '1' ? 'PRIVATE LIVE' : 'LIVE EDIT';
		pinWindow.find('.pin-label').text(thePinText);



		// Get the pin from the Pins global
		var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });


		// TEXT
		if ( thePinModificationType == "html" ) {

			var originalContent = "";
			var changedContent = "";


			// If it's untouched DOM
			if ( iframeElement(theIndex).is(':not([data-revisionary-showing-changes])') ) {

				originalContent = iframeElement(theIndex).html();

				// Default change editor
				changedContent = originalContent;
			}


			// Add the original HTML content to the window
			else if (pin && pin.pin_modification_original != null)
				originalContent = html_entity_decode(pin.pin_modification_original);


			// Get the changed content
			if (pin && pin.pin_modification != null)
				changedContent = html_entity_decode(pin.pin_modification);


			// Difference check
			//var diffContent = diffCheck(originalContent, changedContent)


			// Add the original HTML content
			pinWindow.find('.content-editor .edit-content.original').html( originalContent );


			// Add the changed HTML content
			pinWindow.find('.content-editor .edit-content.changes').html( changedContent );


			// Add the differences content
			//pinWindow.find('.content-editor .edit-content.differences').html( diffContent );


		}


		// IMAGE
		if ( thePinModificationType == "image" ) {

			var originalImageSrc = "";
			var changedImageSrc = "";


			// If it's untouched DOM
			if ( iframeElement(theIndex).is(':not([data-revisionary-showing-changes])') ) {

				originalImageSrc = iframeElement(theIndex).attr('src');

				// Default image preview
				changedImageSrc = "";
			}


			// Add the original image URL
			else if (pin && pin.pin_modification_original != null)
				originalImageSrc = pin.pin_modification_original;


			// Update it the image is a relative path
			if (originalImageSrc.indexOf('http://') !== 0 && originalImageSrc.indexOf('https://') !== 0)
				originalImageSrc = remote_URL + originalImageSrc;


			// Get the new image
			if (pin && pin.pin_modification != null)
				changedImageSrc = pin.pin_modification;



			// Add the original image
			pinWindow.find('.image-editor .edit-content.original img.original-image').attr('src', originalImageSrc);


			// Add the new image
			pinWindow.find('.image-editor .edit-content.changes img.new-image').attr('src', changedImageSrc);

		}



	}


	// If it's first time, remove the "Done" button
	$('#pin-window .pin-complete').hide();
	if (!firstTime) $('#pin-window .pin-complete').show();


	// Reveal it
	pinWindow.addClass('active');
	pinWindowOpen = true;


	// If the new pin registered, remove the loading message
	if ( $.isNumeric(pin_ID) )
		pinWindow.removeClass('loading');



	// COMMENTS
	// If new pin added
	if (firstTime)
		$('.pin-comments').html('<div class="xl-center">Add your comment:</div>'); // Write a message

	// If this is an already registered pin
	else
		getComments(pin_ID); // Bring the comments


	// Clean the existing comment in the input
	$('#pin-window input.comment-input').val('');



	// Show the pin
	$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');


	// Relocate the window
	relocatePins();

}


// Close pin window
function closePinWindow() {


	console.log('PIN WINDOW CLOSING.');


	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;

	// Hide it
	pinWindow.removeClass('active');
	pinWindowOpen = false;


	// Add the loading text after loading
	pinWindow.addClass('loading');


	// Remove the moved class
	pinWindow.removeClass('moved');


	// Reset the pin opacity
	$('#pins > pin').css('opacity', '');


	if (cursorWasActive) toggleCursorActive(false, true); // Force Open


	// Enable the iframe
	//$('#the-page').css('pointer-events', '');

}


// Toggle pin window
function togglePinWindow(pin_ID) {

	if (pinWindowOpen && pinWindow.attr('data-pin-id') == pin_ID) closePinWindow();
	else openPinWindow(pin_ID);

}


// DB: Remove a pin
function removePin(pin_ID) {


    // Add pin to the DB
    console.log('Remove the pin #' + pin_ID + ' from DB!!');


    // Add removing message
    pinWindow.addClass('removing');


	// Start the process
	var newPinProcessID = newProcess();

    ajax('pin-remove',
    {
		'type'	  	: 'pin-remove',
		'pin_ID'	: pin_ID

	}).done(function(result){

		//console.log(result.data);


	    // Remove removing message
	    pinWindow.removeClass('removing');


		// Close the pin window
		closePinWindow();


		// Bring the pin info
		var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
		var pinIndex = Pins.indexOf(pin);

		//console.log(modification);

		if (pin) {

			// Revert the changes
			revertChanges([pin.pin_element_index]);


			// Delete from the list
			Pins.splice(pinIndex, 1);

		}



		// Remove the pin from DOM
		pinElement(pin_ID).remove();


		// Re-Index the pin counts
		reindexPins();


		// Finish the process
		endProcess(newPinProcessID);


	});


}


// DB: Complete/Incomplete a pin
function completePin(pin_ID, complete) {


    console.log( (complete ? 'Complete' : 'Incomplete') +' the pin #' + pin_ID + ' on DB!!');


	// Update from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);

	Pins[pinIndex].pin_complete = complete ? 1 : 0;


	// Update the pin status
	pinElement(pin_ID).attr('data-pin-complete', (complete ? '1' : '0'));


	// Update the pin window status
	pinWindow.attr('data-pin-complete', (complete ? '1' : '0'));



	// Start the process
	var completePinProcessID = newProcess();

    // Update pin from the DB
	ajax('pin-complete',
	{
		'pin_ID' 	   : pin_ID,
		'complete'	   : (complete ? 'complete' : 'incomplete')

	}).done(function(result) {

		console.log(result.data);



		// Finish the process
		endProcess(completePinProcessID);

	});


}


// DB: Convert pin
function convertPin(pin_ID, targetPin) {


	// New values
	var pinType = targetPin.data('pin-type');
	var pinPrivate = targetPin.data('pin-private');
	var pinLabel = targetPin.next().text();
	var elementIndex = parseInt( pinElement(pin_ID).attr('data-revisionary-index') ); console.log('element-index', elementIndex);


	console.log('Convert PIN #'+ pin_ID +' to: ', pinType, 'Private: ' + pinPrivate);


	// Update from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);


	// If the new type is standard, reset the modifications
	if (pinType == "standard") {

		revertChanges([elementIndex]);

		Pins[pinIndex].pin_modification_type = null;
		Pins[pinIndex].pin_modification = null;
		Pins[pinIndex].pin_modification_original = null;

		pinElement(pin_ID).attr('data-pin-modification-type', 'null');
		pinWindow.attr('data-pin-modification-type', 'null');

		// Remove outlines from iframe
		removeOutline();

	}

	Pins[pinIndex].pin_type = pinType;
	Pins[pinIndex].pin_private = pinPrivate;


	// Update the pin status
	pinElement(pin_ID)
		.attr('data-pin-type', pinType)
		.attr('data-pin-private', pinPrivate);


	// Update the pin window status
	pinWindow.attr('data-pin-type', pinType)
		.attr('data-pin-type', pinType)
		.attr('data-pin-private', pinPrivate);


	// Update the pin type section label
	pinWindow.find('pin.chosen-pin').attr('data-pin-type', pinType).attr('data-pin-private', pinPrivate);
	pinWindow.find('.pin-label').text(pinLabel);


	// If it's a live pin, change the element outline color
	if (pinType == "live") outline( iframeElement(elementIndex), pinPrivate );



	// Start the process
	var convertPinProcessID = newProcess();


	// Save it on DB
	ajax('pin-convert',
	{
		'pin_ID' 	    : pin_ID,
		'pin_type'		: pinType,
		'pin_private'   : pinPrivate

	}).done(function(result) {

		console.log(result.data);


		// Finish the process
		endProcess(convertPinProcessID);

	});

}


// DB: Save a modification
function saveChange(pin_ID, modification) {


    // Add pin to the DB
    //console.log( 'Save modification for the pin #' + pin_ID + ' on DB!!');


    // Update from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);
	var elementIndex = pin.pin_element_index;
	var changedElement = iframeElement(elementIndex);
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
	var modifyPinProcessID = newProcess();

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
		if (modification != "{%null%}") {

			pinElement(pin_ID).attr('data-revisionary-edited', "1");
			pinWindow.attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");
			changedElement.attr('data-revisionary-edited', "1");
			changedElement.attr('data-revisionary-showing-changes', "1");

		} else {

			pinElement(pin_ID).attr('data-revisionary-edited', "0");
			pinWindow.attr('data-revisionary-edited', "0").attr('data-revisionary-showing-changes', "1");
			changedElement.removeAttr('data-revisionary-showing-changes');
			changedElement.removeAttr('data-revisionary-edited');

		}


		// Finish the process
		endProcess(modifyPinProcessID);

	});


}


// DB: Save CSS changes
function saveCSS(pin_ID, css) {


    console.log( 'Save CSS for the pin #' + pin_ID + ' on DB!!', css);


    // Update from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);

	var elementIndex = pin.pin_element_index;
	var changedElement = iframeElement(elementIndex);

	//var change = modification == "{%null%}" ? null : htmlentities(modification, "ENT_QUOTES");


	//console.log('CHANGE:', change);


	// Start the process
	var pinCSSProcessID = newProcess();

	// Update from DB
    ajax('pin-css', {

		'css' 	 : css,
		'pin_ID' : pin_ID

	}).done(function(result){


		var data = result.data; console.log(data);
		var cssCode = data.css_code;


		// Update the global
		Pins[pinIndex].pin_css = cssCode; //console.log('FILTERED: ', filtered_css);


		// Update the changed status
		pinWindow.find('ul.options').attr('data-changed', (cssCode != null ? "yes" : "no"));


		// Remove the CSS codes if null
		if (cssCode == null) {

		    // Reopen the pin window
		    openPinWindow(pin_ID);

		}


		// Finish the process
		endProcess(pinCSSProcessID);

	});


}


// DB: Reset CSS changes
function resetCSS(pin_ID) {


    console.log( 'Reset CSS for the pin #' + pin_ID + ' on DB!!');


	// Reset the codes
    saveCSS(pin_ID, {display: "block"});

}


// Toggle content edits
function toggleChange(pin_ID) {


	// Check if this is currently showing the changed content
	var isShowingChanges = pinWindow.attr('data-revisionary-showing-changes') == "1" ? true : false;


    // Get the pin from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });


	// If pin found
	if (pin) {


		if (pin.pin_modification_type == "html") {


			// Change the content on DOM
			iframeElement(pin.pin_element_index)
				.html( html_entity_decode( (isShowingChanges ? pin.pin_modification_original : pin.pin_modification) ) )
				.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1") )
				.attr('contenteditable', (isShowingChanges ? "false" : "true"));

			// Update the Pin Window and Pin info
			pinWindow.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));
			pinElement(pin_ID).attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));


		} else if (pin.pin_modification_type == "image") {


			// Change the content on DOM
			iframeElement(pin.pin_element_index)
				.attr('src', (isShowingChanges ? pin.pin_modification_original : pin.pin_modification) )
				.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1") );

			// Update the Pin Window and Pin info
			pinWindow.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));
			pinElement(pin_ID).attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));


		}


	}

}


// DB: Get Comments
function getComments(pin_ID, commentsWrapper = $('#pin-window .pin-comments')) {


	// Remove dummy comments and add loading indicator
	commentsWrapper.html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Comments are loading...</span></div>');


	// Disable comment sender
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Send the Ajax request
    ajax('comments-get',
    {

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

			// Clean it first
			if ( i == 0 ) commentsWrapper.html('');


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
		relocatePins();


	});


}


// DB: Send a comment
function sendComment(pin_ID, message) {

	//console.log('Sending this message: ', message);


	// Disable the inputs
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Start the process
	var newCommentProcessID = newProcess();

    ajax('comment-add',
    {

		'pin_ID'	: pin_ID,
		'message'	: message

	}).done(function(result){

		//console.log(result.data);


		// List the comments
		getComments(pin_ID);


		// Finish the process
		endProcess(newCommentProcessID);


		// Enable the inputs
		$('#pin-window #comment-sender input').prop('disabled', false);


		// Clean the text in the message box and refocus
		$('#pin-window #comment-sender input.comment-input').val('').focus();


		//console.log('Message SENT: ', message);

	});

}


// DB: Delete a comment
function deleteComment(pin_ID, comment_ID) {

	//console.log('Deleting comment #', comment_ID);


	// Disable the inputs
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Start the process
	var deleteCommentProcessID = newProcess();

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
		$('#pin-window #comment-sender input.comment-input').val('').focus();


		//console.log('Comment #', comment_ID, ' DELETED');

	});

}


// Remove Image
function removeImage(pin_ID, element_index) {

	console.log('DELETE THE IMAGE');


	// Reset the uploader
	$('#pin-window .uploader img.new-image').attr('src', '');
	$('#pin-window .uploader #filePhoto').val('');


	// Revert the modification for the image
	revertChanges([element_index]);


    // Update from the Pins global
	var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
	var pinIndex = Pins.indexOf(pin);

	Pins[pinIndex].pin_modification = null;


	// Update the pin and pin window info
	pinElement(pin_ID).attr('data-revisionary-edited', '0').attr('data-revisionary-showing-changes', '1');
	pinWindow.attr('data-revisionary-edited', '0').attr('data-revisionary-showing-changes', '1');


	// Remove from DB
	saveChange(pin_ID, "{%null%}");

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
			listedPinTemplate(pin_number, pin.pin_ID, pin.pin_complete, pin.pin_element_index, pin.pin_modification, pin.pin_modification_type, pin.pin_private, pin.pin_type, pin.pin_x, pin.pin_y)
		);


	});

}



// SELECTORS:
// Find iframe element
function iframeElement(selector) {

	if ( $.isNumeric(selector) ) {

		return iframeElement('[data-revisionary-index="'+ selector +'"]');

	} else {

		return iframe.find(selector);

	}

}


// Find pin element
function pinElement(selector, byElementIndex = false) {

	if ( $.isNumeric(selector) ) {

		if (byElementIndex)
			return pinElement('[data-revisionary-index="'+ selector +'"]');
		else
			return pinElement('[data-pin-id="'+ selector +'"]');

	} else {

		return $('#pins').children(selector);

	}

}



// TEMPLATES:
// Pin template
function pinTemplate(pin_number, pin_ID, pin_complete, pin_element_index, pin_modification, pin_modification_type, pin_private, pin_type, pin_x, pin_y, temporary = false, size = "big") {

	return '\
		<pin \
			class="pin '+size+'" \
			'+(temporary ? "temporary" : "")+' \
			data-pin-type="'+pin_type+'" \
			data-pin-private="'+pin_private+'" \
			data-pin-complete="'+pin_complete+'" \
			data-pin-id="'+pin_ID+'" \
			data-pin-x="'+pin_x+'" \
			data-pin-y="'+pin_y+'" \
			data-pin-modification-type="'+pin_modification_type+'" \
			data-revisionary-index="'+pin_element_index+'" \
			data-revisionary-edited="'+( pin_modification != null ? '1' : '0' )+'" \
			data-revisionary-showing-changes="1" \
		>'+pin_number+'</pin> \
	';

}


// Listed pin template
function listedPinTemplate(pin_number, pin_ID, pin_complete, pin_element_index, pin_modification, pin_modification_type, pin_private, pin_type, pin_x, pin_y, temporary = false) {

	// Pin description
	var pinText = "Comment Pin";
	if (pin_type == "live") pinText = "Live Edit and " + pinText;
	if (pin_modification_type == "image") pinText = pinText.replace('Live Edit', 'Image Edit');
	if (pin_private == "1") pinText = "Private " + pinText;

	var editSummary = "";
	if (pin_modification == null) editSummary = '<br /><i class="edit-summary">-No change yet.-</i>';
	if (pin_modification == "") editSummary = '<br /><i class="edit-summary">-Content deleted.-</i>';
	if (pin_modification_type == "html" && pin_modification != null && pin_modification != "") {
		var text_no_html = cleanHTML(html_entity_decode(pin_modification));
		editSummary = '<br /><i class="edit-summary">'+ text_no_html +'</i>';
	}

	if (pin_modification_type == "image" && pin_modification != null && pin_modification != "")
		editSummary = '<br /><i class="edit-summary"><img src="'+ pin_modification +'" alt="" /></i>';


	return ' \
		<div class="pin '+pin_type+' '+(pin_complete == "1" ? "complete" : "incomplete")+'" \
			'+(temporary ? "temporary" : "")+' \
			data-pin-type="'+pin_type+'" \
			data-pin-private="'+pin_private+'" \
			data-pin-complete="'+pin_complete+'" \
			data-pin-id="'+pin_ID+'" \
			data-pin-x="'+pin_x+'" \
			data-pin-y="'+pin_y+'" \
			data-pin-modification-type="'+pin_modification_type+'" \
			data-revisionary-index="'+pin_element_index+'" \
			data-revisionary-edited="'+( pin_modification != null ? '1' : '0' )+'" \
			data-revisionary-showing-changes="1"> \
			<a href="#" class="pin-locator"> \
				'+ pinTemplate(pin_number, pin_ID, pin_complete, pin_element_index, pin_modification, pin_modification_type, pin_private, pin_type, pin_x, pin_y, temporary, 'mid') +' \
			</a> \
			<a href="#" class="pin-title close"> \
				'+pinText+' <i class="fa fa-caret-up" aria-hidden="true"></i> \
				'+ editSummary +' \
			</a> \
			<div class="pin-comments"><div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Comments are loading...</span></div></div> \
		</div> \
	';

}


// Comment template
function commentTemplate(comment, left = true, hide = false, sameTime = false) {

	var date = new Date(comment.comment_modified);
	var picture = comment.user_picture;
	var hasPic = picture == null ? false : true;
	var printPic = hasPic ? " style='background-image: url(/cache/users/user-"+ comment.user_ID +"/"+ comment.user_picture +");'" : "";
	var direction = left ? "left" : "right";
	var itsMe = comment.user_ID == user_ID ? true : false;

	return '\
			<div class="comment wrap xl-flexbox xl-top '+ (hide ? "recurring" : "") +' '+ (sameTime ? "sametime" : "") +'"> \
				<a class="col xl-2-12 xl-'+ direction +' xl-'+ (left ? "first" : "last") +' profile-image" href="#"> \
					<picture class="profile-picture big square" '+ printPic +'> \
						<span class="'+ (hasPic ? "has-pic" : "") +'">'+ comment.user_first_name.charAt(0) + comment.user_last_name.charAt(0) +'</span> \
					</picture> \
				</a> \
				<div class="col xl-10-12 comment-inner-wrapper"> \
					<div class="wrap xl-flexbox xl-'+ direction +' xl-bottom comment-title"> \
						<a href="#" class="col xl-'+ (left ? "first" : "last") +' comment-user-name">'+comment.user_first_name+' '+comment.user_last_name+'</a> \
						<span class="col comment-date">'+timeSince(date)+' ago</span> \
					</div> \
					<div class="comment-text xl-'+ direction +'"> \
						'+comment.pin_comment+' \
						'+ (itsMe ? ' <a href="#" class="delete-comment" data-comment-id="'+comment.comment_ID+'" data-tooltip="Delete this comment">&times;</a>' : '') +' \
					</div> \
				</div> \
			</div> \
	';

}



// HELPERS:
function queryParameter(url, key, value = null) {


	var url = new URL(url);
	var query_string = url.search;
	var search_params = new URLSearchParams(query_string);


	if (value == "") search_params.delete(key);
	else search_params.set(key, value);


	if (value == null) return search_params.get(key);


	url.search = search_params.toString();
	var new_url = url.toString();

	return new_url;
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
    decimal
  var constMappingTable = {},
    constMappingQuoteStyle = {}
  var useTable = {},
    useQuoteStyle = {}

  // Translate arguments
  constMappingTable[0] = 'HTML_SPECIALCHARS'
  constMappingTable[1] = 'HTML_ENTITIES'
  constMappingQuoteStyle[0] = 'ENT_NOQUOTES'
  constMappingQuoteStyle[2] = 'ENT_COMPAT'
  constMappingQuoteStyle[3] = 'ENT_QUOTES'

  useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS'
  useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() :
    'ENT_COMPAT'

  if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
    throw new Error('Table: ' + useTable + ' not supported')
    // return false;
  }

  entities['38'] = '&amp;'
  if (useTable === 'HTML_ENTITIES') {
	entities['38'] = '&amp;'
	entities['60'] = '&lt;'
	entities['62'] = '&gt;'
	entities['160'] = '&nbsp;'
	entities['161'] = '&iexcl;'
	entities['162'] = '&cent;'
	entities['163'] = '&pound;'
	entities['164'] = '&curren;'
	entities['165'] = '&yen;'
	entities['166'] = '&brvbar;'
	entities['167'] = '&sect;'
	entities['168'] = '&uml;'
	entities['169'] = '&copy;'
	entities['170'] = '&ordf;'
	entities['171'] = '&laquo;'
	entities['172'] = '&not;'
	entities['173'] = '&shy;'
	entities['174'] = '&reg;'
	entities['175'] = '&macr;'
	entities['176'] = '&deg;'
	entities['177'] = '&plusmn;'
	entities['178'] = '&sup2;'
	entities['179'] = '&sup3;'
	entities['180'] = '&acute;'
	entities['181'] = '&micro;'
	entities['182'] = '&para;'
	entities['183'] = '&middot;'
	entities['184'] = '&cedil;'
	entities['185'] = '&sup1;'
	entities['186'] = '&ordm;'
	entities['187'] = '&raquo;'
	entities['188'] = '&frac14;'
	entities['189'] = '&frac12;'
	entities['190'] = '&frac34;'
	entities['191'] = '&iquest;'
	entities['192'] = '&Agrave;'
	entities['193'] = '&Aacute;'
	entities['194'] = '&Acirc;'
	entities['195'] = '&Atilde;'
	entities['196'] = '&Auml;'
	entities['197'] = '&Aring;'
	entities['198'] = '&AElig;'
	entities['199'] = '&Ccedil;'
	entities['200'] = '&Egrave;'
	entities['201'] = '&Eacute;'
	entities['202'] = '&Ecirc;'
	entities['203'] = '&Euml;'
	entities['204'] = '&Igrave;'
	entities['205'] = '&Iacute;'
	entities['206'] = '&Icirc;'
	entities['207'] = '&Iuml;'
	entities['208'] = '&ETH;'
	entities['209'] = '&Ntilde;'
	entities['210'] = '&Ograve;'
	entities['211'] = '&Oacute;'
	entities['212'] = '&Ocirc;'
	entities['213'] = '&Otilde;'
	entities['214'] = '&Ouml;'
	entities['215'] = '&times;'
	entities['216'] = '&Oslash;'
	entities['217'] = '&Ugrave;'
	entities['218'] = '&Uacute;'
	entities['219'] = '&Ucirc;'
	entities['220'] = '&Uuml;'
	entities['221'] = '&Yacute;'
	entities['222'] = '&THORN;'
	entities['223'] = '&szlig;'
	entities['224'] = '&agrave;'
	entities['225'] = '&aacute;'
	entities['226'] = '&acirc;'
	entities['227'] = '&atilde;'
	entities['228'] = '&auml;'
	entities['229'] = '&aring;'
	entities['230'] = '&aelig;'
	entities['231'] = '&ccedil;'
	entities['232'] = '&egrave;'
	entities['233'] = '&eacute;'
	entities['234'] = '&ecirc;'
	entities['235'] = '&euml;'
	entities['236'] = '&igrave;'
	entities['237'] = '&iacute;'
	entities['238'] = '&icirc;'
	entities['239'] = '&iuml;'
	entities['240'] = '&eth;'
	entities['241'] = '&ntilde;'
	entities['242'] = '&ograve;'
	entities['243'] = '&oacute;'
	entities['244'] = '&ocirc;'
	entities['245'] = '&otilde;'
	entities['246'] = '&ouml;'
	entities['247'] = '&divide;'
	entities['248'] = '&oslash;'
	entities['249'] = '&ugrave;'
	entities['250'] = '&uacute;'
	entities['251'] = '&ucirc;'
	entities['252'] = '&uuml;'
	entities['253'] = '&yacute;'
	entities['254'] = '&thorn;'
	entities['255'] = '&yuml;'
	entities['402'] = '&fnof;'
	entities['913'] = '&Alpha;'
	entities['914'] = '&Beta;'
	entities['915'] = '&Gamma;'
	entities['916'] = '&Delta;'
	entities['917'] = '&Epsilon;'
	entities['918'] = '&Zeta;'
	entities['919'] = '&Eta;'
	entities['920'] = '&Theta;'
	entities['921'] = '&Iota;'
	entities['922'] = '&Kappa;'
	entities['923'] = '&Lambda;'
	entities['924'] = '&Mu;'
	entities['925'] = '&Nu;'
	entities['926'] = '&Xi;'
	entities['927'] = '&Omicron;'
	entities['928'] = '&Pi;'
	entities['929'] = '&Rho;'
	entities['931'] = '&Sigma;'
	entities['932'] = '&Tau;'
	entities['933'] = '&Upsilon;'
	entities['934'] = '&Phi;'
	entities['935'] = '&Chi;'
	entities['936'] = '&Psi;'
	entities['937'] = '&Omega;'
	entities['945'] = '&alpha;'
	entities['946'] = '&beta;'
	entities['947'] = '&gamma;'
	entities['948'] = '&delta;'
	entities['949'] = '&epsilon;'
	entities['950'] = '&zeta;'
	entities['951'] = '&eta;'
	entities['952'] = '&theta;'
	entities['953'] = '&iota;'
	entities['954'] = '&kappa;'
	entities['955'] = '&lambda;'
	entities['956'] = '&mu;'
	entities['957'] = '&nu;'
	entities['958'] = '&xi;'
	entities['959'] = '&omicron;'
	entities['960'] = '&pi;'
	entities['961'] = '&rho;'
	entities['962'] = '&sigmaf;'
	entities['963'] = '&sigma;'
	entities['964'] = '&tau;'
	entities['965'] = '&upsilon;'
	entities['966'] = '&phi;'
	entities['967'] = '&chi;'
	entities['968'] = '&psi;'
	entities['969'] = '&omega;'
	entities['977'] = '&thetasym;'
	entities['978'] = '&upsih;'
	entities['982'] = '&piv;'
	entities['8226'] = '&bull;'
	entities['8230'] = '&hellip;'
	entities['8242'] = '&prime;'
	entities['8243'] = '&Prime;'
	entities['8254'] = '&oline;'
	entities['8260'] = '&frasl;'
	entities['8472'] = '&weierp;'
	entities['8465'] = '&image;'
	entities['8476'] = '&real;'
	entities['8482'] = '&trade;'
	entities['8501'] = '&alefsym;'
	entities['8592'] = '&larr;'
	entities['8593'] = '&uarr;'
	entities['8594'] = '&rarr;'
	entities['8595'] = '&darr;'
	entities['8596'] = '&harr;'
	entities['8629'] = '&crarr;'
	entities['8656'] = '&lArr;'
	entities['8657'] = '&uArr;'
	entities['8658'] = '&rArr;'
	entities['8659'] = '&dArr;'
	entities['8660'] = '&hArr;'
	entities['8704'] = '&forall;'
	entities['8706'] = '&part;'
	entities['8707'] = '&exist;'
	entities['8709'] = '&empty;'
	entities['8711'] = '&nabla;'
	entities['8712'] = '&isin;'
	entities['8713'] = '&notin;'
	entities['8715'] = '&ni;'
	entities['8719'] = '&prod;'
	entities['8721'] = '&sum;'
	entities['8722'] = '&minus;'
	entities['8727'] = '&lowast;'
	entities['8730'] = '&radic;'
	entities['8733'] = '&prop;'
	entities['8734'] = '&infin;'
	entities['8736'] = '&ang;'
	entities['8743'] = '&and;'
	entities['8744'] = '&or;'
	entities['8745'] = '&cap;'
	entities['8746'] = '&cup;'
	entities['8747'] = '&int;'
	entities['8756'] = '&there4;'
	entities['8764'] = '&sim;'
	entities['8773'] = '&cong;'
	entities['8776'] = '&asymp;'
	entities['8800'] = '&ne;'
	entities['8801'] = '&equiv;'
	entities['8804'] = '&le;'
	entities['8805'] = '&ge;'
	entities['8834'] = '&sub;'
	entities['8835'] = '&sup;'
	entities['8836'] = '&nsub;'
	entities['8838'] = '&sube;'
	entities['8839'] = '&supe;'
	entities['8853'] = '&oplus;'
	entities['8855'] = '&otimes;'
	entities['8869'] = '&perp;'
	entities['8901'] = '&sdot;'
	entities['8968'] = '&lceil;'
	entities['8969'] = '&rceil;'
	entities['8970'] = '&lfloor;'
	entities['8971'] = '&rfloor;'
	entities['9001'] = '&lang;'
	entities['9002'] = '&rang;'
	entities['9674'] = '&loz;'
	entities['9824'] = '&spades;'
	entities['9827'] = '&clubs;'
	entities['9829'] = '&hearts;'
	entities['9830'] = '&diams;'
	entities['338'] = '&OElig;'
	entities['339'] = '&oelig;'
	entities['352'] = '&Scaron;'
	entities['353'] = '&scaron;'
	entities['376'] = '&Yuml;'
	entities['710'] = '&circ;'
	entities['732'] = '&tilde;'
	entities['8194'] = '&ensp;'
	entities['8195'] = '&emsp;'
	entities['8201'] = '&thinsp;'
	entities['8204'] = '&zwnj;'
	entities['8205'] = '&zwj;'
	entities['8206'] = '&lrm;'
	entities['8207'] = '&rlm;'
	entities['8211'] = '&ndash;'
	entities['8212'] = '&mdash;'
	entities['8216'] = '&lsquo;'
	entities['8217'] = '&rsquo;'
	entities['8218'] = '&sbquo;'
	entities['8220'] = '&ldquo;'
	entities['8221'] = '&rdquo;'
	entities['8222'] = '&bdquo;'
	entities['8224'] = '&dagger;'
	entities['8225'] = '&Dagger;'
	entities['8240'] = '&permil;'
	entities['8249'] = '&lsaquo;'
	entities['8250'] = '&rsaquo;'
	entities['8364'] = '&euro;'
  }

  if (useQuoteStyle !== 'ENT_NOQUOTES') {
    entities['34'] = '&quot;'
  }
  if (useQuoteStyle === 'ENT_QUOTES') {
    entities['39'] = '&#39;'
  }

  // ascii decimals to real symbols
  for (decimal in entities) {
    if (entities.hasOwnProperty(decimal)) {
      hash_map[String.fromCharCode(decimal)] = entities[decimal]
    }
  }

  return hash_map
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
    symbol = ''

  string = string == null ? '' : string + ''

  if (!hash_map) {
    return false
  }

  if (quote_style && quote_style === 'ENT_QUOTES') {
    hash_map["'"] = '&#039;'
  }

  double_encode = double_encode == null || !!double_encode

  var regex = new RegExp('&(?:#\\d+|#x[\\da-f]+|[a-zA-Z][\\da-z]*);|[' +
    Object.keys(hash_map)
    .join('')
    // replace regexp special chars
    .replace(/([()[\]{}\-.*+?^$|\/\\])/g, '\\$1') + ']',
    'g')

  return string.replace(regex, function (ent) {
    if (ent.length > 1) {
      return double_encode ? hash_map['&'] + ent.substr(1) : ent
    }

    return hash_map[ent]
  })
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
    entity = ''
  tmp_str = string.toString()

  if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
    return false
  }

  // fix &amp; problem
  // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
  delete (hash_map['&'])
  hash_map['&'] = '&amp;'

  for (symbol in hash_map) {
    entity = hash_map[symbol]
    tmp_str = tmp_str.split(entity)
      .join(symbol)
  }
  tmp_str = tmp_str.split('&#039;')
    .join("'")

  return tmp_str
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

function formatBytes(bytes, decimals) {
   if(bytes == 0) return '0 Bytes';
   var k = 1024,
       dm = decimals <= 0 ? 0 : decimals || 2,
       sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
       i = Math.floor(Math.log(bytes) / Math.log(k));
   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function makeID() {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

	for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));

	return text;
}