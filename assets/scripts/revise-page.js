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


});


// When everything is loaded
$(window).on("load", function (e) {

	// Hide the loading overlay
	$('#loading').fadeOut();


	// Close all the tabs
	$('.opener').each(function() {

		toggleTab( $(this) );

	});


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: true
	});

});


// Console log shortcut
function log(log, arg1) {
	console.log(log);
}