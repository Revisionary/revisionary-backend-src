// When document is ready
$(function() {


	// TEST: CONTENT EDITOR PLUGIN
	$(".edit-content").popline();


	// Detect cursor moves
	$(window).mousemove(function(e) {

		// Iframe offset
		offset = $('#the-page').offset();

		containerX = e.clientX - offset.left;
		containerY = e.clientY - offset.top;

	});


	// Tab opener
	$('.opener').click(function(e) {
		toggleTab( $(this) );

		e.preventDefault();
		return false;
	});


	// Comment Opener !!!
	$('.pins-list .pin-title').click(function(e) {
		$(this).toggleClass('close');

		e.preventDefault();
		return false;
	});


	// Cursor activator
	activator.click(function(e) {
		toggleCursorActive();

		e.preventDefault();
		return false;
	});


	// Pin mode selector
	pinTypeSelector.click(function(e) {
		togglePinTypeSelector();

		e.preventDefault();
		return false;
	});


	// Pin mode change
	$('.pin-types a').click(function(e) {

		var selectedPinType = $(this).children('pin').data('pin-type');
		var selectedPinPrivate = $(this).children('pin').data('pin-private');

		switchPinType(selectedPinType, selectedPinPrivate);

		e.preventDefault();
		return false;
	});



	// Iframe Fit to the screen
	var maxWidth  = $('iframe').width();
	var maxHeight = $('iframe').height();
	$('.iframe-container').css({ width: maxWidth, height: maxHeight });

	$(window).resize(function(evt) {

	    var $window = $(window);
	    var width = $window.width() - 26; // -(10+10) for the borders
	    var height = $window.height() - 26; // -(10+10) for the borders

	    // early exit
	    if(width >= maxWidth && height >= maxHeight) {
	        $('iframe').css({'-webkit-transform': ''});
	        //$('.iframe-container').css({ width: '', height: '' });
	        return;
	    }

	    iframeScale = Math.min(width/maxWidth, height/maxHeight);
	    iframeWidth = maxWidth * iframeScale;
		iframeHeight = maxHeight * iframeScale;


	    $('iframe').css({'-webkit-transform': 'scale(' + iframeScale + ')'});
	    $('.iframe-container').css({ width: iframeWidth, height: iframeHeight });


	    // Re-Locate the pins
	    relocatePins();

	}).resize();



	// Continue scrolling on any pin or the pin window
	$(document).on('mousewheel', '#pins > pin', function(e) {

		var scrollDelta = e.originalEvent.wheelDelta;
		iframe.scrollTop( scrollOffset_top - scrollDelta );

	});



	// Close pin window
	$('#pin-window .close-button').click(function(e) {

		closePinWindow();

		e.preventDefault();

	});



	// PIN DRAG & DROP
	hoveringPin = false;
	var pinClicked = false;
	var pinDragging = false;
	$(document).on('mouseover', '#pins > pin', function(e) {

		console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type"), $(this).attr("data-pin-private"), $(this).attr("data-pin-complete"), $(this).attr("data-revisionary-index") );


		hoveringPin = true;


		// Reset the pin opacity
		if (!pinWindowOpen) $('#pins > pin').css('opacity', '');


		// Hide the cursor
		cursor.stop().fadeOut();


		// Outline the element if this is a live pin
		if ($(this).attr("data-pin-type") == "live") {

			var hoveringPinPrivate = $(this).attr("data-pin-private");

			iframe.find('body *').css('outline', 'none');
			iframe.find('body *[data-revisionary-index="'+ $(this).attr("data-revisionary-index") +'"]').css('outline', '2px dashed ' + (hoveringPinPrivate == 1 ? '#FC0FB3' : 'green'), 'important');

		}


		e.preventDefault();

	}).on('mousedown', '#pins > pin', function(e) {

		console.log('CLICKED TO A PIN!');


		focusedPin = $(this);
		pinClicked = true;
		pinDragging = false;


		// Disable the iframe
		$('#the-page').css('pointer-events', 'none');


		e.preventDefault();

	}).on('mousemove', function(e) {


		if (pinClicked) {

			var focused_pin_id = focusedPin.attr('data-pin-id');

			// If not on DB, don't move it
			if ( $.isNumeric(focused_pin_id) ) {

				console.log('PIN IS MOVING!');

				pinDragging = true;

				var pinSize = 45;
				var pos_x = containerX - pinSize/2;
				var pos_y = containerY - pinSize/2;

				relocatePins(focusedPin, pos_x, pos_y);

			}

			e.preventDefault();
		}

	}).on('mouseup', function(e) {


		if (pinClicked) {

			console.log('PIN UN-CLICKED!');

			var pinWasDragging = pinDragging;
			pinClicked = false;
			pinDragging = false;
			hoveringPin = true;


			// Enable the iframe
			$('#the-page').css('pointer-events', 'auto');


			// Show the pin window if not dragging
		    if (!pinWasDragging) {


				togglePinWindow(focusedPin.attr('data-pin-x'), focusedPin.attr('data-pin-y'), focusedPin.attr('data-pin-id'));

		        console.log('TOGGLE THE PIN WINDOW !!!');


		    } else {


			    // Update the pin location on DB
			    console.log('Update the new pin location on DB');

				// Start the process
				var relocateProcessID = newProcess();

			    $.post(ajax_url, {
					'type'	  	 : 'pin-relocate',
					'nonce'	  	 : pin_nonce,
					'pin_ID'	 : focusedPin.attr('data-pin-id'),
					'pin_x' 	 : focusedPin.attr('data-pin-x'),
					'pin_y' 	 : focusedPin.attr('data-pin-y')
				}, function(result){

					// Finish the process
					endProcess(relocateProcessID);

					console.log(result.data);

				}, 'json');


		    }


			focusedPin = null;

			e.preventDefault();
		}

	}).on('mouseout', '#pins > pin', function(e) {

		console.log('MOUSE OUT FROM PIN!', pinDragging);

		hoveringPin = false;


		// Show the cursor
		if (cursorActive && !pinDragging) cursor.stop().fadeIn();


		e.preventDefault();

	});



});


// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});


});


function runTheInspector() {

	// WHEN IFRAME HAS LOADED
	$('iframe').on('load', function(){


		// Iframe element
	    iframe = $('iframe').contents();



		// CURSOR WORKS
		// Close Pin Mode pinTypeSelector - If on revise mode !!!
		toggleCursorActive(false, true);

		// Update the cursor number with the existing pins
		changePinNumber(currentPinNumber);



		// PAGE INTERACTIONS
		// Hide the loading overlay
		$('#loading').fadeOut();

		// Close all the tabs
		$('.opener').each(function() {

			toggleTab( $(this), true );

		});



		// Body class !!! ???
		$('body').addClass('ready');


	    // Show current process on loading overlay with progress bar
	    // ...


    	// Remove the loading overlay ???
		// $('#loading-overlay').fadeOut();


	    // Update the title
		if ( iframe.find('title').length ) {
			$('title').text( "Revise Page: " + iframe.find('title').text() );
		}


		// MOUSE ACTIONS
	    iframe.on('mousemove', function(e) { // Detect the mouse moves in frame


			// Iframe offset - NO NEED FOR NOW !!!
			offset = $('#the-page').offset();

		    // Mouse coordinates according to the screen - NO NEED FOR NOW !!!
		    screenX = e.clientX * iframeScale + offset.left;
		    screenY = e.clientY * iframeScale + offset.top;

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



		    // Focused Element
	        focused_element = $(e.target);
	        focused_element_index = focused_element.attr('data-revisionary-index');
	        focused_element_has_index = focused_element_index != null ? true : false;
	        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
	        focused_element_children = focused_element.children();
	        focused_element_grand_children = focused_element_children.children();
	        focused_element_pin = $('#pins > pin[data-pin-type="live"][data-revisionary-index="'+ focused_element_index +'"]');



			// Work only if cursor is active
			if (cursorActive && !hoveringPin) {


				// Re-Focus if only child element has no child and has content
				if (
					focused_element_text == "" && // Focused element has no content
					focused_element_children.length == 1 && // Has only one child
					focused_element_grand_children.length == 0 && // No grand child
					focused_element_children.first().text().trim() != "" // Grand child should have content
				) {

					// Re-Focus to the child element
					focused_element = focused_element_children.first();
			        focused_element_index = focused_element.attr('data-revisionary-index');
			        focused_element_has_index = focused_element_index != null ? true : false;
			        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
			        focused_element_children = focused_element.children();
			        focused_element_grand_children = focused_element_children.children();
					focused_element_pin = $('#pins > pin[data-revisionary-index="'+ focused_element_index +'"]');

				}



				// See what am I focusing
				console.log( focused_element.prop("tagName"), focused_element_index );



				// Check element text editable: <p>Lorem ipsum dolor sit amet...
				hoveringText = false;
		        focused_element_editable = false;
		        focused_element_html_editable = false;
		        if (
			        easy_html_elements.indexOf( focused_element.prop("tagName") ) != -1 && // In easy HTML elements?
		        	focused_element_text.trim() != "" && // If not empty
		        	focused_element.html() != "&nbsp;" && // If really not empty
		        	focused_element_children.length == 0 // If doesn't have any child
		        ) {

					hoveringText = true;
					focused_element_editable = true; // Obviously Text Editable
					focused_element_html_editable = true;
					console.log( '* Obviously Text Editable: ' + focused_element.prop("tagName") );
					console.log( 'Focused Element Text: ' + focused_element_text );

				}



				// Check element image editable: <img src="#">...
				hoveringImage = false;
		        if ( focused_element.prop("tagName") == "IMG" ) {

					hoveringImage = true;
					focused_element_editable = true; // Obviously Image Editable
					console.log( '* Obviously Image Editable: ' + focused_element.prop("tagName") );
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
						console.log( '* Text Editable (No Grand Child): ' + focused_element.prop("tagName") );
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
						console.log( '* Text Editable (One Grand Child): ' + focused_element.prop("tagName") );
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



				// Check if it doesn't have any element index: <p data-revisionary-index="16">...
				if (focused_element_editable && !focused_element_has_index) {

					focused_element_editable = false;
					focused_element_html_editable = false;
					console.log( '* Element editable but NO INDEX: ' + focused_element.prop("tagName") );

				}



				// Clean Other Outlines
				iframe.find('body *').css('outline', '');

				// Reset the pin opacity
				$('#pins > pin').css('opacity', '');



				// LIVE REACTIONS
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


			// If cursor
			if (cursorActive) {


				if (focused_element_has_live_pin) {


					// Open the pin window !!!
					openPinWindow(focused_element_pin.attr('data-pin-x'), focused_element_pin.attr('data-pin-y'), focused_element_pin.attr('data-pin-id'));


				} else {

					// Add a pin and open a pin window
					putPin(e.pageX, e.pageY);

				}



			}


			// Prevent clicking something?
			e.preventDefault();
			return false;

		}).on('scroll', function(e) { // Detect the scroll to re-position pins

			scrollOffset_top = $(this).scrollTop();
			scrollOffset_left = $(this).scrollLeft();


			scrollX = scrollOffset_left * iframeScale;
			scrollY = scrollOffset_top * iframeScale;


		    // Re-Locate the pins
		    relocatePins();

		});


		$(window).on('resize', function(e) { // Detect the scroll to re-position pins

			scrollOffset_top = iframe.scrollTop();
			scrollOffset_left = iframe.scrollLeft();


			scrollX = scrollOffset_left * iframeScale;
			scrollY = scrollOffset_top * iframeScale;


		    // Re-Locate the pins
		    relocatePins();

		});



	});


}


// FUNCTION: Tab Toggler
function toggleTab(tab, forceClose = false) {

	var speed = 500;

	var container = tab.parent();
	var containerWidth = container.outerWidth();
	var sideElement = tab.parent().parent();


	if ( sideElement.hasClass('top-left') || sideElement.hasClass('bottom-left') ) {

		if (container.hasClass('open') || forceClose) {

			sideElement.animate({
				left: -containerWidth,
			}, speed, function() {
				container.removeClass('open');
			});

		} else {

			sideElement.animate({
				left: 0,
			}, speed, function() {
				container.addClass('open');
			});

		}

	} else {

		if (container.hasClass('open') || forceClose) {

			sideElement.animate({
				right: -containerWidth,
			}, speed, function() {
				container.removeClass('open');
			});

		} else {

			sideElement.animate({
				right: 0,
			}, speed, function() {
				container.addClass('open');
			});

		}

	}

}


// FUNCTION: Color the element
function outline(element, private_pin) {

	element.css('outline', '2px dashed ' + (private_pin == 1 ? '#FC0FB3' : 'green'), 'important');

}


// FUNCTION: Switch to a different pin mode
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


// FUNCTION: Switch to a different cursor mode
function switchCursorType(cursorType) {

	log(cursorType);

	cursor.attr('data-pin-type', cursorType).attr('data-pin-private', currentPinPrivate);
	currentCursorType = cursorType;

}


// FUNCTION: Toggle Inspect Mode
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
		if (!cursorVisible) cursor.fadeIn();

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


// FUNCTION: Toggle Pin Mode Selector
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


// FUNCTION: Change the pin number on cursor
function changePinNumber(pinNumber) {

	cursor.text(pinNumber);
	currentPinNumber = pinNumber;

}


// FUNCTION: Re-Locate Pins
function relocatePins(pin_selector = null, x = null, y = null) {


	var scrolled_window_x, scrolled_window_y;


	if ( pin_selector ) {

	    var scrolled_pin_x = x > 0 ? x : 0;
	    var scrolled_pin_y = y > 0 ? y : 0;

	    scrolled_pin_x = x < iframeWidth - 45 ? scrolled_pin_x : iframeWidth - 45;
	    scrolled_pin_y = y < iframeHeight - 45 ? scrolled_pin_y : iframeHeight - 45;


	    pin_selector.attr('data-pin-x', (scrolled_pin_x / iframeScale) + scrollOffset_left );
		pin_selector.attr('data-pin-y', (scrolled_pin_y / iframeScale) + scrollOffset_top );

		pinWindow.attr('data-pin-x', (scrolled_pin_x / iframeScale) + scrollOffset_left);
		pinWindow.attr('data-pin-y', (scrolled_pin_y / iframeScale) + scrollOffset_top);


		pin_selector.css('left', scrolled_pin_x + "px");
		pin_selector.css('top', scrolled_pin_y + "px");


		// Pin Window location
		scrolled_window_x = scrolled_pin_x + 50;
		scrolled_window_y = scrolled_pin_y + 50;

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


		// Current pin window location
		var window_x = parseInt(pinWindow.attr('data-pin-x')) * iframeScale;
		var window_y = parseInt(pinWindow.attr('data-pin-y')) * iframeScale;

	    scrolled_window_x = window_x - scrollX + 50;
	    scrolled_window_y = window_y - scrollY + 50;

	}


	var spaceWidth = (iframeWidth + ($(window).width() - iframeWidth) / 2) - 15;
	var spaceHeight = (iframeHeight + ($(window).height() - iframeHeight) / 2) - 15;


    new_scrolled_window_x = scrolled_window_x < spaceWidth - pinWindowWidth ? scrolled_window_x : spaceWidth - pinWindowWidth;
    new_scrolled_window_y = scrolled_window_y < spaceHeight - pinWindowHeight ? scrolled_window_y : spaceHeight - pinWindowHeight;

    console.log('SCROLLED WINDOW: ', scrolled_window_x, scrolled_window_y);


	// Change the side of the window
	if (
		scrolled_window_x >= spaceWidth - pinWindowWidth &&
		scrolled_window_y >= spaceHeight - pinWindowHeight
	) {
		console.log('OUCH!');

		new_scrolled_window_x = scrolled_window_x - pinWindowWidth - 55;

	}


	//
	if (scrolled_window_y > new_scrolled_window_y + pinWindowHeight) {

		console.log('GOODBYE!');
		new_scrolled_window_y = scrolled_window_y - pinWindowHeight;

	}


	// Relocate the pin window
	pinWindow.css('left', new_scrolled_window_x + "px");
	pinWindow.css('top', new_scrolled_window_y + "px");

}


// FUNCTION: Put a pin to cordinates
function putPin(pinX, pinY) {

	// Put it just on the pointer point
	pinX = pinX - 45/2;
	pinY = pinY - 45/2;


	// Disable the inspector
	toggleCursorActive(true); // Force deactivate


	console.log('Put the Pin #' + currentPinNumber, pinX, pinY, currentCursorType, currentPinPrivate, focused_element_index);


	var temporaryPinID = makeID();


	// Add the pin to the DOM
	$('#pins').append(
		newPinTemplate(pinX, pinY, temporaryPinID, user_ID)
	);



	// Open the pin window
	openPinWindow(pinX, pinY, temporaryPinID);




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


		// Finish the process
		endProcess(newPinProcessID);

	}, 'json');




	// Re-Locate the pins
	relocatePins();


	// Increase the pin number
	changePinNumber(currentPinNumber + 1);

}


