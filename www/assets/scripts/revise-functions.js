// FUNCTIONS:
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

			setTimeout(function() { // Does not work sometimes, and needs improvement !!!

				iframe.scrollTop(oldScrollOffset_top);
				iframe.scrollLeft(oldScrollOffset_left);

				console.log('REDIRECTED', page_redirected, oldScrollOffset_top, oldScrollOffset_left);

				oldScrollOffset_top = oldScrollOffset_left = 0;

				page_redirected = false;

			}, 2000);

		}

		if ( !canAccessIFrame( $(this) ) ) {

			oldScrollOffset_top = scrollOffset_top;
			oldScrollOffset_left = scrollOffset_left;

			if (!page_redirected) window.frames["the-page"].location = page_URL;

			page_redirected = true; console.log('REDIRECTED', page_redirected, scrollOffset_top, scrollOffset_left);

		}



		// CURSOR WORKS:
		// Close Pin Mode pinTypeSelector - If on revise mode !!!
		toggleCursorActive(false, true);

		// Update the cursor number with the existing pins
		changePinNumber(currentPinNumber);



		// MODIFICATIONS:
		$(modifications).each(function(i, modification) {

			console.log(i, modification);


			// Find the element
			var element = iframe.find('[data-revisionary-index='+modification.element_index+']');


			// Add edited status
			element.attr('data-revisionary-edited', "0");


			// If the type is HTML content change
			if ( modification.modification_type == "html" ) {

				// Record the old HTML
				var oldHtml = element.html();
				modifications[i].original = htmlentities(oldHtml, "ENT_QUOTES"); console.log('OLD', modifications[i].original);


				// Apply the modification
				var newHTML = html_entity_decode(modification.modification); console.log('NEW', newHTML);
				element.html( newHTML ).attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");

				// Update the pin status
				$('#pins > pin[data-pin-id="'+modification.pin_ID+'"]').attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");

			}


		});



		// PAGE INTERACTIONS:
		// Hide the loading overlay
		$('#loading').fadeOut();

		// Close all the tabs
		$('.opener').each(function() {

			toggleTab( $(this), true );

		});


		// Body class !!! ???
		$('body').addClass('ready');


	    // Update the title
		if ( iframe.find('title').length ) {
			$('title').text( "Revise Page: " + iframe.find('title').text() );
		}



		// MOUSE ACTIONS:
	    iframe.on('mousemove', function(e) { // Detect the mouse moves in frame


		    // Mouse coordinates according to the iframe container
		    containerX = e.clientX * iframeScale;
		    containerY = e.clientY * iframeScale;


		    // Follow the mouse cursor
			$('.mouse-cursor').css({
				left:  containerX,
				top:   containerY
			});



/*
			console.log('Screen: ', screenX, screenY);
			console.log('Container: ', containerX, containerY);
*/



			// FOCUSING:
		    // Focused Element is the mouse pointed element as default
	        focused_element = $(e.target);

	        focused_element_index = focused_element.attr('data-revisionary-index');
	        focused_element_has_index = focused_element_index != null ? true : false;
	        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
			focused_element_html = focused_element.html();
	        focused_element_children = focused_element.children();
	        focused_element_grand_children = focused_element_children.children();
			focused_element_pin = $('#pins > pin[data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"]');
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


					console.log('REFOCUS - Only child element has no child and has content: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class'));

				}


				// Re-focus to the edited element if this is child of it: <p data-edited="1" focused><b>Lorem
				if (focused_element_edited_parents.length) {

					// Re-focus to the parent edited element
					focused_element = focused_element_edited_parents.first();


					console.log('REFOCUS - Already edited closest parent: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class'));

				}


				// Re-focus to the edited element if this is parent of it: <p><b data-edited="1" focused>Lorem</b> <span>ipsum dolor</span></p>
				if (
					focused_element_has_edited_child == 1
				) {

					// Re-focus to the parent edited element
					focused_element = focused_element.find('[data-revisionary-index][data-revisionary-edited]');


					console.log('REFOCUS - Already edited only child element: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class'));

				}


				// Update refocused sub elements
		        focused_element_index = focused_element.attr('data-revisionary-index');
		        focused_element_has_index = focused_element_index != null ? true : false;
		        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
				focused_element_html = focused_element.html();
		        focused_element_children = focused_element.children();
		        focused_element_grand_children = focused_element_children.children();
				focused_element_pin = $('#pins > pin[data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"]');
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
					console.log( '* Obviously Text Editable: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
					console.log( 'Focused Element Text: ' + focused_element_text );

				}


				// Check element image editable: <img src="#">...
				hoveringImage = false;
		        if ( focused_element.prop("tagName") == "IMG" ) {

					hoveringImage = true;
					focused_element_editable = true; // Obviously Image Editable
					console.log( '* Obviously Image Editable: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
					console.log( 'Focused Element Image: ' + focused_element.attr('src') );

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
						console.log( '* Text Editable (No Grand Child): ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
						console.log( 'Focused Element Text: ' + focused_element_text );

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
						console.log( '* Text Editable (One Grand Child): ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );
						console.log( 'Focused Element Text: ' + focused_element_text );

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
					console.log( '* Button Editable: ' + focused_element.prop("tagName") );
					console.log( 'Focused Button Text: ' + focused_element.attr('value') );

				}



				// PREVENTIONS
				// Check if it doesn't have any element index: <p data-revisionary-index="16">...
				if (focused_element_editable && !focused_element_has_index) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					console.log( '* Element editable but NO INDEX: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );

				}


				// If focused element has edited child, don't focus it
				if (focused_element_has_edited_child > 1 ) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					console.log( '* Element editable but there are edited #'+focused_element_has_edited_child+' children: ' + focused_element.prop("tagName") + '.' + focused_element.attr('class') );

				}



/*
				// See what am I focusing
				console.log("CURRENT FOCUSED: ", focused_element.prop("tagName"), focused_element_index );
				console.log("CURRENT FOCUSED EDITABLE: ", focused_element_editable );
*/



				// Clean Other Outlines
				iframe.find('body *').css('outline', '');

				// Reset the pin opacity
				$('#pins > pin').css('opacity', '');



				// LIVE REACTIONS:
				focused_element_has_live_pin = false;
				if (currentPinType == "live") {

					if (focused_element_editable) {

						switchCursorType('live');
						outline(focused_element, currentPinPrivate);


						// Check if it already has a pin
						if ( focused_element_pin.length ) {

							focused_element_has_live_pin = true;


							// Point to the pin
							$('#pins > pin:not([data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"])').css('opacity', '0.2');


							// Color the element that has a pin according to the pin type
							outline(focused_element, focused_element_pin.attr('data-pin-private'));


							console.log('This element already has a live pin.');

						}


					} else {

						// If not editable, switch back to the standard pin
						switchCursorType('standard');

					}

				} // If current pin type is 'live'



			} // If cursor active


		}).on('click', function(e) { // Detect the mouse clicks in frame


			// If cursor is active
			if (cursorActive) {

				// If focused element has a live pin
				if (focused_element_has_live_pin) {

					// Open the pin window
					openPinWindow(focused_element_pin.attr('data-pin-x'), focused_element_pin.attr('data-pin-y'), focused_element_pin.attr('data-pin-id'));

				} else {

					// Add a pin and open a pin window
					putPin(e.pageX, e.pageY);

				}


			}


			// Prevent clicking something
			e.preventDefault();
			return false;

		}).on('scroll', function(e) { // Detect the scroll to re-position pins

			console.log('SCROLLIIIIIIIING');


		    // Re-Locate the pins
		    relocatePins();

		});


		$(window).on('resize', function(e) { // Detect the scroll to re-position pins

			console.log('RESIZIIIIIIIING');


		    // Re-Locate the pins
		    relocatePins();

		});


		// Relocate Pins
		relocatePins();

	});


}


