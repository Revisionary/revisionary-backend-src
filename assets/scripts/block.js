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


	    // Update the order
	    var orderData = updateOrderNumbers();

		//console.log(orderData);

	    // AJAX update the order
		$.post(ajax_url, {
			'type':'data-action',
			'action': 'reorder',
			'orderData' : orderData
		}, function(result){


			$.each(result.data, function(key, data){


				// Progressbar works !!!
				console.log(key, data);


			});

		}, 'json');





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


	function updateOrderNumbers() {

		var categories = $('.blocks > .cat-separator');

		categories.each(function(index) {

			var catID = $(this).attr('data-id');

			$(this).nextAll( ".block:not(.cat-separator):not(.add-new-block):not(.add-new-template)" ).attr('data-cat-id', catID);


		});



		var newOrder = [];
		var blocks = $('.blocks > .col:not(.add-new-block):not(.add-new-template)');

		blocks.each(function(index) {

			$(this).attr('data-order', index);

			//$(this).prevUntil( $('.cat-separator'), ".block" ).attr('data-order', 'Test' + index);

			newOrder.push({
	            'type' : $(this).attr('data-type'),
	            'ID' :  $(this).attr('data-id'),
	            'catID' :  $(this).attr('data-cat-id'),
	            'order' : index
	        });

		});

		return newOrder;

	}

	addNewPageButtons();

});