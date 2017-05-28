var activator, selector, selectorOpen, inspectMode, currentCursorMode;

// When document is ready
$(function() {


	// Activator Pin
	activator = $('.inspect-activator').children('pin');
	inspectMode = activator.hasClass('active');


	// Pin Mode Selector
	selector = $('.pin-mode-selector');
	selectorOpen = selector.parent().hasClass('selector-open');


	// Detect initial cursor mode
	currentCursorMode = activator.data('pin-mode');


	// Inspect activator
	activator.parent().click(function(e) {
		toggleInspectMode();

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

});


// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});


	// Close Pin Mode Selector
	toggleInspectMode();


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
function toggleInspectMode(forceClose = false) {

	if (inspectMode || forceClose) {

		activator.removeClass('active');
		inspectMode = false;

	} else {

		activator.addClass('active');
		inspectMode = true;
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
		if (!inspectMode) toggleInspectMode();

	} else {

		selector.addClass('open');
		selector.parent().addClass('selector-open');
		$('#pin-mode-selector').fadeIn();
		selectorOpen = true;
		if (inspectMode) toggleInspectMode(true);

	}

}
