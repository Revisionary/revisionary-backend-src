var activator, inspectMode, currentCursorMode;

// When document is ready
$(function() {


	// Activator Pin
	activator = $('.inspect-activator').children('pin');


	// Detect initial activation
	inspectMode = activator.hasClass('active');


	// Detect initial cursor mode
	currentCursorMode = activator.data('pin-mode');


	// Inspect activator
	activator.parent().click(function(e) {
		toggleInspectMode();


		e.preventDefault();
		return false;
	});


	// Pin mode selector
	activator.parent().next().click(function(e) {
		togglePinModeSelector();


		e.preventDefault();
		return false;
	});

});


// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});


	// Close Pin Mode Selector
	togglePinModeSelector();


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






// FUNCTION: Switch Cursor
function toggleInspectMode(forceClose = false) {

	$('.inspect-activator').children('pin').toggleClass('active');
	inspectMode = inspectMode ? true : false;

}


// FUNCTION: Toggle Pin Mode Selector
function togglePinModeSelector(forceClose = false) {

	var selector = activator.parent().next();


	if (selector.hasClass('open')) {

		$('#pin-mode-selector').fadeOut();

	} else {

		$('#pin-mode-selector').fadeIn();

	}

	toggleInspectMode();
	selector.toggleClass('open');

}
