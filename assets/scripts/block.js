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

		$('.progress').css('width', "0%");

	    // AJAX update the order
		$.post(ajax_url, {
			'type':'data-action',
			'action': 'reorder',
			'orderData' : orderData,
			'nonce' : nonce
		}, function(result){


			$.each(result.data, function(key, data){

				console.log(key, data);

				// Progressbar Update
				if ( data.status == "successful" ) $('.progress').css('width', "100%");

			});

		}, 'json');


	});



	// ACTIONS
	$('.actions a').click(function(e) {

		var confirm_text;

		if ( $(this).hasClass('archive') )
			confirm_text = "Are you sure you want to archive this?";

		if ( $(this).hasClass('delete') )
			confirm_text = "Are you sure you want to delete this?";

		if ( $(this).hasClass('recover') )
			confirm_text = "Are you sure you want to recover this?";

		if ( $(this).hasClass('remove') )
			confirm_text = "Are you sure you want to completely remove this?";


		if ( confirm(confirm_text) ) {

			var url = $(this).attr('href');
			var block = $(this).parent().parent().parent().parent();

			$('.progress').css('width', "0%");

			// AJAX Send data
			$.get(url, {ajax:true}, function(result){

				$.each(result.data, function(key, data){

					console.log(key, data);

					// Progressbar Update
					if ( data.status == "successful" ) {

						block.remove();

						$('.progress').css('width', "100%");
					}

				});

			}, 'json');

		}


		e.preventDefault();
		return false;

	});


	function addNewPageButtons() {

		var page_type = "Page";
		if ( $('h1').text() == "PROJECTS" ) page_type = "Project";

		var box_html = $('<div>').append( $('.add-new-template').clone().removeClass('add-new-template').addClass('add-new-block') ).html();

		$('.add-new-block').remove();

		$('.cat-separator').each(function() {

			if ( $(this).prev().hasClass('block') || ($(this).prev().hasClass('cat-separator') && !$(this).prev().hasClass('xl-hidden')) ) {

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