// Tab Toggler
function toggleTab(tab, forceClose = false) {

	var sideElement = tab.parent().parent();


	if (sideElement.hasClass('open') || forceClose) {

		sideElement.removeClass('open');

	} else {

		sideElement.addClass('open');

	}


}


// Color the element
function outline(element, private_pin) {

	element.css('outline', '2px dashed ' + (private_pin == 1 ? '#FC0FB3' : '#7ED321'), 'important');

}


// Switch to a different pin mode
function switchPinType(pinType, pinPrivate) {

	log('Switched Pin Type: ', pinType);
	log('Switched Pin Private? ', pinPrivate);


	currentPinType = pinType;
	currentPinPrivate = pinPrivate;


	// Change the activator color
	activator.attr('data-pin-type', currentPinType).attr('data-pin-private', currentPinPrivate);


	// Change the cursor color
	switchCursorType(pinType == "live" ? 'standard' : pinType, currentPinPrivate);


	// Close the type selector
	if (pinTypeSelectorOpen) togglePinTypeSelector(true);


	// Close the open pin window
	if (pinWindowOpen) closePinWindow();

}


// Switch to a different cursor mode
function switchCursorType(cursorType) {

	log(cursorType);

	cursor.attr('data-pin-type', cursorType).attr('data-pin-private', currentPinPrivate);
	currentCursorType = cursorType;

}


