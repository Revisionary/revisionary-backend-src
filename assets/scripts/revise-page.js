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

		console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type"), $(this).attr("data-pin-private"), $(this).attr("data-pin-complete"), $(this).attr("data-element-index") );


		hoveringPin = true;


		// Hide the cursor
		cursor.stop().fadeOut();


		// Outline the element if this is a live pin
		if ($(this).attr("data-pin-type") == "live") {

			var hoveringPinPrivate = $(this).attr("data-pin-private");

			iframe.find('body *').css('outline', 'none');
			iframe.find('body *[data-element-index="'+ $(this).attr("data-element-index") +'"]').css('outline', '2px dashed ' + (hoveringPinPrivate == 1 ? '#FC0FB3' : 'green'), 'important');

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
					'type'	  	 : 'relocate-pin',
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


	/*
		// INDEX WORKS - NOT NOW !!! For now, only the static content will be editable!
		// Count the indexed elements
		indexCount = iframe.find('body *[data-element-index]').length;
		elementCount = iframe.find('body *').length;


		// Check if the file already indexed
		if ( elementCount > indexCount ) {

			console.log('PAGE NEEDS TO BE INDEXED', iframe.find('body *').length, iframe.find('body *[data-element-index]').length);

			// Add all the HTML element indexes
			iframe.find('body *:not([data-element-index])').each(function(i) {

				var elementIndex = i;

				if (i <= indexCount) elementIndex = indexCount + 1

				$(this).attr('data-element-index', elementIndex);
				console.log(i + ': New element index added: ', $(this).prop("tagName"), elementIndex);

				indexCount++;

			});

			// Send this state of the "body" tag to DB? or File? to save !!! Security Issue?!!!
			var newBodyHTML = iframe.find('body').prop('outerHTML');

			console.log('Send the BODY');


			// Start the indexing process
			var indexingProcessID = newProcess();

			$.post(ajax_url, {
				'type'	  	 : 'add-element-indexed-html',
				'nonce'	  	 : element_index_nonce,
				'page_ID'	 : page_ID,
				'version_number' : version_number,
				'bodyHTML'	 : newBodyHTML
			}, function(result){

				console.log(result.data);

				// Finish the process
				endProcess(indexingProcessID);

			}, 'json');

		}
*/



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
	        focused_element_index = focused_element.attr('data-element-index'); // !!!
	        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
	        focused_element_children = focused_element.children();
	        focused_element_grand_children = focused_element_children.children();
	        focused_element_pin = $('#pins > pin[data-element-index="'+ focused_element_index +'"]');



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
			        focused_element_index = focused_element.attr('data-element-index'); // !!!
			        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
			        focused_element_children = focused_element.children();
			        focused_element_grand_children = focused_element_children.children();
					focused_element_pin = $('#pins > pin[data-element-index="'+ focused_element_index +'"]');

				}



				// See what am I focusing
				console.log( focused_element.prop("tagName"), focused_element_index );
				// UNIQUE SELECTORS !!!
				//console.log( focused_element.getSelector()[0] );



				// Check element text editable
				hoveringText = false;
		        focused_element_editable = false;
		        focused_element_html_editable = false;
		        if (
				        (
				        	focused_element.prop("tagName") == "A" ||
				        	focused_element.prop("tagName") == "B" ||
				        	focused_element.prop("tagName") == "STRONG" ||
				        	focused_element.prop("tagName") == "SMALL" ||
				        	focused_element.prop("tagName") == "TEXTAREA" ||
				        	focused_element.prop("tagName") == "LABEL" ||
				        	focused_element.prop("tagName") == "BUTTON" ||
				        	focused_element.prop("tagName") == "TIME" ||
				        	focused_element.prop("tagName") == "DATE" ||
				        	focused_element.prop("tagName") == "ADDRESS" ||
				        	focused_element.prop("tagName") == "P" ||
				        	focused_element.prop("tagName") == "DIV" ||
				        	focused_element.prop("tagName") == "SPAN" ||
				        	focused_element.prop("tagName") == "LI" ||
				        	focused_element.prop("tagName") == "H1" ||
				        	focused_element.prop("tagName") == "H2" ||
				        	focused_element.prop("tagName") == "H3" ||
				        	focused_element.prop("tagName") == "H4" ||
				        	focused_element.prop("tagName") == "H5" ||
				        	focused_element.prop("tagName") == "H6"
			        	)
			        	&&
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



				// Check element image editable
				hoveringImage = false;
		        if ( focused_element.prop("tagName") == "IMG" ) {

					hoveringImage = true;
					focused_element_editable = true; // Obviously Image Editable
					console.log( '* Obviously Image Editable: ' + focused_element.prop("tagName") );
					console.log( 'Focused Element Image: ' + focused_element.attr('src') );

				}



				// Check if element has children but doesn't have grand children
				if (
					focused_element_children.length > 0 && // Has child
					focused_element_grand_children.length == 0 && // No grand child
					focused_element_text.trim() != "" && // And, also have to have text
					focused_element.html() != "&nbsp;" // And, also have to have text
				) {


					// Also check the children's tagname
					var hardToEdit = false;
					focused_element_children.each(function() {

						if (
							$(this).prop("tagName") != "BR" &&
				        	$(this).prop("tagName") != "A" &&
				        	$(this).prop("tagName") != "B" &&
				        	$(this).prop("tagName") != "STRONG" &&
				        	$(this).prop("tagName") != "SMALL" &&
				        	$(this).prop("tagName") != "TEXTAREA" &&
				        	$(this).prop("tagName") != "LABEL" &&
				        	$(this).prop("tagName") != "BUTTON" &&
				        	$(this).prop("tagName") != "TIME" &&
				        	$(this).prop("tagName") != "DATE" &&
				        	$(this).prop("tagName") != "ADDRESS" &&
				        	$(this).prop("tagName") != "P" &&
				        	$(this).prop("tagName") != "SPAN" &&
				        	$(this).prop("tagName") != "LI" &&
				        	$(this).prop("tagName") != "H1" &&
				        	$(this).prop("tagName") != "H2" &&
				        	$(this).prop("tagName") != "H3" &&
				        	$(this).prop("tagName") != "H4" &&
				        	$(this).prop("tagName") != "H5" &&
				        	$(this).prop("tagName") != "H6"
						) hardToEdit = true;

					});

					if (!hardToEdit) {

						hoveringText = true;
						focused_element_editable = true;
						focused_element_html_editable = true;
						console.log( '* Text Editable (No Grand Child): ' + focused_element.prop("tagName") );
						console.log( 'Focused Element Text: ' + focused_element_text );

					}

				}



				// Check the submit buttons
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



				// Clean Other Outlines
				iframe.find('body *').css('outline', '');

				// Reset the pin opacity
				pins.css('opacity', '');



				// LIVE REACTIONS
				if (currentPinType == "live") {

					if (focused_element_editable) {

						switchCursorType('live');
						focused_element.css('outline', '2px dashed ' + (currentPinPrivate == 1 ? '#FC0FB3' : 'green'), 'important');


						// Check if it already has a pin
						if ( focused_element_pin.length ) {

							focused_element_has_live_pin = true;


							// Point to the pin
							$('#pins > pin:not([data-element-index="'+ focused_element_index +'"])').css('opacity', '0.2');


							// If this has a private pin, make the outline pink
							if ( focused_element_pin.attr('data-pin-private') == 1 ) {

								focused_element.css('outline', '2px dashed #FC0FB3', 'important');

							}


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

	// Increase the pin number
	changePinNumber(currentPinNumber + 1);

}


// Console log shortcut
function log(log, arg1) {
	//console.log(log);
}