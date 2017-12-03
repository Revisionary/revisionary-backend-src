// When document is ready
$(function() {


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

	    $('iframe').css({'-webkit-transform': 'scale(' + iframeScale + ')'});
	    $('.iframe-container').css({ width: maxWidth * iframeScale, height: maxHeight * iframeScale });


	    // Re-Locate the pins
	    relocatePins();

	}).resize();



	// PIN DRAG & DROP
	hoveringPin = false;
	var pinClicked = false;
	var pinDragging = false;
	$(document).on('mouseover', '#pins > pin', function(e) {

		console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type"), $(this).attr("data-pin-private"), $(this).attr("data-pin-complete"), $(this).attr("data-revisionary-index") );


		hoveringPin = true;


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

			console.log('PIN IS MOVING!');

			pinDragging = true;

			var pinSize = 45;
			var pos_x = containerX - pinSize/2;
			var pos_y = containerY - pinSize/2;

			relocatePins(focusedPin, pos_x, pos_y);

		}


		e.preventDefault();

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


		        console.log('TOGGLE THE PIN WINDOW!');


		    } else {


			    // Update the pin location on DB !!!
			    console.log('Update the new pin location on DB!');

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

		}

		e.preventDefault();

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
	        focused_element_pin = $('#pins > pin[data-revisionary-index="'+ focused_element_index +'"]');



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
				// UNIQUE SELECTORS ? !!!
				//console.log( focused_element.getSelector()[0] );



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
				if (currentPinType == "live") {

					if (focused_element_editable) {

						switchCursorType('live');
						outline(focused_element, currentPinPrivate);


						// Check if it already has a pin
						if ( focused_element_pin.length ) {

							focused_element_has_live_pin = true;


							// Point to the pin
							$('#pins > pin:not([data-revisionary-index="'+ focused_element_index +'"])').css('opacity', '0.2');


							// Color the element that has a pin according to the pin type
							outline(focused_element, focused_element_pin.attr('data-pin-private'));


							console.log('This element already has a live pin.');

						}



					} else {

						switchCursorType('standard');

					}

				}



			} // If cursor active


		}).on('click', function(e) { // Detect the mouse clicks in frame


			// If cursor
			if (cursorActive) {


				// Add a pin and open a pin window !!!
				putPin(e.pageX, e.pageY, currentCursorType);


			}


			// Prevent clicking something?
			e.preventDefault();
			return false;

		}).on('scroll', function(e) { // Detect the scroll to re-position pins

			scrollOffset_top = $(this).scrollTop();
			scrollOffset_left = $(this).scrollLeft();


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


// FUNCTION: Re-Locate Pins
function relocatePins(pin_selector = null, x = null, y = null) {

	var pins = pin_selector || $('#pins > pin');

    pins.each(function() {

	    var pin = $(this);
	    var pin_x = x || pin.attr('data-pin-x');
	    var pin_y = y || pin.attr('data-pin-y');


	    var scrollX = scrollOffset_left * iframeScale;
	    var scrollY = scrollOffset_top * iframeScale;


	    var pinX = parseInt(pin_x) * iframeScale;
	    var pinY = parseInt(pin_y) * iframeScale;


	    var scrolled_pin_x = pinX - scrollX;
	    var scrolled_pin_y = pinY - scrollY;


	    if (x && y) {
		    scrolled_pin_x = x;
		    scrolled_pin_y = y;

		    pin.attr('data-pin-x', (scrolled_pin_x / iframeScale) + scrollOffset_left );
			pin.attr('data-pin-y', (scrolled_pin_y / iframeScale) + scrollOffset_top );

	    }


	    pin.css('left', scrolled_pin_x + "px");
	    pin.css('top', scrolled_pin_y + "px");


    });

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

}


// FUNCTION: Switch to a different cursor mode
function switchCursorType(cursorType) {

	log(cursorType);

	cursor.attr('data-pin-type', cursorType).attr('data-pin-private', currentPinPrivate);
	currentCursorType = cursorType;

}


// FUNCTION: Toggle Inspect Mode
function toggleCursorActive(forceClose = false, forceOpen = false) {

	if ( (cursorActive || forceClose) && !forceOpen ) {

		// Deactivate
		activator.removeClass('active');

		// Hide the cursor
		cursor.fadeOut();

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
		cursor.fadeIn();

		// Hide the original cursor
		iframe.find('body, body *').css('cursor', 'none', 'important');

		// Disable all the links
	    // ...


		cursorActive = true;
		if (pinTypeSelectorOpen) togglePinTypeSelector(true); // Force Close

	}

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


// FUNCTION: Put a pin to cordinates
function putPin(pinX, pinY, pinType) {

	// Disable the inspector
	toggleCursorActive(true); // Force deactivate


	console.log('Put the Pin #' + currentPinNumber, pinX, pinY, pinType, focused_element_index);


	// Add the pin
	$('#pins').append(
		pinTemplate(pinType, currentPinPrivate, '0', 'USER-ID', 'PIN-ID', pinX, pinY, focused_element_index, currentPinNumber)
	);


	// Re-Locate the pins
	relocatePins();


	// Increase the pin number
	changePinNumber(currentPinNumber + 1);

}


// FUNCTION: Color the element
function outline(element, private_pin) {

	element.css('outline', '2px dashed ' + (private_pin == 1 ? '#FC0FB3' : 'green'), 'important');

}


// TEMPLATE: Pin template
function pinTemplate(pin_type, pin_private, pin_complete, user_ID, pin_ID, pin_x, pin_y, pin_element_index, currentPinNumber) {

	pin_x = pin_x - 45/2;
	pin_y = pin_y - 45/2;

	return '\
		<pin \
			class="pin big" \
			data-pin-type="'+pin_type+'" \
			data-pin-private="'+pin_private+'" \
			data-pin-complete="'+pin_complete+'" \
			data-pin-user-id="'+user_ID+'" \
			data-pin-id="'+pin_ID+'" \
			data-pin-x="'+pin_x+'" \
			data-pin-y="'+pin_y+'" \
			data-revisionary-index="'+pin_element_index+'" \
			style="top: '+pin_y+'px; left: '+pin_x+'px;" \
		>'+currentPinNumber+'</pin> \
	';

}


// Console log shortcut
function log(log, arg1) {
	//console.log(log);
}