// Toggle Inspect Mode
function toggleCursorActive(forceClose = false, forceOpen = false) {

	cursor.stop();
	var cursorVisible = cursor.is(":visible");

	if ( (cursorActive || forceClose) && !forceOpen ) {

		// Deactivate
		activator.removeClass('active');

		// Hide the cursor
		if (cursorVisible) cursor.fadeOut();

		// Show the original cursor
		iframe.find('body, body *').css('cursor', '', '');

		// Enable all the links
	    // ...


		cursorActive = false;
		focused_element = null;

	} else {


		// Activate
		activator.addClass('active');

		// Show the cursor
		if (!cursorVisible && !pinWindowOpen) cursor.fadeIn();

		// Hide the original cursor
		iframe.find('body, body *').css('cursor', 'none', 'important');

		// Disable all the links
	    // ...


		cursorActive = true;
		if (pinTypeSelectorOpen) togglePinTypeSelector(true); // Force Close

	}


	// Close the open pin window
	if (pinWindowOpen) closePinWindow();

}


// Toggle Pin Mode Selector
function togglePinTypeSelector(forceClose = false) {

	if (pinTypeSelectorOpen || forceClose) {

		pinTypeSelector.removeClass('open');
		pinTypeSelector.parent().removeClass('selector-open');
		$('#pin-type-selector').fadeOut();
		pinTypeSelectorOpen = false;
		if (!cursorActive) toggleCursorActive();

	} else {

		pinTypeSelector.addClass('open');
		pinTypeSelector.parent().addClass('selector-open');
		$('#pin-type-selector').fadeIn();
		pinTypeSelectorOpen = true;
		if (cursorActive) toggleCursorActive(true);

	}

}


// Change the pin number on cursor
function changePinNumber(pinNumber) {

	cursor.text(pinNumber);
	currentPinNumber = pinNumber;

}


