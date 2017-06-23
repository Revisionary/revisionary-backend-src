$(function() {
	$('.size-selector a').click(function(e) {

		var selected = $(this).data('column');

		$('.blocks').removeClass (function (index, className) {
		    return (className.match (/(^|\s)xl-\S+/g) || []).join(' ');
		}).addClass('xl-' + selected);


		$(this).parent().parent().children('li').removeClass('selected');
		$(this).parent().addClass('selected');

		e.preventDefault();
		return false;

	});


	// Block Sortables
	$('.sortable').sortable({
	    items: '[draggable="true"]',
	    forcePlaceholderSize: true
	}).bind('sortupdate', function(e, ui) {
	    addNewPageButtons();

	    // AJAX update the order
	    // ...

	});

	function addNewPageButtons() {

		var page_type = "Page";
		if ( $('h1').text() == "PROJECTS" ) page_type = "Project";

		var box_html = $('<div>').append( $('.add-new-template').clone().removeClass('add-new-template').addClass('add-new-block') ).html();

		$('.add-new-block').remove();

		$('.cat-separator').each(function() {

			if ( $(this).prev().hasClass('block') || $(this).prev().hasClass('cat-separator') ) {

				$(this).prev().after(box_html);

			}

		});

	}

	addNewPageButtons();

});