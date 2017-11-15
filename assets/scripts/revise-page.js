// When document is ready
$(function() {


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

	$(window).resize(function(evt) {

	    var $window = $(window);
	    var width = $window.width() - 20; // -20 for the borders
	    var height = $window.height() - 20; // -20 for the borders

	    // early exit
	    if(width >= maxWidth && height >= maxHeight) {
	        $('iframe').css({'-webkit-transform': ''});
	        $('.iframe-container').css({ width: '', height: '' });
	        return;
	    }

	    iframeScale = Math.min(width/maxWidth, height/maxHeight);

	    $('iframe').css({'-webkit-transform': 'scale(' + iframeScale + ')'});
	    $('.iframe-container').css({ width: maxWidth * iframeScale, height: maxHeight * iframeScale });


	    // Re-Locate the pins
	    relocatePins();

	}).resize();



	// Hovering a pin?
	hoveringPin = false;
	$('#pins > pin').on('mouseover', function() {

		hoveringPin = true;
		console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type") );

	}).on('mouseout', function() {

		hoveringPin = false;
		console.log( 'Un-Hovering a Pin: ' + $(this).attr("data-pin-type") );

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



		// Close Pin Mode pinTypeSelector - If on revise mode !!!
		toggleCursorActive(false, true);

		// Hide the loading overlay
		$('#loading').fadeOut();

		// Close all the tabs
		$('.opener').each(function() {

			toggleTab( $(this), true );

		});

		// Body class
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


		    // Focused Element
	        focused_element = $(e.target);
	        focused_element_index = focused_element.attr('data-element-index'); // !!!
	        focused_element_text = focused_element.clone().children().remove().end().text(); // Gives only text, without inner html
	        focused_element_children = focused_element.children();
	        focused_element_grand_children = focused_element_children.children();



			// Follow the mouse cursor
			var offset = $('#the-page').offset();
			$('.mouse-cursor').css({
				left:  e.clientX * iframeScale + offset.left,
				top:   e.clientY * iframeScale + offset.top
			});


			// Disable cursor if on some places ??
/*
			if (
				cursorActive &&

				(
					focusedElement.hasClass('revisionary-comment-window') ||
					focusedElement.hasClass('more-info') ||
					focusedElement.parents('.revisionary-comment-window').length ||
					focusedElement.hasClass('revisionary-pin') ||
					focusedElement.parents('.revisionary-pin').length
				)
			) {

				// Deactivate the inspect mode
				toggleCursorActive(true);

			} else {

				// Reactivate the inspect mode
				toggleCursorActive(false, true);

			}
*/


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

				}



				// See what am I focusing
				console.log( focused_element.prop("tagName") );



				// Check element text editable
				hoveringText = false;
		        focused_element_editable = false;
		        focused_element_html_editable = false;
		        if (
				        (
				        	focused_element.prop("tagName") == "A" ||
				        	focused_element.prop("tagName") == "B" ||
				        	focused_element.prop("tagName") == "STRONG" ||
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

					hoveringText = true;
					focused_element_editable = true;
					focused_element_html_editable = true;
					console.log( '* Text Editable (No Grand Child): ' + focused_element.prop("tagName") );
					console.log( 'Focused Element Text: ' + focused_element_text );

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



				// Live reactions
				if (currentPinType == "live") {

					if (focused_element_editable) {

						switchCursorType('live');
						focused_element.css('outline', '2px dashed ' + (currentPinPrivate == 1 ? '#FC0FB3' : 'green'), 'important');

					} else {

						switchCursorType('standard');

					}

				}



			} // If cursor active


		}).on('click', function(e) { // Detect the mouse clicks in frame


			// If cursor
			if (cursorActive) {


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




/*
	// Mouse cursor capture !!!
	$(document).on('mousemove', function(e){

		//log( mouseInTheFrame );

	});
*/

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
function relocatePins() {


    $('#pins > pin').each(function() {

	    var pin = $(this);
	    var pin_x = pin.attr('data-pin-x');
	    var pin_y = pin.attr('data-pin-y');


	    var scrolled_pin_x = parseInt(pin_x) * iframeScale - scrollOffset_left * iframeScale;
	    var scrolled_pin_y = parseInt(pin_y) * iframeScale - scrollOffset_top * iframeScale;


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


// FUNCTION: Put a pin to cordinates
function putPin(pinX, pinY, pinType) {

	// Disable the inspector
	toggleCursorActive(true);


	console.log('Put A Pin NOW!', pinX, pinY, pinType);



}


// Console log shortcut
function log(log, arg1) {
	//console.log(log);
}