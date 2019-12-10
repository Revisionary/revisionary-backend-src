/*jshint multistr: true */

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

		set_client_cache(user_ID + '_columnSize', selected);

		e.preventDefault();
		return false;

	});


	// Apply Block sizes
	var columnSize = get_client_cache(user_ID + '_columnSize');
	if ( columnSize ) $('.size-selector a[data-column="'+ columnSize +'"]').click();


	// Category Sortable
	$(".cat-sortable").sortable({
		group: 'categories',
		handle: '.cat-handle',
		itemSelector: "li:not(#uncategorized)",
		exclude: '.cat-separator, .empty-cat',
		vertical: true,
		distance: 20,
		nested: false,
		placeholder: "<li class='col sortable-placeholder'></li>",
		onDrop: function ($item, container, _super, event) {


			$item.removeClass(container.group.options.draggedClass).removeAttr("style");
			$("body").removeClass(container.group.options.bodyClass);



			// Show all the screen navigations
			$('.screens .dropdown > ul').show();



		    // Update the order
		    doAction('reorder', 'user', 0, updateOrderNumbers());


		}
	});


	// Project/Page sortable
	$('.object-sortable').sortable({
		group: 'objects',
		handle: '.object-handle:not(.pages)',
		itemSelector: "li:not(.add-new-template)",
		exclude: '.cat-separator, .empty-cat',
		vertical: false,
		distance: 20,
		nested: false,
	    placeholder: "<li class='col'><div class='sortable-placeholder'></div></li>",
	    onDragStart: function ($item, container, _super, event) {


			$item.css({
				height: $item.outerHeight(),
				width: $item.outerWidth()
			});
			$item.addClass(container.group.options.draggedClass);
			$("body").addClass(container.group.options.bodyClass);



			// Remove all add new boxes
			$('.add-new-block').css('opacity', '0').css('width', '0').css('padding', '0');
			$('.empty-cat').remove();


			// Remove all the screen navigations
			$('.screens .dropdown > ul').hide();


		},
		onDrop: function ($item, container, _super, event) {


			$item.removeClass(container.group.options.draggedClass).removeAttr("style");
			$("body").removeClass(container.group.options.bodyClass);



			// Re-add them
	    	addNewPageButtons();

			// Show all the screen navigations
			$('.screens .dropdown > ul').show();



		    // Update the order
		    doAction('reorder', 'user', 0, updateOrderNumbers());


		}
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

		// Update the URLs
		$('.add-phase').each(function() {

			var newPhaseURL = $(this).attr('href');

			var widthOnURL = getParameterByName('page_width', newPhaseURL);
			var heightOnURL = getParameterByName('page_height', newPhaseURL);

			var newURL = newPhaseURL.replace('page_width='+widthOnURL, 'page_width='+width);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+height);

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

			$('.category, .block').show();
			$('.filter-blocks input').val("");

		}

	});


	$('.filter-blocks input').keyup(function(){

    	var selectSize = $(this).val().toLowerCase();

        if (selectSize != "") filter(selectSize);
        else $('.category, .block').show();

    });
    function filter(e) {
        var regex = new RegExp('\\b\\w*' + e + '\\w*\\b');

        $('.category').hide();

        $('.block').hide().filter(function() {
            return regex.test( $(this).find('.box-name .name').text().toLowerCase() );
        }).each(function() {

	        $(this).show();
	        $(this).parents('.category').show();

        });
    }

});


// Update order numbers
function updateOrderNumbers() {


	var newOrder = [];


	$('.categories > .category').each(function(index) {

		var catID = $(this).attr('data-id');

		// Update the block category IDs
		$(this).find(".blocks > .block" ).attr('data-cat-id', catID);


		// Update the category order
		$(this).attr('data-order', index);


		newOrder.push({
            'type' : $(this).attr('data-type'),
            'ID' :  $(this).attr('data-id'),
            'catID' : catID,
            'order' : index
        });

	});



	$('.blocks > .block:not(.add-new-block)').each(function(index) {


		// Skip the uncategorized
		if ( $(this).attr('data-id') == 0 ) return true;


		// Update the block order
		$(this).attr('data-order', index);


		newOrder.push({
            'type' : $(this).attr('data-type'),
            'ID' :  $(this).attr('data-id'),
            'catID' :  $(this).attr('data-cat-id'),
            'order' : index
        });

	});

	return newOrder;

}


