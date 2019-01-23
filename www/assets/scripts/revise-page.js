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

		var thePin = $(this).prev().children('pin');
		var pin_ID = thePin.attr('data-pin-id');
		var pinComments = $(this).next('.pin-comments[data-pin-id="'+ pin_ID +'"]');


		if (!$(this).hasClass('close')) {

			console.log('Getting comments for: ', pin_ID);

			getComments(pin_ID, pinComments);

		}



		e.preventDefault();
		return false;
	});



	// Pin mode change
	$('.pin-types li:not(.deactivator) a').click(function(e) {

		var selectedPinType = $(this).parent().attr('data-pin-type');
		var selectedPinPrivate = $(this).parent().attr('data-pin-private');

		switchPinType(selectedPinType, selectedPinPrivate);

		$('.pin-mode .dropdown > ul').hide();


		var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;

		if (history.pushState) {
		    var newurl = queryParameter(currentUrl, 'pinmode', (selectedPinType == "live" ? "" : selectedPinType));
		    newurl = queryParameter(newurl, 'privatepin', (selectedPinPrivate == 1 ? "1" : ""));
		    window.history.pushState({path:newurl},'',newurl);
		}


		e.preventDefault();
		return false;
	});

	$('.pin-mode').hover(function() {
		$('.pin-mode .dropdown > ul').css('display', '');
	});


	// Cursor deactivator
	$('.deactivator').click(function(e) {
		toggleCursorActive(true);


		var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;

		if (history.pushState) {
		    var newurl = queryParameter(currentUrl, 'pinmode', 'browse');
		    newurl = queryParameter(newurl, 'privatepin', '');
		    window.history.pushState({path:newurl},'',newurl);
		}


		e.preventDefault();
		return false;
	});



	// Iframe Fit to the screen
	var maxWidth  = iframeWidth = $('iframe').width();
	var maxHeight = iframeHeight = $('iframe').height();
	$('.iframe-container').css({ width: maxWidth, height: maxHeight });

	$(window).resize(function(e) {


	    var page = $('#page');
	    var width = page.width();
	    var height = page.height();



	    // UPDATE THE CURRENT WINDOW SIZE FOR CUSTOM SCREEN ADDING

		// Show new values
		$('.screen-width').text(width);
		$('.screen-height').text(height);

		// Edit the input values
		$('input[name="page_width"]').attr('value', width);
		$('input[name="page_height"]').attr('value', height);

		// Update the URLs
		$('.new-screen[data-screen-id="11"]').each(function() {

			var newScreenURL = $(this).attr('href');
			//var topBarHeight = $('#top-bar').outerHeight();

			var widthOnURL = getParameterByName('page_width', newScreenURL);
			var heightOnURL = getParameterByName('page_height', newScreenURL);

			var newURL = newScreenURL.replace('page_width='+widthOnURL, 'page_width='+width);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+height);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});



		// IFRAME FIT TO THE SCREEN

	    width = width - 2; // -2 for the borders
	    height = height - 2; // -2 for the borders

	    // Early exit if smaller than the screen
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



	// Stop scrolling on content editor
	var scrollOnContent = false;
	$(document).on('mousewheel', '#pin-window [contenteditable], #pin-window .pin-comments', function(e) {

		scrollOnContent = true;
		//console.log('SCROLL ON CONTENT');

	});



	// Continue scrolling on any pin or the pin window
	$(document).on('mousewheel', '#pins > pin, #pin-window', function(e) {

		//if (!scrollOnContent) console.log('SCROLL ON WINDOW');

		var scrollDelta = e.originalEvent.wheelDelta;
		if (!scrollOnContent) iframe.scrollTop( scrollOffset_top - scrollDelta );

		scrollOnContent = false;

	});


	// Pin window draggable
	$("#pin-window").draggable({
		handle: ".move-window",
		cursor: "move",
		opacity: 0.7,
		containment: "#page",
		iframeFix: true,
		start: function( event, ui ) {

			// console.log('PIN WINDOW IS MOVING');
			$("#pin-window").addClass('moved');

		}
	});


	// Close pin window
	$('#pin-window .close-button').click(function(e) {

		// Send the comment if unsend
		if($('#pin-window .comment-input').val()) $('#comment-sender').submit();

		// Close the window
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
		var imgData = null;
/*
		var pin = pinElement(pin_ID);
		var element_index = pin.attr('data-revisionary-index');


		// Take screenshot
		html2canvas( iframeElement(element_index)[0] ).then(canvas => {

			//var imgData = canvas.toDataURL();
		    //console.log(imgData);

			// Toggle Complete it on DB
			//console.log(pin_ID, !isComplete);
			completePin(pin_ID, !isComplete, imgData);

		});
*/


		completePin(pin_ID, !isComplete, imgData);
		e.preventDefault();

	});


	// Pin window section toggle
	$('#pin-window .section-title').click(function(e) {


		// Toggle the collapsed class
		$(this).toggleClass('collapsed');


		// Update the location and size values first
		relocatePins(null, null, null, true);


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


	// Toggle differences content
	$('.difference-switch').click(function(e) {

		pinWindow.toggleClass('show-differences');

		var diffText = pinWindow.hasClass('show-differences') ? "SHOW CHANGES" : "SHOW DIFFERENCE";
		var diffIcon = pinWindow.hasClass('show-differences') ? "fa-pencil-alt" : "fa-random";

		pinWindow.find('.content-editor span.diff-text').text(diffText);
		pinWindow.find('.content-editor .difference-switch > i').removeClass('fa-random', 'fa-pencil-alt').addClass(diffIcon);


		if (pinWindow.hasClass('show-differences')) {

			var originalContent = pinWindow.find('.content-editor .edit-content.original').html();
			var changedContent = pinWindow.find('.content-editor .edit-content.changes').html();


			// Difference check
			var diffContent = diffCheck(originalContent, changedContent)


			// Add the differences content
			pinWindow.find('.content-editor .edit-content.differences').html( diffContent );

		}


		e.preventDefault();

	});


	// Pin window content changes
	var doChange = {};
	$(document).on('input', '#pin-window.active .content-editor .edit-content', function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var elementIndex = pinWindow.attr('data-revisionary-index');
		var modification = $(this).html();
		var changedElement = iframeElement(elementIndex);


		//console.log('REGISTERED CHANGES', changes);


		// Stop the auto-refresh
		stopAutoRefresh();


		// Instant apply the change
		changedElement.html(modification);
		changedElement.attr('contenteditable', "true");


		// Remove unsent job
		if (doChange[elementIndex]) clearTimeout(doChange[elementIndex]);

		// Send changes to DB after 1 second
		doChange[elementIndex] = setTimeout(function(){

			saveChange(pin_ID, modification);

		}, 1000);

		//console.log('Content changed.');

	});


	// Uploader
	$('#filePhoto').change(function() {

		var maxSize = $(this).attr('data-max-size');


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


	// CSS EDITS
	var doChangeCSS = {};
	$('[data-edit-css]').on('click input', function(e) {


		var property = $(this).attr('data-edit-css');
		var isActive = $(this).hasClass('active');

		var defaultValue = $(this).attr('data-default');
		var value = $(this).attr('data-value') || $(this).val() || defaultValue;


		value = isActive ? defaultValue : value;


		var pin_ID = pinWindow.attr('data-pin-id');
		var elementIndex = pinWindow.attr('data-revisionary-index');
		var options = pinWindow.find('ul.options');


		console.log('EDIT CSS: ', property, value, elementIndex);


		// Stop the auto-refresh
		stopAutoRefresh();


		// Disable the active status
		options.find('a[data-edit-css="'+ property +'"]').removeClass('active');
		options.find('a[data-edit-css="'+ property +'"][data-value="'+ value +'"]').addClass('active');


		// Add the value
		options.attr('data-' + property, value);


		// Prepare the CSS data
		var css = {
			'display' 				: options.attr('data-display'),
			'opacity' 				: options.attr('data-opacity'),
			'text-align'			: options.attr('data-text-align'),
			'text-decoration-line'	: options.attr('data-text-decoration-line'),
			'font-weight'			: options.attr('data-font-weight'),
			'font-style'			: options.attr('data-font-style'),
			'color'					: options.attr('data-color'),
			'background-color'		: options.attr('data-background-color')
		}


		// Prepare the CSS declarations
		var cssCode = "";
		$.each( css, function( key, value ) {

			// Skip if display is block
			if (key == "display" && value == "block") return true;

			cssCode = cssCode + key + ":" + value + " !important; ";

		});


		// Instant update the CSS
		updateCSS(elementIndex, cssCode);


		// Remove unsent job
		if (doChangeCSS[elementIndex]) clearTimeout(doChangeCSS[elementIndex]);

		// Send changes to DB after 1 second
		doChangeCSS[elementIndex] = setTimeout(function(){

			saveCSS(pin_ID, css);

		}, 1000);


		relocatePins(null, null, null, true);
		e.preventDefault();

	});


	// Reset CSS
	$('.reset-css').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var element_index = parseInt(pinWindow.attr('data-revisionary-index'));


		if ( confirm('Are you sure you want to reset all your view options?') ) {


			// Reset CSS on DB
			resetCSS(pin_ID);


			// Remove the styles
			iframeElement('style[data-index="'+ element_index +'"]').remove();


		}


		e.preventDefault();

	});


	// Reset Content
	$('.reset-content').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var element_index = parseInt(pinWindow.attr('data-revisionary-index'));


		if ( confirm('Are you sure you want to revert your content changes?') ) {


			// Reset content on DB
			resetContent(pin_ID);


		}


		e.preventDefault();

	});


	// Show original CSS toggle
	$('.show-original-css').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');


		toggleCSS(pin_ID);


		e.preventDefault();

	});


	// Convert Pin
	$('.type-convertor > li > a').click(function(e) {

		var pin_ID = pinWindow.attr('data-pin-id');
		var element_index = parseInt(pinWindow.attr('data-revisionary-index'));
		var targetPin = $(this).children('pin');


		// Confirm if converting to standard pin
		if (
			pinWindow.attr('data-pin-type') == "live"
			&& targetPin.attr('data-pin-type') == "standard"
			&& !confirm('Are you sure you want to convert this live pin to a standard comment pin? All your changes will be reverted.')
		) return false;


		// Remove the image on this element
		convertPin(pin_ID, targetPin);


		e.preventDefault();

	});


	// Hovering a pin from the pins list tab
	$(document).on('mouseover', '.pins-list > .pin', function(e) {

		var pin_ID = $(this).find('pin').attr('data-pin-id');
		var pin_type = $(this).find('pin').attr('data-pin-type');
		var pin_private = $(this).find('pin').attr('data-pin-private');
		var element_index = $(this).find('pin').attr('data-revisionary-index');
		var pinX =  pinElement(pin_ID).attr('data-pin-x');
		var pinY =  pinElement(pin_ID).attr('data-pin-y'); console.log(pinY, ($('.iframe-container').height() / 2), 22.5, parseInt( pinY ) - ($('.iframe-container').height() / 2) - 22.5);

		// Outline
		$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');
		outline(iframeElement(element_index), pin_private, pin_type);

		// Scroll
		scrollToPin(pin_ID);

		e.preventDefault();
	}).on('mouseout', '.pins-list > .pin', function(e) {

		if (pinAnimation) pinAnimation.stop();
		if (pinAnimationTimeout) clearTimeout(pinAnimationTimeout);

		$('#pins > pin').css('opacity', '');
		removeOutline();

		e.preventDefault();
	});


	// Filtering the pins
	$('.pins-filter > a').click(function(e) {


		console.log('SHOW THE PINS: ', filter);


		var filter = $(this).attr('data-filter');


		$('.pins-filter > a').removeClass('selected');
		$(this).addClass('selected');


		$('#pins, .pins-list').attr('data-filter', filter);



		var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;


		if (history.pushState) {
		    var newurl = queryParameter(currentUrl, 'filter', (filter == "all" ? '' : filter));
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
		removeOutline();


		// Outline the element
		var hoveringPinPrivate = $(this).attr("data-pin-private");
		outline(iframeElement($(this).attr("data-revisionary-index")), hoveringPinPrivate, $(this).attr("data-pin-type"));


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
				togglePinWindow(focusedPin.attr('data-pin-id'));


			focusedPin = null;

			e.preventDefault();
		}

	}).on('mouseout', '#pins > pin', function(e) {

		//console.log('MOUSE OUT FROM PIN!', pinDragging);

		hoveringPin = false;

		removeOutline();


		// Show the cursor
		if (cursorActive && !pinDragging) cursor.stop().fadeIn();


		e.preventDefault();

	});


});


// When everything is loaded
$(window).on("load", function (e) {


	// CONTENT EDITOR PLUGIN
	$(".edit-content[contenteditable='true']").popline();


	// COLOR PICKER PLUGIN
	$("input[type='color']").spectrum({
		preferredFormat: "hex",
	    showInitial: true,
	    showInput: true,
	    showAlpha: true,
	    allowEmpty: true,
		chooseText: "Close",
	    move: function(color) {

		    $(this).val(color.toRgbString()).attr('data-value', color.toRgbString()).trigger('input');

	    }
	}).on("dragstart.spectrum", function(e, color) {

		console.log('DRAG STARTED', color.toHexString());
		$(this).val(color.toHexString()).spectrum("set", color.toHexString());

	});


});