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
		console.log(pin_ID, !isComplete);
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

		toggleContentEdit(pin_ID);

		e.preventDefault();

	});


	// Pin window content changes
	var doChange;
	$(document).on('DOMSubtreeModified', '#pin-window.active .edit-content', function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var elementIndex = pinWindow.attr('data-revisionary-index');
		var changes = $(this).html();
		var changedElement = iframe.find('[data-revisionary-index="'+ elementIndex +'"]');
		var changedElementOriginal = changedElement.html();




		console.log('REGISTERED CHANGES', changes);



		// Stop the auto-refresh
		stopAutoRefresh();


		// Apply the change
		changedElement.html(changes).attr('data-revisionary-edited', "1").attr('data-revisionary-showing-changes', "1");



		// MODIFICATION LIST
		var modification = modifications.find(function(modification) {
			return modification.pin_ID == pin_ID ? true : false;
		});


		// Add the original content to the list
		if (modification) {

			// Update the modification on the list
			modification.modification =  htmlentities( changes, "ENT_QUOTES");

			if (modification.original == null)
				modification.original =  htmlentities( changedElementOriginal, "ENT_QUOTES");

		} else {

			// Add to the modifications list
			modifications[modifications.length] = {
				element_index: elementIndex,
				pin_ID: pin_ID,
				modification_type: "html",
				modification: htmlentities( changes, "ENT_QUOTES"),
				original: htmlentities( changedElementOriginal, "ENT_QUOTES")
			};

		}


		// Remove unsent job
		if (doChange) clearTimeout(doChange);

		// Send changes to DB after 1 second
		doChange = setTimeout(function(){

			saveModification(pin_ID, changes, 'html');

		}, 1000);

		console.log('Content changed.');

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


		// Clear all outlines
		iframe.find('body *').css('outline', '');


		// Outline the element if this is a live pin
		if ($(this).attr("data-pin-type") == "live") {

			var hoveringPinPrivate = $(this).attr("data-pin-private");

			iframe.find('body *').css('outline', 'none');
			iframe.find('body *[data-revisionary-index="'+ $(this).attr("data-revisionary-index") +'"]').css('outline', '2px dashed ' + (hoveringPinPrivate == 1 ? '#FC0FB3' : '#7ED321'), 'important');

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


			// Stop auto refresh
			stopAutoRefresh();


			var focused_pin_id = focusedPin.attr('data-pin-id');

			// If not on DB, don't move it
			if ( $.isNumeric(focused_pin_id) ) {

				pinDragging = true;

				var pinSize = 45;
				var pos_x = containerX - pinSize/2;
				var pos_y = containerY - pinSize/2;

				relocatePins(focusedPin, pos_x, pos_y);

				//console.log('PIN IS MOVING!', pos_x, pos_y);

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


		        console.log('TOGGLE THE PIN WINDOW !!!');

				togglePinWindow(focusedPin.attr('data-pin-x'), focusedPin.attr('data-pin-y'), focusedPin.attr('data-pin-id'));


		    } else {


				var pin_ID = focusedPin.attr('data-pin-id');
				var pinX = parseFloat(focusedPin.attr('data-pin-x')).toFixed(5);
				var pinY = parseFloat(focusedPin.attr('data-pin-y')).toFixed(5);


			    // Update the pin location on DB
			    console.log('Update the new pin location on DB', pinX, pinY);

				// Start the process
				var relocateProcessID = newProcess();

			    $.post(ajax_url, {
					'type'	  	 : 'pin-relocate',
					'nonce'	  	 : pin_nonce,
					'pin_ID'	 : pin_ID,
					'pin_x' 	 : pinX,
					'pin_y' 	 : pinY
				}, function(result){


					// Update the global pins
					var pin = pins.find(function(pin) {
						return pin.pin_ID == pin_ID ? true : false;
					});

					var pinIndex = pins.findIndex(function(element, index, array) {
						return element == pin
					});

					pins[pinIndex].pin_x = pinX;
					pins[pinIndex].pin_y = pinY;



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

		iframe.find('body *').css('outline', '');


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