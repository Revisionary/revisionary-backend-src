$(function() {

	// Tab opener
	$('.opener').click(function(e) {

		toggleTab( $(this) );

		e.preventDefault();
		return false;

	});


});


// When everything is loaded
$(window).on("load", function (e) {

	// Hide the loading overlay
	$('#loading-overlay').fadeOut();


	// Close all the tabs
	$('.opener').each(function() {

		toggleTab( $(this) );

	});

});


// Tab Toggler
function toggleTab(tab, slow = false) {

	var speed = slow ? 500 : 100;

	var container = tab.parent();
	var containerWidth = container.outerWidth();
	var sideElement = tab.parent().parent();


	if ( sideElement.hasClass('top-left') || sideElement.hasClass('bottom-left') ) {

		if (container.hasClass('open')) {

			container.animate({
				left: -containerWidth,
			}, speed, function() {
				container.removeClass('open');
			});

		} else {

			container.animate({
				left: 0,
			}, speed, function() {
				container.addClass('open');
			});

		}

	} else {

		if (container.hasClass('open')) {

			container.animate({
				right: -containerWidth,
			}, speed, function() {
				container.removeClass('open');
			});

		} else {

			container.animate({
				right: 0,
			}, speed, function() {
				container.addClass('open');
			});

		}

	}

}