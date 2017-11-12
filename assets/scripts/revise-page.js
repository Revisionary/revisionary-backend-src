var iframeScale = 1;

// When document is ready
$(function() {


	// Tab opener
	$('.opener').click(function(e) {
		toggleTab( $(this) );

		e.preventDefault();
		return false;
	});


	// Comment Opener
	$('.pins-list .pin-title').click(function(e) {
		$(this).toggleClass('close');

		e.preventDefault();
		return false;
	});



	// Iframe Fit
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

	}).resize();


});


// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});


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


// Console log shortcut
function log(log, arg1) {
	//console.log(log);
}