// Re-Locate Pins
function relocatePins(pin_selector = null, x = null, y = null) {

	// Update the values
	offset = $('#the-page').offset();

	scrollOffset_top = iframe.scrollTop();
	scrollOffset_left = iframe.scrollLeft();

	scrollX = scrollOffset_left * iframeScale;
	scrollY = scrollOffset_top * iframeScale;



	if ( pin_selector ) {

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
		if ( pin_selector.attr('data-pin-id') == pinWindow.attr('data-pin-id') ) {

			pinWindow.attr('data-pin-x', realPinX);
			pinWindow.attr('data-pin-y', realPinY);

		}


	} else {


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
	var spaceHeight = offset.top + iframeHeight + offset.top - 15;


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
	pinWindow.css('left', new_scrolled_window_x + "px");
	pinWindow.css('top', new_scrolled_window_y + "px");

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



// Get Up-To-Date Pins !!!
function getPins() {

}



// Put a pin to cordinates
function putPin(pinX, pinY) {

	// Put it just on the pointer point
	pinX = pinX - 45/2;
	pinY = pinY - 45/2;


	console.log('Put the Pin #' + currentPinNumber, pinX, pinY, currentCursorType, currentPinPrivate, focused_element_index);


	var temporaryPinID = makeID();


	// Add the pin to the DOM
	$('#pins').append(
		newPinTemplate(pinX, pinY, temporaryPinID, user_ID)
	);



	// Open the pin window
	openPinWindow(pinX, pinY, temporaryPinID, true);



    // Add pin to the DB
    console.log('Add pin to the DB !!!');


	// Start the process
	var newPinProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	 		: 'pin-add',
		'nonce'	  	 		: pin_nonce,
		'pin_x' 	 		: pinX,
		'pin_y' 	 		: pinY,
		'pin_type' 	 		: currentCursorType,
		'pin_private'		: currentPinPrivate,
		'pin_element_index' : focused_element_index,
		'pin_version_ID'	: version_ID,
	}, function(result){

		console.log(result.data);

		var realPinID = result.data.real_pin_ID;

		console.log('REAL PIN ID: '+realPinID);


		// Update the pin ID !!!
		$('#pins > pin[data-pin-id="'+temporaryPinID+'"]').attr('data-pin-id', realPinID);
		$('#pin-window').attr('data-pin-id', realPinID);


		if (currentCursorType == "live") {

			var editedElement = iframe.find('[data-revisionary-index="'+focused_element_index+'"]');

			// Add edited status to the DOM
			editedElement.attr('data-revisionary-edited', "0");


			// Add to the modifications list
			modifications[modifications.length] = {
				element_index: focused_element_index,
				pin_ID: realPinID,
				modification_type: "html",
				modification: null,
				original: htmlentities( editedElement.html(), "ENT_QUOTES")
			};

		}

		// Remove the loading text on pin window
		$('#pin-window').removeClass('loading');


		// Finish the process
		endProcess(newPinProcessID);

	}, 'json');




	// Re-Locate the pins
	relocatePins();


	// Increase the pin number
	changePinNumber(currentPinNumber + 1);

}


