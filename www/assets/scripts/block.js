var screenWidth = $(window).width();
var screenHeight = $(window).height();

$(function() {

	// Prevent clicking '#' links
	$('a[href="#"]').click(function(e) {
		e.preventDefault();
	});


	// Block Sizes
	$('.size-selector a').click(function(e) {

		var selected = $(this).data('column');

		$('.blocks').removeClass(function (index, className) {
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


	    // Update the order
	    var orderData = updateOrderNumbers(); //console.log(orderData);

		// Start the process
		var actionID = newProcess();

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
				if ( data.status == "successful" ) endProcess(actionID);

			});

		}, 'json');


	});


	// DRAG DETECTOR
	$('.sortable [draggable="true"]').bind('dragstart', function( event ) {


		// Remove all add new boxes
		$('.add-new-block').css('opacity', '0').css('width', '0').css('padding', '0');

		// Remove all the device navigations
		$('.devices nav.dropdown').hide();


	}).bind('dragend', function( event ){


		// Re-add them
    	addNewPageButtons();

		// Show all the device navigations
		$('.devices nav.dropdown').show();


    });


	// Rename Inputs
	$('.name-field input.edit-name').keydown(function (e){

	    if(e.keyCode == 13)
	        $(this).blur();

	}).focusout(function() {

		$(this).next().click();

	});



	// Open the new page modal if project is just added
	if(window.location.hash == "#add-first-page") {


		openModal('#add-new-page');

		var thisBlock = $('.add-new-template');
		var catID = thisBlock.prevAll('.cat-separator:first').attr('data-cat-id') || 0;
		var catName = thisBlock.prevAll('.cat-separator:first').find('.name').text();
		var orderNumber = thisBlock.prev('.block').attr('data-order') || 0;

		$('#add-new-page .to').html("To <b></b> Section");
		$('#add-new-page .to > b').text(catName);

		if (catName == "Uncategorized")
			$('#add-new-page .to').html("");


		// Category ID input update
		$('#add-new-page input[name="category"]').attr('value', catID);


		// Order number input update
		$('#add-new-page input[name="order"]').attr('value', ( typeof orderNumber !== 'undefined' ? parseInt(orderNumber) + 1 : 0 ));


		// Focus to the input
		setTimeout(function() {

			$('#add-new-page input[autofocus]').focus();

		}, 500);



	}



	// NEW PAGE/PROJECT CLONES
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


	// Update the current screen size
	$(window).resize(function() {

		var width = $(this).width();
		var height = $(this).height();

		screenWidth = width;
		screenHeight = height - 45 - 2; // -45 for the topbar, -2 for borders !!! ?

		//console.log(width, height);

		// Show new values
		$('.screen-width').text(screenWidth);
		$('.screen-height').text(screenHeight);

		// Edit the input values
		$('input[name="page-width"]').attr('value', screenWidth);
		$('input[name="page-height"]').attr('value', screenHeight);


		$('[data-device-id="11"]').attr('data-device-width', screenWidth);
		$('[data-device-id="11"]').attr('data-device-height', screenHeight);


		$('.new-device[data-device-id="11"]').each(function() {

			var newDeviceURL = $(this).attr('href');
			var widthOnURL = getParameterByName('page_width', newDeviceURL);
			var heightOnURL = getParameterByName('page_height', newDeviceURL);

			var newURL = newDeviceURL.replace('page_width='+widthOnURL, 'page_width='+screenWidth);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+screenHeight);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});


	}).resize();


});