// FUNCTION: Open the pin window
function openPinWindow(pin_x, pin_y, pin_ID) {


	closePinWindow();


	// Previous state of cursor
	if (!pinWindowWasOpen) cursorWasActive = cursorActive;


	// Disable the inspector
	toggleCursorActive(true); // Force deactivate


	// Add the pin window data !!!
	pinWindow.attr('data-pin-x', pin_x);
	pinWindow.attr('data-pin-y', pin_y);
	pinWindow.attr('data-pin-id', pin_ID);


	// Relocate the window
	relocatePins();


	// Reveal it
	pinWindow.addClass('active');
	pinWindowOpen = true;


	// Show the pin
	$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');


	// Update the pin window sizes
	pinWindowWidth = pinWindow.outerWidth();
	pinWindowHeight = pinWindow.outerHeight();

}


// FUNCTION: Close pin window
function closePinWindow() {

	// Previous state of window
	pinWindowWasOpen = pinWindowOpen;

	// Hide it
	pinWindow.removeClass('active');
	pinWindowOpen = false;


	// Reset the pin opacity
	$('#pins > pin').css('opacity', '');


	if (cursorWasActive) toggleCursorActive(false, true); // Force Open

}


// FUNCTION: Toggle pin window
function togglePinWindow(pin_x, pin_y, pin_ID) {

	if (pinWindowOpen && pinWindow.attr('data-pin-id') == pin_ID) closePinWindow();
	else openPinWindow(pin_x, pin_y, pin_ID);

}


// TEMPLATE: Pin template
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
			data-revisionary-index="'+focused_element_index+'" \
			style="top: '+pin_y+'px; left: '+pin_x+'px;" \
		>'+currentPinNumber+'</pin> \
	';

}



// FUNCTION: ID Creator
function makeID() {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

	for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));

	return text;
}


// Console log shortcut
function log(log, arg1) {
	//console.log(log);
}