// Open the pin window
function openPinWindow(pin_x, pin_y, pin_ID, firstTime) {


	var thePin = $('#pins > pin[data-pin-id="'+ pin_ID +'"]');
	var thePinType = thePin.attr('data-pin-type');
	var thePinPrivate = thePin.attr('data-pin-private');
	var thePinComplete = thePin.attr('data-pin-complete');
	var theIndex = thePin.attr('data-revisionary-index');
	var thePinText = thePinPrivate == '1' ? 'PRIVATE COMMENT' : 'ONLY COMMENT';
	var thePinModified = thePin.attr('data-revisionary-edited');
	var thePinShowingChanges = thePin.attr('data-revisionary-showing-changes');
	var originalContent = "";


	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;


	// Previous state of cursor
	if (!pinWindowWasOpen) cursorWasActive = cursorActive;


	// Close the previous window
	closePinWindow();


	// Disable the inspector
	toggleCursorActive(true); // Force deactivate


	// Add the pin window data !!!
	pinWindow.attr('data-pin-type', thePinType);
	pinWindow.attr('data-pin-private', thePinPrivate);
	pinWindow.attr('data-pin-complete', thePinComplete);
	pinWindow.attr('data-pin-x', thePin.attr('data-pin-x'));
	pinWindow.attr('data-pin-y', thePin.attr('data-pin-y'));
	pinWindow.attr('data-pin-id', pin_ID);
	pinWindow.attr('data-revisionary-edited', thePinModified);
	pinWindow.attr('data-revisionary-showing-changes', thePinShowingChanges);
	pinWindow.attr('data-revisionary-index', theIndex);


	// Update the pin type section
	pinWindow.find('pin.chosen-pin').attr('data-pin-type', thePinType);
	pinWindow.find('pin.chosen-pin').attr('data-pin-private', thePinPrivate);
	pinWindow.find('pin.chosen-pin + span > span').text(thePinText);


	// Arrange the convertor options
	pinWindow.find('ul.type-convertor > li').show();
	pinWindow.find('ul.type-convertor > li > a > pin[data-pin-type="'+thePinType+'"][data-pin-private="'+thePinPrivate+'"]').parent().parent().hide();

	// Also remove the live option on comments
	if (thePinType == "standard")
		pinWindow.find('ul.type-convertor > li > a > pin[data-pin-type="live"][data-pin-private="0"]').parent().parent().hide();


	// Hide the editor
	pinWindow.find('.content-editor').hide();


	// If on 'Live' mode
	if (thePinType == 'live') {

		thePinText = thePinPrivate == '1' ? 'PRIVATE LIVE' : 'LIVE EDIT';


		// Update the pin type section
		pinWindow.find('pin.chosen-pin + span > span').text(thePinText);


		// Show the content editor
		pinWindow.find('.content-editor').show();



		var origContent = iframe.find('[data-revisionary-index="'+ theIndex +'"]:not([data-revisionary-showing-changes])');



		// MODIFICATION FINDER
		var modification = modifications.find(function(modification) {
			return modification.pin_ID == pin_ID ? true : false;
		});

		// Show the changed HTML content on the editor
		if (modification && modification.modification != null)
			pinWindow.find('.edit-content.changes').html( html_entity_decode (modification.modification) );

		// Add the original HTML content
		if (modification && modification.original != null)
			originalContent = html_entity_decode (modification.original);

		// If it's untouched DOM
		if ( origContent.length ) {
			originalContent = origContent.html();

			// Default change editor
			pinWindow.find('.edit-content.changes').html( origContent.html() );
		}


		// Update the original content
		pinWindow.find('.edit-content.original').html( originalContent );

	}


	// Relocate the window
	relocatePins();


	// Reveal it
	pinWindow.addClass('active');
	pinWindowOpen = true;


	// If the new pin registered, remove the loading message
	if ( $.isNumeric(pin_ID) )
		pinWindow.removeClass('loading');


	// COMMENTS
	// If this is an already registered pin
	if (!firstTime) getComments(pin_ID); // Bring the comments
	// If new pin added
	else $('.pin-comments').html('<div class="xl-center">Add your comment:</div>'); // Write a message


	// Clean the existing comment
	$('#pin-window input.comment-input').val('');



	// Show the pin
	$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');


	// Update the pin window sizes
	pinWindowWidth = pinWindow.outerWidth();
	pinWindowHeight = pinWindow.outerHeight();

}


// Close pin window
function closePinWindow() {

	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;

	// Hide it
	pinWindow.removeClass('active');
	pinWindowOpen = false;


	// Add the loading text after loading
	pinWindow.addClass('loading');


	// Reset the pin opacity
	$('#pins > pin').css('opacity', '');


	if (cursorWasActive) toggleCursorActive(false, true); // Force Open

}


// Remove a pin
function removePin(pin_ID) {


    // Add pin to the DB
    console.log('Remove the pin #' + pin_ID + ' from DB!!');


	// Start the process
	var newPinProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	: 'pin-remove',
		'nonce'	  	: pin_nonce,
		'pin_ID'	: pin_ID
	}, function(result){

		console.log(result.data);


		// Close the pin window
		closePinWindow();


		// Revert the changes
		var modification = modifications.find(function(modification) {
			return modification.pin_ID == pin_ID ? true : false;
		});
		var modificationIndex = modifications.indexOf(modification);

		console.log(modification);

		if (modification) {

			var modifiedElement = iframe.find('[data-revisionary-index="'+ modification.element_index +'"]');

			// Add the original HTML content
			if (modification.original != null)
				modifiedElement.html( html_entity_decode (modification.original) );

			// Remove the edited status from DOM element
			modifiedElement.removeAttr('data-revisionary-edited').removeAttr('data-revisionary-showing-changes');

			// Delete from the list
			modifications.splice(modificationIndex, 1);


		}



		// Remove the pin from DOM
		$('#pins > pin[data-pin-id="'+pin_ID+'"]').remove();


		// Re-Index the pin counts
		reindexPins();


		// Finish the process
		endProcess(newPinProcessID);

	}, 'json');


}


