var activator, selector, selectorOpen, cursorActive, currentCursorMode, currentPinNumber;
var mouseInTheFrame;

// When document is ready
$(document).ready(function() {


	// Activator Pin
	activator = $('.inspect-activator').children('pin');
	cursorActive = activator.hasClass('active');


	// Pin Mode Selector
	selector = $('.pin-mode-selector');
	selectorOpen = selector.parent().hasClass('selector-open');


	// Detect initial cursor mode
	currentCursorMode = activator.data('pin-mode');
	currentPinNumber = 1;


	// Iframe
	mouseInTheFrame = false;

/*
	WILL BE NEEDED

	addMode = false;
	pinWindowOpen = false;
	hoveringText = false;
	hoveringImage = false;
	currentText = "";
	currentDevice = "Desktop";
*/


	// Inspect activator
	activator.click(function(e) {
		togglecursorActive();

		e.preventDefault();
		return false;
	});

	// Pin mode selector
	selector.click(function(e) {
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






	// Inside the Iframe
	$('iframe').on('load', function(){

		// Iframe element
	    var iframe = $('iframe').contents();


	    // Disable all the links
	    // ...


		// Hide the original cursor
	    iframe.find('body *').css('cursor', 'none', 'important');



		// Bring the cursor
	    iframe.on('mousemove', function(e) {

			$('.mouse-cursor').css({
				left:  e.screenX - 15,
				top:   e.screenY - 90
			});

		}).on('click', function(e) {

			log('TST');

			// Prevent clicking something?
			e.preventDefault();
			return false;
		});



	});


	// Mouse cursor
	$(document).on('mousemove', function(e){

		//log( mouseInTheFrame );

	});



});




// When everything is loaded
$(window).on("load", function (e) {


	// Close Pin Mode Selector - If on revise mode !!!
	togglecursorActive();


});


// Tab Toggler
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


// FUNCTION: Switch Pin Mode
function switchPinMode(pinMode) { log(pinMode);

	activator.attr('data-pin-mode', pinMode);
	currentCursorMode = pinMode;

	if (selectorOpen) togglePinModeSelector(true);

}


// FUNCTION: Toggle Inspect Mode
function togglecursorActive(forceClose = false) {

	if (cursorActive || forceClose) {

		activator.removeClass('active');
		cursorActive = false;

	} else {

		activator.addClass('active');
		cursorActive = true;
		if (selectorOpen) togglePinModeSelector(true);

	}

}


// FUNCTION: Toggle Pin Mode Selector
function togglePinModeSelector(forceClose = false) {

	if (selectorOpen || forceClose) {

		selector.removeClass('open');
		selector.parent().removeClass('selector-open');
		$('#pin-mode-selector').fadeOut();
		selectorOpen = false;
		if (!cursorActive) togglecursorActive();

	} else {

		selector.addClass('open');
		selector.parent().addClass('selector-open');
		$('#pin-mode-selector').fadeIn();
		selectorOpen = true;
		if (cursorActive) togglecursorActive(true);

	}

}