// New page/project clones
function addNewPageButtons() {

	console.log('New page/project buttons adding...');

	var cat_project_ID = 'new';
	if (dataType == 'page') cat_project_ID = project_ID;


	// Remove the all boxes
	$('.add-new-block').remove();


	// Add the box to each category
	$('.category').each(function() {

		var category_ID = parseInt( $(this).attr('data-id') );
		var order = $(this).find('li.item').length;

		$(this).find('ol.blocks').append( newBlockTemplate(cat_project_ID, category_ID, order) );

	});

}


// TEMPLATES:

// New project/page buttons
function newBlockTemplate(cat_project_ID, category_ID, order) {


	// Default
	cat_project_ID = assignDefault(cat_project_ID, "new");
	category_ID = assignDefault(category_ID, 0);
	order = assignDefault(order, 0);


	var limitExceed = false;
	var limitExceedBy = dataType;

	if ( dataType == "project" && $('.limit-wrapper .projects-limit.exceed').length ) {
		limitExceed = true;
	}

	else if ( $('.limit-wrapper .pages-limit.exceed').length ) {
		limitExceed = true;
		limitExceedBy = "page";
	}

	var currentLimit = limitations.max[limitExceedBy];


	return '\
	<li class="col xl-3-12 block add-new-block '+ (limitExceed ? 'exceed-limit' : '') +'">\
		<div class="limit-message">\
			<span>You have reached <br> the '+ currentLimit +' '+ limitExceedBy +'s limit.</span>\
			<a href="/upgrade" class="upgrade-button">Increase the '+ limitExceedBy +'s Limit Now</a>\
		</div>\
		<div class="box xl-center">\
			<div class="wrap xl-flexbox xl-middle xl-left new">\
				<div class="col xl-8-12 xl-outside-24 xl-center">\
					<form action="/projects" method="post" class="new-project-form">\
						<input type="hidden" name="add_new" value="true"/>\
						<input type="hidden" name="project_ID" value="'+cat_project_ID+'"/>\
						<input type="hidden" name="category" value="'+category_ID+'"/>\
						<input type="hidden" name="order" value="'+order+'"/>\
						<input type="hidden" name="page_width" value="1440"/>\
						<input type="hidden" name="page_height" value="900"/>\
						<input type="hidden" name="screens[]" value="11"/>\
						<label for="url-'+category_ID+'">\
							<b>Add New '+ dataType +'</b><br>\
						</label>\
						<div class="wrap xl-table xl-bottom xl-center xl-gutter-8">\
							<div class="col">\
								<input id="url-'+category_ID+'" type="url" name="page-url" class="full" placeholder="ENTER A WEBSITE URL" tabindex="1" required autofocus/>\
							</div>\
							<div class="col" style="width: 55px;">\
								<input type="submit" value="Add" class="invert" title="Go Revising!"/>\
							</div>\
						</div>\
					</form>\
					<div class="wrap xl-flexbox xl-between">\
						<div class="col">\
							<small class="design-uploader">or <a href="#" data-tooltip="In development..."><b><u>Upload</u></b></a> your page design <i class="fa fa-question-circle tooltip bottom-tooltip" data-tooltip="Upload design images to add your comments."></i></small>\
						</div>\
						<div class="col" style="width: 55px;">\
							<a href="#" class="plus-icon" data-modal="add-new" data-type="'+ dataType +'" data-id="'+cat_project_ID+'"><small style="opacity: .3; font-size: 10px; letter-spacing: 0.7px;"><i class="fa fa-ellipsis-v"></i> Advanced</small></a>\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>\
	</li>';

}