// Complete/Incomplete a pin
function completePin(pin_ID, complete) {


    // Add pin to the DB
    console.log( (complete ? 'Complete' : 'Incomplete') +' the pin #' + pin_ID + ' on DB!!');


	// Start the process
	var newPinProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	 		: 'pin-complete',
		'complete' 	 		: (complete ? 'complete' : 'incomplete'),
		'nonce'	  	 		: pin_nonce,
		'pin_ID'			: pin_ID
	}, function(result){

		console.log(result.data);

		// Update the pin status
		$('#pins > pin[data-pin-id="'+pin_ID+'"]').attr('data-pin-complete', (complete ? '1' : '0'));


		// Update the pin window status
		pinWindow.attr('data-pin-complete', (complete ? '1' : '0'));


		// Finish the process
		endProcess(newPinProcessID);

	}, 'json');


}


// Save a modification
function saveModification(pin_ID, modification, modification_type = "html") {


    // Add pin to the DB
    console.log( 'Save modification for the pin #' + pin_ID + ' on DB!!');


	// Start the process
	var newPinProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	 		: 'pin-modify',
		'modification' 	 	: modification,
		'modification_type'	: modification_type,
		'nonce'	  	 		: pin_nonce,
		'pin_ID'			: pin_ID
	}, function(result){

		console.log(result.data);

		// Update the pin status
		$('#pins > pin[data-pin-id="'+pin_ID+'"]').attr('data-revisionary-edited', "1");


		// Update the pin window status
		pinWindow.attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");


		// Finish the process
		endProcess(newPinProcessID);

	}, 'json');


}


// Toggle content edits
function toggleContentEdit(pin_ID) {

	var isShowingChanges = pinWindow.attr('data-revisionary-showing-changes') == "1" ? true : false;


	// MODIFICATION FINDER
	var modification = modifications.find(function(modification) {
		return modification.pin_ID == pin_ID ? true : false;
	});


	if (modification) {

		// Change the content on DOM
		iframe.find('[data-revisionary-index="'+modification.element_index+'"]')
			.html( html_entity_decode( (isShowingChanges ? modification.original : modification.modification) ) )
			.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1") );

		// Update the Pin Window and Pin info
		pinWindow.attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));
		$('#pins > pin[data-pin-id="'+pin_ID+'"]').attr('data-revisionary-showing-changes', (isShowingChanges ? "0" : "1"));

	}

}


// Toggle pin window
function togglePinWindow(pin_x, pin_y, pin_ID) {

	if (pinWindowOpen && pinWindow.attr('data-pin-id') == pin_ID) closePinWindow();
	else openPinWindow(pin_x, pin_y, pin_ID);

}


// Get Comments
function getComments(pin_ID) {


	// Remove dummy comments and add loading indicator
	$('#pin-window .pin-comments').html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Comments are loading...</span></div>');


	// Disable comment sender
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Send the Ajax request
    $.post(ajax_url, {
		'type'	  	: 'comments-get',
		'nonce'	  	: pin_nonce,
		'pin_ID'	: pin_ID
	}, function(result){

		var comments = result.comments;

		console.log(result.data);
		console.log('COMMENTS: ', comments);


		// Clean the loading
		$('#pin-window .pin-comments').html('<div class="xl-center">No comments yet.</div>');


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
				if (previousTime == timeSince(date)) { console.log('TIME SINCE', timeSince(date));
					sameTime = true;
				}

			}

			// Clean it first
			if ( i == 0 ) $('#pin-window .pin-comments').html('');


			// Append the comments
			$('#pin-window .pin-comments').append(
				commentTemplate(comment, directionLeft, hide, sameTime)
			);


			// Record the previous commenter
			previousDirectionLeft = directionLeft;
			directionLeft = !directionLeft;
			previousCommenter = comment.user_ID;
			previousTime = timeSince(date);

		});


		// Scroll down to the latest comment
		$('#pin-window .pin-comments').scrollTop(9999);


		// Enable comment sender
		$('#pin-window #comment-sender input').prop('disabled', false);

	}, 'json');


}


