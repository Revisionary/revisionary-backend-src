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


	// DRAG DETECTOR
	$('.sortable [draggable="true"]').bind('dragstart', function( event ) {


		// Remove all add new boxes
		$('.add-new-block').css('opacity', '0').css('width', '0').css('padding', '0');


	}).bind('dragend', function( event ){


		// Re-add them
    	addNewPageButtons();


    });





	// ACTIONS
	$('.actions a').click(function(e) {

		var action = $(this).attr('data-action');
		var parent_item = $(this).parent().parent();

		if (action == "rename") {

			var input = parent_item.find('input.edit-name');
			parent_item.toggleClass('editing');


			// If the same text
			if ( input.val() == input.attr('value') ) {
				e.preventDefault();
				return false;
			}

		}


		// Confirmations
		var confirm_text;

		if ( action =='archive' )
			confirm_text = "Are you sure you want to archive this?";

		if ( action =='delete' )
			confirm_text = "Are you sure you want to delete this?";

		if ( action =='recover' )
			confirm_text = "Are you sure you want to recover this?";

		if ( action =='remove' )
			confirm_text = "Are you sure you want to completely remove this?";



		// If confirmed, send data
		if (action == "rename" || action == "add-new-category" || confirm(confirm_text) ) {

			var url = $(this).attr('href');
			var block = parent_item.parent().parent();

			$('.progress').css('width', "0%");



			// AJAX Send data
			$.get(url, {ajax:true, inputText: ( action == "rename" ? input.val() : '' )}, function(result){

				$.each(result.data, function(key, data){

					console.log(key, data);

					// Progressbar Update
					if ( data.status == "successful" ) {


						if (action == "rename") {

							parent_item.find('.name').text( input.val() );
							input.attr('value', input.val() );

							$('.filter [data-cat-id="' + parent_item.attr('data-cat-id') + '"]').text( input.val() );

						} else if (action == "add-new-category") {

							alert('New category added!');

						} else { // Archive / Delete / Remove


							// If a category is deleting
							if ( parent_item.hasClass('cat-separator') ) {

								var deleted_cat_id = parent_item.attr('data-cat-id');


								// Remove from filter bar
								$('.filter [data-cat-id="' + deleted_cat_id + '"]').remove();


								// Remove the category
								parent_item.remove();


								// Update the add new blocks
								addNewPageButtons();



							} else
								block.remove();

						}

						$('.progress').css('width', "100%");


					} else {

						if (action == "rename") {

							parent_item.addClass('editing ' + data.status);

						}

					}

				});

			}, 'json');

		}


		e.preventDefault();
		return false;

	});


	// Rename Inputs
	$('.name-field input.edit-name').keydown(function (e){

	    if(e.keyCode == 13)
	        $(this).blur();

	}).focusout(function() {

		$(this).next().click();

	});


	// New Page/Project Modal
	$(document).on('click', '.add-new-box', function(e) {

		openModal('#add-new');

		var thisBlock = $(this).parent().parent();
		var catID = thisBlock.prevAll('.cat-separator:first').attr('data-cat-id') || 0;
		var catName = thisBlock.prevAll('.cat-separator:first').find('.name').text();
		var orderNumber = thisBlock.prev('.block').attr('data-order') || 0;

		$('#add-new .to').html("To <b></b> Section");
		$('#add-new .to > b').text(catName);

		if (catName == "Uncategorized")
			$('#add-new .to').html("");


		// Category ID input update
		$('#add-new input[name="category"]').attr('value', catID);


		// Order number input update
		$('#add-new input[name="order"]').attr('value', ( typeof orderNumber !== 'undefined' ? parseInt(orderNumber) + 1 : 0 ));


		// Focus to the input
		setTimeout(function() {

			$('#add-new input[name="project-name"]').focus();

		}, 500);


		e.preventDefault();
		return false;

	});


	// Close New Page/Project Modal
	$('#add-new .cancel-button').on('click', function(e) {

		closeModal('#add-new');

		e.preventDefault();
		return false;

	});


	// More Options Button on New Page/Project Modal
	$('#add-new .option-toggler').on('click', function(e) {


		$('#add-new').toggleClass('more-options');


		if ( $('#add-new').hasClass('more-options') )
			$('#add-new .more-options-wrapper input').prop('disabled', false);
		else
			$('#add-new .more-options-wrapper input').prop('disabled', true);


		e.preventDefault();
		return false;


	});



	// MODAL WORKS
	function openModal(modalElement) {

		$('body').addClass('popup-open');
		$(modalElement).addClass('active');

	}

	function closeModal(modalElement) {

		$(modalElement).removeClass('active');
		$('body').removeClass('popup-open');

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



	// Add new device to modal
	$('.device-add > li > a').click(function(e) {

		var listed_device = $(this).parent();

		var device_id = $(this).attr('data-device-id');
		var device_width = $(this).attr('data-device-width');
		var device_height = $(this).attr('data-device-height');
		var device_cat_name = $(this).attr('data-device-cat-name');
		var device_cat_icon = $(this).attr('data-device-cat-icon');


		var new_device_html = '\
			<li>\
				<input type="hidden" name="devices[]" value="'+device_id+'"/>\
				<i class="fa '+ device_cat_icon +'" aria-hidden="true"></i> <span>'+device_cat_name+' ('+device_width+' x '+device_height+')</span>\
				<a href="#" class="remove-device"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
			</li>\
		';

		$('.selected-devices').append(new_device_html);

		listed_device.hide();

		// Show all the remove buttons
		$('.selected-devices a.remove-device').show();


		e.preventDefault();
		return false;

	});



	// Add user toggle
	$('.new-member').click(function(e) {

		$(this).next().fadeToggle();

		e.preventDefault();
		return false;

	});


	// Add new user
	$('.new-member + input').keydown(function (e){

		var input = $(this);
		var type = $(this).attr('data-type');
		var userList = $('.shares.user.'+type);
		var emailList = $('.shares.email.'+type);

		function userTemplate_html(user_ID, userLink, userPhoto, userName, type, deletable = true) {

			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="' + user_ID + '"/>' : '') +'\
					<a href="' + userLink + '">\
						<picture class="profile-picture" ' + userPhoto + '>\
							'+userName+'\
						</picture>\
					</a>\
					<a href="#" class="remove-share" data-type="'+type+'" data-value="'+user_ID+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			';

		}

		function emailTemplate_html(email, type, deletable = true) {

			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="' + email + '"/>' : '') +'\
					<span>\
						'+email+'\
						<a href="#" class="remove-share" data-type="'+type+' data-value="'+email+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
					</span>\
				</li>\
			';

		}


	    if(e.keyCode == 13) {

		    input.prop('disabled', true);


			// AJAX Send data
			$.post(ajax_url, {

				'type'	: 'user-check',
				'nonce'	: nonce,
				'email'	: input.val()

			}, function(result){

				$.each(result.data, function(key, data){


					console.log(key, data);


					// If user found
					if ( data.status == "found" ) {


						// Add if not already exists
						if ( !userList.find('[value="'+data.user_ID+'"]').length )
							userList.append(userTemplate_html(data.user_ID, data.user_link, data.user_photo, data.user_name, type));


						// Also add to the page shares list
						if ( type == "project" && $('.shares.user.page').length && !$('.shares.user.page [data-value="'+data.user_ID+'"]').length ) {
							$('.shares.user.page').append(userTemplate_html(data.user_ID, data.user_link, data.user_photo, data.user_name, type, false));
						}


						input.removeClass('error');
						input.val('');

					} else if ( data.status == "not-found" ) {


						if ( !emailList.find('[value="'+input.val()+'"]').length )
							emailList.append(emailTemplate_html(input.val(), type));


						// Also add to the page shares list
						if ( type == "project" && $('.shares.email.page').length && !$('.shares.email.page [data-value="'+input.val()+'"]').length ) {
							$('.shares.email.page').append(emailTemplate_html(input.val(), type, false));
						}


						input.removeClass('error');
						input.val('');

					} else if ( data.status == "invalid-email" ) {

						input.addClass('error');

					}

					input.prop('disabled', false);

				});

			}, 'json');



		    e.preventDefault();
		    return false;
	    }

	});



	// Delete selected shared person from the list
	$(document).on('click', '.shares a.remove-share', function(e) {


		var share = $(this).closest('li');
		var type = $(this).attr('data-type');
		var value = $(this).attr('data-value');


		// Remove the share
		share.remove();


		// Remove from page list
		if ( type == "project" && $('.shares > li.undeletable a.remove-share[data-value="'+value+'"]').length ) {

			$('.shares a.remove-share[data-value="'+value+'"]').closest('li').remove();

		}


		e.preventDefault();
		return false;

	});





	// Delete selected device from the list
	$(document).on('click', '.selected-devices a.remove-device', function(e) {


		var listed_device = $(this).parent();

		var device = listed_device.find('input');
		var device_id = device.attr('value');


		// Show in the list
		$('.device-add > li > a[data-device-id="'+device_id+'"]').parent().show();


		// Remove the device
		listed_device.remove();


		// Count the selected devices and if less than 2, hide that remover
		if ( $('.selected-devices > li').length < 2 ) {

			$('.selected-devices a.remove-device').hide();

		}


		console.log('REMOVE', device_id);


		e.preventDefault();
		return false;

	});





});



// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: false
	});


});