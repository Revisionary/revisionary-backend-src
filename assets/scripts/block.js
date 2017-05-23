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

		$('.add-new-block').remove();

		$('.cat-separator').each(function() {

			if ( $(this).prev().hasClass('block') || $(this).prev().hasClass('cat-separator') ) {
				$(this).prev().after('<div class="col block add-new-block"><div class="box xl-center"><a href="#" class="add-new-box wrap xl-flexbox xl-middle xl-center" style="min-height: inherit; letter-spacing: normal;"><div class="col">New Page<div class="plus-icon" style="font-family: Arial; font-size: 90px; line-height: 80px;">+</div></div></a></div></div>');
			}

		});

	}

	addNewPageButtons();

});