// Send a comment
function sendComment(pin_ID, message) {

	console.log('Sending this message: ', message);


	// Disable the inputs
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Start the process
	var newCommentProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	: 'comment-add',
		'nonce'	  	: pin_nonce,
		'pin_ID'	: pin_ID,
		'message'	: message
	}, function(result){

		console.log(result.data);


		// List the comments
		getComments(pin_ID);


		// Finish the process
		endProcess(newCommentProcessID);


		// Enable the inputs
		$('#pin-window #comment-sender input').prop('disabled', false);


		// Clean the text in the message box and refocus
		$('#pin-window #comment-sender input.comment-input').val('').focus();


		console.log('Message SENT: ', message);

	}, 'json');

}


// Delete a comment
function deleteComment(pin_ID, comment_ID) {

	console.log('Deleting comment #', comment_ID);


	// Disable the inputs
	$('#pin-window #comment-sender input').prop('disabled', true);


	// Start the process
	var deleteCommentProcessID = newProcess();

    $.post(ajax_url, {
		'type'	  	 : 'comment-delete',
		'nonce'	  	 : pin_nonce,
		'pin_ID'	 : pin_ID,
		'comment_ID' : comment_ID
	}, function(result){

		console.log(result.data);


		// List the comments
		getComments(pin_ID);


		// Finish the process
		endProcess(deleteCommentProcessID);


		// Enable the inputs
		$('#pin-window #comment-sender input').prop('disabled', false);


		// Clean the text in the message box and refocus
		$('#pin-window #comment-sender input.comment-input').val('').focus();


		console.log('Comment #', comment_ID, ' DELETED');

	}, 'json');

}



// TEMPLATES:
// Pin template
function newPinTemplate(pin_x, pin_y, pin_ID, user_ID) {

	return '\
		<pin \
			class="pin big" \
			data-pin-type="'+currentCursorType+'" \
			data-pin-private="'+currentPinPrivate+'" \
			data-pin-complete="0" \
			data-pin-user-id="'+user_ID+'" \
			data-pin-id="'+pin_ID+'" \
			data-pin-x="'+pin_x+'" \
			data-pin-y="'+pin_y+'" \
			data-revisionary-edited="0" \
			data-revisionary-showing-changes="1" \
			data-revisionary-index="'+focused_element_index+'" \
			style="top: '+pin_y+'px; left: '+pin_x+'px;" \
		>'+currentPinNumber+'</pin> \
	';

}


// Comment template
function commentTemplate(comment, left = true, hide = false, sameTime = false) {

	var date = new Date(comment.comment_modified);
	var picture = comment.user_picture;
	var hasPic = picture == null ? false : true;
	var printPic = hasPic ? " style='background-image: url(/assets/cache/user-"+ comment.user_ID +"/"+ comment.user_picture +");'" : "";
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
function get_html_translation_table (table, quote_style) {
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
  }

  if (useQuoteStyle !== 'ENT_NOQUOTES') {
    entities['34'] = '&quot;'
  }
  if (useQuoteStyle === 'ENT_QUOTES') {
    entities['39'] = '&#39;'
  }
  entities['60'] = '&lt;'
  entities['62'] = '&gt;'

  // ascii decimals to real symbols
  for (decimal in entities) {
    if (entities.hasOwnProperty(decimal)) {
      hash_map[String.fromCharCode(decimal)] = entities[decimal]
    }
  }

  return hash_map
}

function htmlentities (string, quote_style, charset, double_encode) {
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

function html_entity_decode (string, quote_style) {
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

function timeSince(date) {

  var seconds = Math.floor((new Date() - date) / 1000);

  var interval = Math.floor(seconds / 31536000);

  if (interval > 1) {
    return interval + " years";
  }
  interval = Math.floor(seconds / 2592000);
  if (interval > 1) {
    return interval + " months";
  }
  interval = Math.floor(seconds / 86400);
  if (interval > 1) {
    return interval + " days";
  }
  interval = Math.floor(seconds / 3600);
  if (interval > 1) {
    return interval + " hours";
  }
  interval = Math.floor(seconds / 60);
  if (interval > 1) {
    return interval + " minutes";
  }
  //return Math.floor(seconds) + " seconds";
  return "about a minute";
}

function makeID() {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

	for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));

	return text;
}

function log(log, arg1) {
	//console.log(log, arg1);
}