// When document is ready
$(function() {

	// Prevent clicking '#' links
	$('a[href="#"]').click(function(e) {
		e.preventDefault();
	});


	// Detect cursor moves
	$(window).mousemove(function(e) {

		// Iframe offset
		offset = $('#the-page').offset();

		containerX = e.clientX - offset.left;
		containerY = e.clientY - offset.top;


		//console.log(hoveringPin);

	});


	// Tab opener
	$('.opener').click(function(e) {
		toggleTab( $(this) );

		e.preventDefault();
		return false;
	});


	// Comment Opener
	$(document).on('click', '.pins-list .pin-title', function(e) {

		$(this).toggleClass('close');

		var pinComments = $(this).next();
		var thePin = $(this).prev().children('pin');
		var pin_ID = thePin.attr('data-pin-id');


		if (!$(this).hasClass('close')) {

			console.log('Getting comments for: ', pin_ID);

			getComments(pin_ID, pinComments);

		}



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
	var maxWidth  = iframeWidth = $('iframe').width();
	var maxHeight = iframeHeight = $('iframe').height();
	$('.iframe-container').css({ width: maxWidth, height: maxHeight });

	$(window).resize(function(e) {

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


	}).resize();



	// Continue scrolling on any pin or the pin window
	$(document).on('mousewheel', '#pins > pin', function(e) {

		var scrollDelta = e.originalEvent.wheelDelta;
		iframe.scrollTop( scrollOffset_top - scrollDelta );

	});


	// Close pin window
	$('#pin-window .close-button').click(function(e) {

		if (pinWindowOpen) closePinWindow();

		e.preventDefault();

	});


	// Remove Pin
	$('#pin-window .remove-pin').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');

		// Delete it from DB
		if ( confirm('Are you sure you want to delete this pin and its modifications and comments?') )
			removePin(pin_ID);

		e.preventDefault();

	});


	// Complete Pin
	$('#pin-window .pin-complete > a').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var isComplete = pinWindow.attr('data-pin-complete') == "1" ? true : false;

		// Toggle Complete it on DB
		//console.log(pin_ID, !isComplete);
		completePin(pin_ID, !isComplete);

		e.preventDefault();

	});


	// Comment Sender
	$('#comment-sender').submit(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var message = $(this).find('input.comment-input').val();

		// Add it from DB
		sendComment(pin_ID, message);

		e.preventDefault();
	});


	// Delete Comment
	$(document).on('click', '.delete-comment', function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var comment_ID = $(this).attr('data-comment-id');

		// Delete it from DB if confirmed
		if ( confirm('Are you sure you want to delete this comment?') )
			deleteComment(pin_ID, comment_ID);

		e.preventDefault();
	});


	// Toggle original content
	$('.edits-switch').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');

		toggleChange(pin_ID);

		e.preventDefault();

	});


	// Pin window content changes
	var doChange;
	$(document).on('DOMSubtreeModified', '#pin-window.active .content-editor .edit-content', function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var elementIndex = pinWindow.attr('data-revisionary-index');
		var changes = $(this).html();
		var changedElement = iframeElement(elementIndex);
		var changedElementOriginal = changedElement.html();




		//console.log('REGISTERED CHANGES', changes);



		// Stop the auto-refresh
		stopAutoRefresh();


		// Apply the change
		changedElement.html(changes).attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");



	    // Update from the Pins global
		var pin = Pins.find(function(pin) { return pin.pin_ID == pin_ID ? true : false; });
		var pinIndex = Pins.indexOf(pin);

		Pins[pinIndex].pin_modification = htmlentities(changes, "ENT_QUOTES");


		// Remove unsent job
		if (doChange) clearTimeout(doChange);

		// Send changes to DB after 1 second
		doChange = setTimeout(function(){

			saveChange(pin_ID, changes);

		}, 1000);

		//console.log('Content changed.');

	});


	// Uploader
	$('#filePhoto').change(function() {

		var maxSize = $(this).data('max-size');


	    var reader = new FileReader();
	    reader.onload = function(event) {



			var pin_ID = pinWindow.attr('data-pin-id');
			var elementIndex = pinWindow.attr('data-revisionary-index');
			var imageSrc = event.target.result;
			var changedElement = iframeElement(elementIndex);
			var changedElementOriginal = changedElement.attr('src');



			//console.log('REGISTERED CHANGES', changes);



			// Stop the auto-refresh
			stopAutoRefresh();


			// Apply the change
			$('.uploader img').attr('src', imageSrc);
			changedElement.attr('src', imageSrc).attr('srcset', '').attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");
			pinWindow.attr('data-revisionary-edited', "1");



			// Send changes to DB
			saveChange(pin_ID, imageSrc);


			//console.log('Content changed.');

	    }


		// If a file selected
        if ( $(this).get(0).files.length ) {



            var fileSize = $(this).get(0).files[0].size; // in bytes
            if (fileSize > maxSize) {

                alert('File size is more than ' + formatBytes(maxSize));
                return false;

            } else {

                console.log('File size is correct - '+formatBytes(fileSize)+', no more than '+formatBytes(maxSize));
	        	reader.readAsDataURL($(this).get(0).files[0]);

            }


		// If no file selected
        } else {

		    console.log('NO FILE');
            return false;
        }


		console.log('CHANGED');

	});


	// Select File
	$('.select-file').click(function(e) {


		$('#filePhoto').click();


		e.preventDefault();

	});


	// Remove Image
	$('.remove-image').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var element_index = parseInt(pinWindow.attr('data-revisionary-index'));


		// Remove the image on this element
		removeImage(pin_ID, element_index);


		e.preventDefault();

	});


	// Convert Pin
	$('.type-convertor > li > a').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var element_index = parseInt(pinWindow.attr('data-revisionary-index'));
		var targetPin = $(this).children('pin');


		// Remove the image on this element
		convertPin(pin_ID, targetPin);


		e.preventDefault();

	});


	// Hovering a pin from the pins list tab
	$(document).on('mouseover', '#revise-sections .pins-list > .pin', function(e) {

		var pin_ID = $(this).find('pin').attr('data-pin-id');

		$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');

		e.preventDefault();
	}).on('mouseout', '#revise-sections .pins-list > .pin', function(e) {

		$('#pins > pin').css('opacity', '');

		e.preventDefault();
	});


	// Filtering the pins
	$('.pins-filter > a').click(function(e) {


		console.log('SHOW THE PINS: ', filter);


		var filter = $(this).data('filter');


		$('.pins-filter > a').removeClass('selected');
		$(this).addClass('selected');


		$('#pins, .pins-list').attr('data-filter', filter);


		var queryString = filter == "all" ? '' : '?filter='+ filter;
		if (history.pushState) {
		    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + queryString;
		    window.history.pushState({path:newurl},'',newurl);
		}


		e.preventDefault();
	});


	// PIN HOVERING
	hoveringPin = false;
	var pinClicked = false;
	$(document).on('mouseover', '#pins > pin', function(e) {

		//console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type"), $(this).attr("data-pin-private"), $(this).attr("data-pin-complete"), $(this).attr("data-revisionary-index") );


		hoveringPin = true;


		// Reset the pin opacity
		if (!pinWindowOpen) $('#pins > pin').css('opacity', '');


		// Hide the cursor
		cursor.stop().fadeOut();


		// Clear all outlines
		iframeElement('body *').css('outline', '');


		// Outline the element if this is a live pin
		if ($(this).attr("data-pin-type") == "live") {

			var hoveringPinPrivate = $(this).attr("data-pin-private");

			iframeElement('body *').css('outline', 'none');
			iframeElement('body *[data-revisionary-index="'+ $(this).attr("data-revisionary-index") +'"]').css('outline', '2px dashed ' + (hoveringPinPrivate == 1 ? '#FC0FB3' : '#7ED321'), 'important');

		}


		e.preventDefault();

	}).on('mousedown', '#pins > pin', function(e) {

		//console.log('CLICKED TO A PIN!');


		focusedPin = $(this);
		pinClicked = true;
		pinDragging = false;


		// Disable the iframe
		$('#the-page').css('pointer-events', 'none');


		e.preventDefault();

	}).on('mousemove', function(e) {


		if (pinClicked) {


			// If not on DB, don't move it
			if ( !focusedPin.is('[temporary]') ) {

				//pinDragging = true;

				//relocatePins(focusedPin, pos_x, pos_y);

				//console.log('PIN IS MOVING!', pos_x, pos_y);

			}

			e.preventDefault();
		}

	}).on('mouseup', function(e) {


		if (pinClicked) {

			//console.log('PIN UN-CLICKED!');


			pinClicked = false;
			hoveringPin = true;


			// Enable the iframe
			$('#the-page').css('pointer-events', 'auto');


			// Toggle the pin window even if just a little dragging
		    if (!pinDragging)
				togglePinWindow(focusedPin.attr('data-pin-x'), focusedPin.attr('data-pin-y'), focusedPin.attr('data-pin-id'));


			focusedPin = null;

			e.preventDefault();
		}

	}).on('mouseout', '#pins > pin', function(e) {

		//console.log('MOUSE OUT FROM PIN!', pinDragging);

		hoveringPin = false;

		iframeElement('body *').css('outline', '');


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


	// CONTENT EDITOR PLUGIN
	$(".edit-content[contenteditable='true']").popline();


});