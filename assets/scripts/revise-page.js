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


});


// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});


});


// FUNCTION: Tab Toggler
function toggleTab(tab, slow = false) {

	var speed = slow ? 1000 : 500;

	var container = tab.parent();
	var containerWidth = container.outerWidth();
	var sideElement = tab.parent().parent();


	if ( sideElement.hasClass('top-left') || sideElement.hasClass('bottom-left') ) {

		if (container.hasClass('open')) {

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

		if (container.hasClass('open')) {

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