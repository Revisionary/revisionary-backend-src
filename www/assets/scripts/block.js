var screenWidth = $(window).width();
var screenHeight = $(window).height();

$(function() {

	// Add the new project/page buttons
	addNewPageButtons();


	// Block Sizes
	$('.size-selector a').click(function(e) {

		var selected = $(this).attr('data-column');

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
	    doAction('reorder', 'user', 0, updateOrderNumbers());


	});


	// Drag Detector
	$('.sortable [draggable="true"]').bind('dragstart', function( event ) {


		// Remove all add new boxes
		$('.add-new-block').css('opacity', '0').css('width', '0').css('padding', '0');

		// Remove all the screen navigations
		$('.screens .dropdown > ul').hide();


	}).bind('dragend', function( event ){


		// Re-add them
    	addNewPageButtons();

		// Show all the screen navigations
		$('.screens .dropdown > ul').show();


    });


	// Rename Inputs
	$('.name-field input.edit-name').keydown(function (e){

	    if(e.keyCode == 13)
	        $(this).blur();

	}).focusout(function() {

		$(this).next().click();

	});


	// Update the current screen size !!! Common?
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
		$('input[name="page_width"]').attr('value', screenWidth);
		$('input[name="page_height"]').attr('value', screenHeight);


		$('[data-screen-id="11"]').attr('data-screen-width', screenWidth);
		$('[data-screen-id="11"]').attr('data-screen-height', screenHeight);


		$('.new-screen[data-screen-id="11"]').each(function() {

			var newScreenURL = $(this).attr('href');
			var widthOnURL = getParameterByName('page_width', newScreenURL);
			var heightOnURL = getParameterByName('page_height', newScreenURL);

			var newURL = newScreenURL.replace('page_width='+widthOnURL, 'page_width='+screenWidth);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+screenHeight);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});


	}).resize();



	$('.filter-blocks i').click(function() {

		$('.filter-blocks input').toggleClass('active');

		if ( $('.filter-blocks input').hasClass('active') ) {


			// Focus to the input
			setTimeout(function() {

				$('.filter-blocks input').focus();

			}, 500);


		} else {

			$('.block').show();
			$('.filter-blocks input').val("");

		}

	});


	$('.filter-blocks input').keyup(function(){
    	var selectSize = $(this).val().toLowerCase();
        if (selectSize != "") filter(selectSize);
        else $('.block').show();
    });
    function filter(e) {
        var regex = new RegExp('\\b\\w*' + e + '\\w*\\b');
        $('.block').hide().filter(function () {
            return regex.test( $(this).find('.box-name .name').text().toLowerCase() )
        }).show();
    }


});


// Update order numbers
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
