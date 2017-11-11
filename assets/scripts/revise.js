var activator, cursorActive, currentPinMode
var pinModeSelector, pinModeSelectorOpen
var cursor, currentCursorMode, currentPinNumber;
var mouseInTheFrame;
var hoveringText, hoveringImage;
var iframe;

// Global Focus Variables
var focused_element,
	focused_element_children,
	focused_element_grand_children,
	focused_element_index,
	focused_element_text,
	focused_pin_type,
	focused_element_has_pin,
	focused_element_has_live_pin,
	focused_element_editable,
	focused_element_html_editable;



// VARIABLES
// Activator Pin
activator = $('.inspect-activator').children('pin');
cursorActive = activator.hasClass('active');

// Pin Mode Selector
pinModeSelector = $('.pin-mode-selector');
pinModeSelectorOpen = pinModeSelector.parent().hasClass('selector-open');

// Detect initial cursor mode
cursor = $('.mouse-cursor');
currentPinMode = activator.data('pin-mode');
currentPinNumber = 1;

// Iframe ???
mouseInTheFrame = false;

// Hovers
hoveringText = false;
hoveringImage = false;
hoveringButton = false;

/*
MIGHT BE NEEDED

addMode = false;
pinWindowOpen = false;
currentText = "";
currentDevice = "Desktop";
*/


// Inspect activator
activator.click(function(e) {
	toggleCursorActive();

	e.preventDefault();
	return false;
});

// Pin mode selector
pinModeSelector.click(function(e) {
	togglePinModeSelector();

	e.preventDefault();
	return false;
});

// Pin mode change
$('.pin-modes a').click(function(e) {

	var selectedPinMode = $(this).children('pin').data('pin-mode');

	switchPinMode(selectedPinMode);

	e.preventDefault();
	return false;
});


function runTheInspector() {

	// WHEN IFRAME HAS LOADED
	$('iframe').on('load', function(){


		// Iframe element
	    iframe = $('iframe').contents();


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
			$('.mouse-cursor').css({
				left:  e.screenX,
				top:   e.screenY - 70
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
			if (cursorActive) {


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
				if (currentPinMode == "live" || currentPinMode == "private") {

					if (focused_element_editable) {

						switchCursorMode(currentPinMode == "private" ? 'private-live' : 'live');
						focused_element.css('outline', '2px dashed ' + (currentPinMode == "private" ? '#FC0FB3' : 'green'), 'important');

					} else {

						switchCursorMode(currentPinMode == "private" ? 'private' : 'standard');

					}

				}



			} // If cursor active


		}).on('click', function(e) { // Detect the mouse clicks in frame

			// If cursor
			if (cursorActive) {


			}

			// Prevent clicking something?
			e.preventDefault();
			return false;

		});



	});




/*
	// Mouse cursor capture !!!
	$(document).on('mousemove', function(e){

		//log( mouseInTheFrame );

	});
*/

}


// FUNCTION: Switch to a different pin mode
function switchPinMode(pinMode) {

	log(pinMode);

	activator.attr('data-pin-mode', pinMode);

	if (pinMode == "live")
		switchCursorMode('standard');
	else
		switchCursorMode(pinMode);


	currentPinMode = pinMode;

	if (pinModeSelectorOpen) togglePinModeSelector(true);

}


// FUNCTION: Switch to a different cursor mode
function switchCursorMode(cursorMode) {

	log(cursorMode);

	cursor.attr('data-pin-mode', cursorMode);
	currentCursorMode = cursorMode;

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
		if (pinModeSelectorOpen) togglePinModeSelector(true);

	}

}


// FUNCTION: Toggle Pin Mode Selector
function togglePinModeSelector(forceClose = false) {

	if (pinModeSelectorOpen || forceClose) {

		pinModeSelector.removeClass('open');
		pinModeSelector.parent().removeClass('selector-open');
		$('#pin-mode-selector').fadeOut();
		pinModeSelectorOpen = false;
		if (!cursorActive) toggleCursorActive();

	} else {

		pinModeSelector.addClass('open');
		pinModeSelector.parent().addClass('selector-open');
		$('#pin-mode-selector').fadeIn();
		pinModeSelectorOpen = true;
		if (cursorActive) toggleCursorActive(true);

	}

}