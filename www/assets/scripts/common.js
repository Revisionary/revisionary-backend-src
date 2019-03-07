// Notification Auto-Refresh
var notificationAutoRefreshTimer;
var notificationAutoRefreshInterval = 10000;
var notificationAutoRefreshRequest = null;


$(function() {


	// Prevent clicking '#' links
	$(document).on('click', 'a[href="#"]', function(e) {
		e.preventDefault();
	});


	// Links with confirmation
	$(document).on('click', 'a[data-confirm]:not([data-action])', function(e) { console.log('Clicked');

		var confirmation = $(this).attr('data-confirm');

		// Redownload exception
		if ( $(this).hasClass('redownload') && Pins.length == 0 ) return true;

		if ( confirmation != "" && confirm(confirmation) ) {

			return true;

		}

		return false;
		e.preventDefault();
	});


	// Close Modal
	$('.cancel-button').on('click', function(e) {

		var popup = $(this).parents('.popup-window').attr('id');

		closeModal('#' + popup);

		e.preventDefault();
		return false;

	});

	// Close Modal via Escape key
	$(document).keydown(function (e){

	    if(e.keyCode == 27) { // Escape

			console.log('GET OUT!!!!');
			$('.popup-window.active .cancel-button').trigger('click');

		    e.preventDefault();
		    return false;
	    }

	});


	// More Options Button on New Page/Project Modal
	$('.popup-window .option-toggler').on('click', function(e) {

		var popup = $(this).parents('.popup-window');


		popup.toggleClass('more-options');


		if ( popup.hasClass('more-options') )
			popup.find('.more-options-wrapper input').prop('disabled', false);
		else
			popup.find('.more-options-wrapper input').prop('disabled', true);


		e.preventDefault();
		return false;


	});


	// Add user toggle
	$('.new-member').click(function(e) {

		$(this).next().fadeToggle();

		e.preventDefault();
		return false;

	});


	// ADD NEW MODAL: New user
	$('.new-member + input').keydown(function (e) {

		var input = $(this);
		var type = $(this).attr('data-type');
		var userList = $('.shares.user.'+type);
		var emailList = $('.shares.email.'+type);
		var lists = $('.shares.'+type);

		function userTemplate_html(user_ID, userPhoto, userName, type, deletable = true) {

			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'" data-to="'+ user_ID +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="' + user_ID + '"/>' : '') +'\
					<picture class="profile-picture" ' + userPhoto + '>\
						'+userName+'\
					</picture>\
					<a href="#" class="remove-share" data-type="'+type+'" data-value="'+user_ID+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			';

		}

		function emailTemplate_html(email, type, deletable = true) {

			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'" data-to="'+ email +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="'+email+'"/>' : '') +'\
					<span>\
						'+email+'\
						<a href="#" class="remove-share" data-type="'+type+'" data-value="'+email+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
					</span>\
				</li>\
			';

		}


	    if(e.keyCode == 13) {

		    input.prop('disabled', true);


			// Start the process
			var actionID = newProcess(null, "userCheck");

			ajax('user-check', {

				'email'	: input.val(),
				'nonce'	: nonce

			}).done(function(result) {


				var data = result.data;
				console.log('DATA: ', data);


				if ( data.status == "found" || data.status == "not-found" ) {


					if ( !lists.find('[value="'+data.share_to+'"]').length && !$('.shares.project').children('[data-to="'+data.share_to+'"]').length ) {


						// Add if not already exists
						if ( data.status == "found" )
							userList.append( userTemplate_html(data.user_ID, data.user_photo, data.user_name, type) );

						if ( data.status == "not-found" )
							emailList.append( emailTemplate_html(input.val(), type) );


					}



					// If project sharing
					if (type == "project") {


						// Remove from the page list
						$('.shares.page').children('[data-to="'+data.share_to+'"]').remove();

					}



					input.removeClass('error');
					input.val('');


				} else {

					input.addClass('error');

				}


				input.prop('disabled', false);
				input.focus();


				// Finish the process
				endProcess(actionID);


			});



		    e.preventDefault();
		    return false;
	    }

	});


	// ADD NEW MODAL: Delete selected shared person from the list
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


	// ADD NEW MODAL: New screen
	$('.screen-add > li > a').click(function(e) {

		var listed_screen = $(this).parent();
		var listed_screen_cat = $(this).parents('.screen-cat');

		var screen_id = $(this).attr('data-screen-id');
		var screen_width = $(this).attr('data-screen-width');
		var screen_height = $(this).attr('data-screen-height');
		var screen_cat_name = $(this).attr('data-screen-cat-name');
		var screen_cat_icon = $(this).attr('data-screen-cat-icon');


		var new_screen_html = '\
			<li>\
				<input type="hidden" name="screens[]" value="'+screen_id+'"/>\
				<i class="fa '+ screen_cat_icon +'" aria-hidden="true"></i> <span>'+screen_cat_name+' ('+screen_width+' x '+screen_height+')</span>\
				<a href="#" class="remove-screen"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
			</li>\
		';



		if (screen_id == 11) {

			new_screen_html = '\
				<li>\
					<input type="hidden" name="screens[]" value="'+screen_id+'">\
					<input type="hidden" name="page_width" value="'+screen_width+'">\
					<input type="hidden" name="page_height" value="'+screen_height+'">\
					<i class="fa '+ screen_cat_icon +'" aria-hidden="true"></i> <span>Current Screen (<span class="screen-width">'+screen_width+'</span> x <span class="screen-height">'+screen_height+'</span>)</span>\
					<a href="#" class="remove-screen"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			';

		}




		$('.selected-screens').append(new_screen_html);


		listed_screen.hide();


		// Check if any other screen left in that category
		if ( !listed_screen.parent().children(':visible').length )
			listed_screen_cat.hide();


		// Show all the remove buttons
		$('.selected-screens a.remove-screen').show();


		e.preventDefault();
		return false;

	});


	// ADD NEW MODAL: Delete selected screen from the list
	$(document).on('click', '.selected-screens a.remove-screen', function(e) {


		var listed_screen = $(this).parent();
		var listed_screen_cat = $(this).parents('.screen-cat');

		var screen = listed_screen.find('input');
		var screen_id = screen.attr('value');
		var screen_from_list = $('.screen-add > li > a[data-screen-id="'+screen_id+'"]').parent();


		// Show in the list
		screen_from_list.show();


		// Show the category
		screen_from_list.parents('.screen-cat').show();


		// Remove the screen
		listed_screen.remove();


		// Count the selected screens and if less than 2, hide that remover
		if ( $('.selected-screens > li').length < 2 ) {

			$('.selected-screens a.remove-screen').hide();

		}


		console.log('REMOVE', screen_id);


		e.preventDefault();
		return false;

	});


	// Plain text paste on content editable blocks
	$('[contenteditable]').on('paste',function(e) {


		e.preventDefault();

		var plain_text = (e.originalEvent || e).clipboardData.getData('text/plain');

		if(typeof plain_text !== 'undefined')
			document.execCommand('insertText', false, plain_text);

		console.log('PASTED: ', plain_text);


	});


	// ACTIONS - Archive, delete, recover, rename, ...
	$(document).on('click', '[data-action]', function(e) {


		// Item details
		var item = $(this).parents('.item');
		var object_ID = item.attr('data-id') || null;
		var object_type = item.attr('data-type') || null;
		var firstParameter = item.attr('data-parameter') || null;
		var secondParameter = item.attr('data-second-parameter') || null;
		var thirdParameter = item.attr('data-third-parameter') || null;


		// Action details
		var action = $(this).attr('data-action') || null;
		var confirmText = $(this).attr('data-confirm') || false;


		// When renaming
		if (action == "rename") {

			var parent_item = item.find('.name-field');
			var input = item.find('input.edit-name');
			firstParameter = input.val();

			parent_item.toggleClass('editing');

			// If the same text
			if ( input.val() == input.attr('value') ) {
				e.preventDefault();
				return false;
			}

		}


		// If confirmed, send data
		if ( !confirmText || confirm(confirmText) )
			doAction(action, object_type, object_ID, firstParameter, secondParameter, thirdParameter);


		e.preventDefault();
		return false;

	});


	// Modal Opener
	$(document).on('click', '[data-modal]', function(e) {


		var modalName = $(this).attr('data-modal');
		var modal = $('#' + modalName);
		var currentUserId = modal.attr('data-currentuser-id');

		var dataType = $(this).attr('data-type');
		var object_ID = $(this).attr('data-id');
		var objectName = $(this).attr('data-object-name');
		var iamOwner = $(this).attr('data-iamowner');


		// Update the modal data
		modal.attr('data-type', dataType);
		modal.attr('data-id', object_ID);
		modal.attr('data-iamowner', iamOwner);


		// Update the fields
		modal.find('.data-type').text(dataType);
		modal.find('.data-name').text(objectName);



		// SHARE MODALS
		if (modalName == "share") {


			var input = modal.find('#share-email');


			// Reset the new access selector
			$('#share .new-access-type-selector > li').removeClass('selected');
			$('#share .new-access-type-selector > li:first-child').addClass('selected');
			var accessLabel = $('#share .new-access-type-selector > li:first-child > a').text();
			$('#share .new-access-type').text(accessLabel);


			// Reset the input and button
			input.attr('data-add-type', dataType);
			input.removeClass('error');
			input.val('');
			$('#share button.add-member').prop('disabled', true);


			updateShares();


		}


		// NEW PAGE/PROJECT MODALS
		if (modalName == "add-new") {


			// Update the project ID
			modal.find('input[name="project_ID"]').attr('value', object_ID);

			var thisBlock = $(this).parents('.block');
			var catID = thisBlock.prevAll('.cat-separator:first').attr('data-cat-id') || 0;
			var catName = thisBlock.prevAll('.cat-separator:first').find('.name').text();
			var orderNumber = thisBlock.prev('.block').attr('data-order') || 0;


			// Update the current category name
			modal.find('.to').html('');
			if (catName != "Uncategorized" && thisBlock.length)
				modal.find('.to').html("To <b>"+ catName +"</b> Section");


			// Print the project name
			if (!thisBlock.length && modalName == "add-new" && dataType == "page")
				modal.find('.to').html("To <b>"+ objectName +"</b> Project");


			// Category ID input update
			modal.find('input[name="category"]').attr('value', catID);


			// Order number input update
			modal.find('input[name="order"]').attr('value', ( typeof orderNumber !== 'undefined' ? parseInt(orderNumber) + 1 : 0 ));


		}


		// Open the modal
		openModal('#'+modalName);


		// Focus the element
		setTimeout(function() {

			if (modal.find('input[autofocus]').length) modal.find('input[autofocus]').focus();

		}, 500);


		e.preventDefault();
		return false;

	});


	// SHARE MODAL: Share input
	$('#share #share-email').on('keyup', function() {

		var inputVal = $(this).val();

		if ( inputVal.length > 0 ) {

			$('#share button.add-member').prop('disabled', false);

		} else {

			$('#share button.add-member').prop('disabled', true);

		}

	}).on('keydown', function (e) {


	    if(e.keyCode == 13) {

			addshare( $(this) );

		    e.preventDefault();
		    return false;
	    }

	});


	// SHARE MODAL: Add Member Button
	$('#share .add-member').on('click', function(e) {

		addshare( $('#share #share-email') );

		e.preventDefault();
		return false;
	});


	// SHARE MODAL: New member access type change
	$('#share .new-access-type-selector > li > a').on('click', function(e) {

		var newType = $(this).attr('data-type');
		var newLabel = $(this).text();


		// Update the share input
		$('#share #share-email').attr('data-add-type', newType);


		// Update the label
		$('#share .new-access-type').text(newLabel);


		// Update the selected item
		$('#share .new-access-type-selector > li').removeClass('selected');
		$(this).parent().addClass('selected');


		e.preventDefault();
		return false;
	});


	// Error inputs when typing
	$('input').keydown(function() {

		$(this).removeClass('error');

	});


	// Range sliders
	$('input[type="range"]').on('input change', function() {


		var value = $(this).val();
		//console.log('Changed', value);

		// Find the percentage
		var percentage = parseInt(value * 100);


		$(this).next('.percentage').text(percentage);


	});


	// Notifications
	$('.notification-opener').click(function(e) {

		$(this).toggleClass('open');

		e.preventDefault();
	});


	// Refresh Notifications
	$(document).on('click', '.refresh-notifications', function(e) {


		if ( $(this).hasClass('.notification-opener') && ! $(this).hasClass('open') ) return false;


		getNotifications(true);


		e.preventDefault();
	});


	// Load More Notifications
	$(document).on('click', '.more-notifications > a', function(e) {


		var offset = $(this).attr('data-offset');


		moreNotifications(offset);


		e.preventDefault();
	});


	// Start auto checking notifications
	startNotificationAutoRefresh();


	// Alert auto removal
    setTimeout(function() {

		dismissAlert( $('.alerts > .alert.success') );

	}, 4000);


	// Dismiss an alert
	$(document).on('click', '.alert > .close', function() {

		dismissAlert( $(this).parent() );

	});


});



// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: false
	});


});


// Update shares
function updateShares() {


	console.log('Updating the shares...');


	var modal = $('#share');
	var currentUserId = modal.attr('data-currentuser-id');


	// Update the modal data
	var dataType = modal.attr('data-type');
	var object_ID = modal.attr('data-id');
	var iamOwner = modal.attr('data-iamowner');


	// Clean the old data
	modal.find('.members').html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Loading...</span></div>');


	// Bring the users from DB
	ajax('shares-get', {

		id : object_ID,
		dataType : dataType

	}).done(function(result) {


		var users = result.users;
		//console.log('RESULTS:', result);


		// Clean the wrappers
		modal.find('.members').html('');

		$(users).each(function(i, user) {

			$('.people[data-type="'+ user.type +'"][data-id="'+ user.object_ID +'"]').html('');

		});




		// Append the users
		var ownerID;
		$(users).each(function(i, user) {

			if (user.mStatus == "owner") ownerID = user.user_ID;
			var skipUser = user.mStatus == "projectowner" && ownerID == user.user_ID ? true : false;


			// Modal info
			if (!skipUser) {

				modal.find('.members').append(
					new_modal_shared_member(user.mStatus, user.email, user.fullName, user.nameAbbr, user.userImageUrl, user.user_ID, dataType, user.type, currentUserId, user.sharer_user_ID, user.object_ID, object_ID)
				);

			}


			// Add to the people
			$('.people[data-type="'+ user.type +'"][data-id="'+ user.object_ID +'"]').append(
				boxMemberTemplate(user.mStatus, user.email, user.fullName, user.nameAbbr, user.userImageUrl, user.user_ID)
			);


		});



	}).fail(function(result) {


		console.log('FAILED:', result);


	});


	console.log('Shares updated');

}


// Add a share
function addshare() {


	var modal = $('#share');
	var type = modal.attr('data-type');
	var object_ID = modal.attr('data-id');
	var input = modal.find('#share-email');
	var addType = input.attr('data-add-type');
	var nonce = "";


	console.log('ADDING A SHARE: ', type, object_ID, input.val());


	// Disable the input
	input.prop('disabled', true);


	// Start the process
	var actionID = newProcess(null, type+"Share");

	ajax('share', {

		'data-type'	: type,
		'object_ID'	: object_ID,
		'email'		: input.val(),
		'add-type' 	: addType,
		'nonce'		: nonce

	}).done(function(result) {


		var data = result.data;
		console.log(result);


		// If user added
		if ( data.status == "added" ) {


			// Update the shares list
			updateShares();


			// Reset the input and button
			input.removeClass('error');
			input.val('');
			$('#share button.add-member').prop('disabled', true);


			// Scroll to the bottom
			modal.find('.mCustomScrollBox').animate({
				scrollTop: 999
			}, "slow");


		} else if ( data.status == "invalid-email" ) {

			input.addClass('error');

		} else {

			input.addClass('error ' + data.status);

		}


		// Reactivate the input
		input.prop('disabled', false);
		input.focus();



		// Finish the process
		endProcess(actionID);

	});


}


// Do an action
function doAction(action, object_type, object_ID, firstParameter = null, secondParameter = null, thirdParameter = null, nonce = "") {


	// Start progress bar action
	var actionID = newProcess(null, object_type+":"+action);

	// AJAX Send data
	ajax('data-action', {
		'ajax' 			  : true,
		'action' 		  : action,
		'data-type' 	  : object_type,
		'id' 			  : object_ID,
		'firstParameter'  : firstParameter,
		'secondParameter' : secondParameter,
		'thirdParameter'  : thirdParameter,
		'nonce' 		  : nonce
	}).done(function(result) {


		var data = result.data;
		var items = $('.item[data-type="'+object_type+'"][data-id="'+object_ID+'"]');


		console.log("RESPONSE: ", data);


		// Progressbar Update
		if ( data.status == "successful" ) {


			if (action == "rename") {


				items.find('input.edit-name').attr('value', firstParameter );
				items.find('.name').text( firstParameter );


			} else if (action == "archive" || action == "delete" || action == "remove" || action == "recover") {


				// Page redirection
				if (action == "remove" && firstParameter == "redirect")
					window.open(secondParameter, "_self");


				// Hide the item
				items.remove();


				// Update the add new blocks
				if ( object_type == "projectcategory" || object_type == "pagecategory" ) addNewPageButtons();


			} else if (action == "changeshareaccess" || action == "unshare" || action == "makeownerof") {


				// Update the shares list again
				updateShares();


				// Object ownership
				if (action == "makeownerof") {
					$('#share').attr('data-iamowner', 'no');
					$('[data-type="'+ object_type +'"][data-id="'+ object_ID +'"][data-modal="share"]').attr('data-iamowner', 'no');
				}


			} else {

				console.log(action + ' DONE');

			}


			// End the process
			endProcess(actionID);


		} else { // UNSUCCESSFUL


			if (action == "rename") {

				item.find('.name-field').addClass('editing ' + data.status);

			}


		}


	}).fail(function(error) {

		console.log('FAILED: ', error);

	});


}


// Get notifications
function getNotifications(markAsRead = false) {


	console.log('Getting notifications...');


	$('.notifications > ul').addClass('loading').html('<li><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Notifications are loading...</span></li>');



	ajax('notifications-get', {

		'offset' : 0,
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);
		var notificationsHTML = result.notifications;


		// Apply to DOM
		$('.notifications > ul').html(notificationsHTML).removeClass('loading');


		// Mark all as read
		if (markAsRead && $('.notifications > ul > li.new').length) {


			// After 1 seconds, mark all read
			setTimeout(function() {

				if ( $('.notification-opener').hasClass("open") ) readNotifications();

			}, 1000);


		}


	}).fail(function(e) {

		console.log('ERROR: ', e);

	});

	return true;

}


// Get more notifications
function moreNotifications(offset = 0) {


	console.log('Loading more notifications...');


	$('.notifications .more-notifications').html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Notifications are loading...</span>');


	ajax('notifications-get', {

		'offset' : parseInt(offset),
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);
		var notificationsHTML = result.notifications;


		// Remove the loading text
		$('.notifications .more-notifications').remove();


		// Apply to DOM
		$('.notifications > ul').append(notificationsHTML);



	}).fail(function(e) {


		$('.notifications .more-notifications').html('Notifications couldn\'t be loaded.');
		console.log('ERROR: ', e);

	});

	return true;

}


// Mark "read" all notifications
function readNotifications() {


	var IDsToRead = [];


	// Select all the notifications that's shown now
	$('.notifications > ul > li.new[data-type="notification"]').each(function() {

		var notification_ID = $(this).attr('data-id');

		IDsToRead.push(notification_ID);

	});


	// Don't send if no new notifications
	if (IDsToRead.length == 0) return true;


	// Send data
	ajax('notifications-read', {

		'notification_IDs' : IDsToRead,
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);


		// Delete the new dots
		$('.notifications > ul > li').removeClass('new');


		var currentNotifNumber = parseInt( $('.notification-opener .notif-no').text() );
		var newNotifNumber = currentNotifNumber - IDsToRead.length;


		// Update the count
		$('.notification-opener .notif-no').text(newNotifNumber).addClass( newNotifNumber == 0 ? "hide" : "" );


	}).fail(function(e) {

		console.log('ERROR: ', e);

	});

	return true;


}


// Get notification count
function getNewNotificationCount() {


	ajax('notifications-get-count', {

		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);

		var newCount = result.newcount;


		// Update the count
		$('.notification-opener .notif-no').text(newCount);
		if (newCount == 0) $('.notification-opener .notif-no').addClass('hide');
		else $('.notification-opener .notif-no').removeClass('hide');


		// Add the "Load new notifications" button
		var newButton = '<a href="#" class="refresh-notifications">'+ newCount +' New Notification'+ (newCount > 1 ? "s" : "") +'</a>';
		if (newCount > 0) {


			if ( !$('.notifications > ul > li.refresh-notifications').length )
				$('.notifications > ul:first-child').prepend('<li class="refresh-notifications"></li>');

			$('.notifications > ul > li.refresh-notifications').html(newButton);


		}


	}).fail(function(e) {

		console.log('ERROR: ', e);

	});

	return true;


}


// Start auto-refresh notifications
function startNotificationAutoRefresh() {

	if (!loggedIn) return false;

	console.log('AUTO-REFRESH NOTIFICATIONS STARTED');

	notificationAutoRefreshTimer = setInterval(function() {

		console.log('Auto checking the notifications...');


		// Abort the latest request if not finalized
		if(notificationAutoRefreshRequest && notificationAutoRefreshRequest.readyState != 4) {
			console.log('Latest request aborted');
			notificationAutoRefreshRequest.abort();
		}


		// Get the up-to-date pins
		getNewNotificationCount();


	}, notificationAutoRefreshInterval);

}


// Stop auto-refresh
function stopNotificationAutoRefresh() {

	console.log('AUTO-REFRESH NOTIFICATIONS STOPPED');

	if (notificationAutoRefreshRequest) notificationAutoRefreshRequest.abort();

	clearInterval(notificationAutoRefreshTimer);

}


// Dismiss alerts
function dismissAlert(selector) {

    selector.fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });

}




// MODALS:
// Open modal
function openModal(modalElement) {

	$('body').addClass('popup-open');
	$(modalElement).addClass('active');

}

// Close modal
function closeModal(modalElement) {

	$(modalElement).removeClass('active');
	$('body').removeClass('popup-open');

}



// TEMPLATES
function boxMemberTemplate(mStatus, email, fullName, nameAbbr, userImageUrl, user_ID, unremoveable) {

	var hasPic = 'class="has-pic"';
	var printPic = 'style="background-image: url('+userImageUrl+');"';
	var ownerBadge = '';

	if (mStatus != 'email' ) email = '('+email+')';
	if (mStatus == 'email' ) nameAbbr = '<i class="fa fa-envelope" aria-hidden="true"></i>';
	if (mStatus == 'email' ) user_ID = email;
	if (mStatus == 'owner' ) ownerBadge = '<span class="owner-badge">Owner</span>';
	if (userImageUrl == "" || userImageUrl == null) hasPic = printPic = "";

	return '\
		<picture class="profile-picture" '+printPic+' data-tooltip="'+fullName+'" data-type="user" data-id="'+user_ID+'">\
			<span '+hasPic+'>'+nameAbbr+'</span>\
		</picture>\
	';

}

function new_modal_shared_member(mStatus, email, fullName, nameAbbr, userImageUrl, user_ID, dataType, type, currentUserId, sharer_user_ID, object_ID, page_ID) {

	console.log(email, dataType);

	var hasPic = userImageUrl != null ? "has-pic" : "";
	var printPicture = hasPic == "has-pic" ? "style='background-image: url("+ userImageUrl +")'" : "";

	var shareText = "This " + dataType;
	if (mStatus == "owner") shareText = dataType + " Owner";
	if (mStatus == "projectowner") shareText = "Project Owner";
	if (mStatus == "project") shareText = "Whole Project";


	return '\
		<li class="wrap xl-flexbox xl-middle xl-gutter-16 member item" data-type="user" data-id="'+ user_ID +'" data-parameter="'+ type +'" data-second-parameter="'+ object_ID +'" data-third-parameter="'+ page_ID +'" data-share-status="'+ mStatus +'" data-itsme="'+ ( user_ID == currentUserId ? "yes" : "no" ) +'" data-my-share="'+ ( sharer_user_ID == currentUserId ? "yes" : "no" ) +'">\
			<div class="col xl-8-12">\
				<div class="wrap xl-flexbox xl-middle xl-gutter-8">\
					<div class="col xl-2-12">\
						<figure class="profile-picture '+ hasPic +'" '+ printPicture +'>\
							<span class="abbr">'+ nameAbbr +'</span>\
						</figure>\
					</div>\
					<div class="col xl-10-12">\
						<span class="full-name">'+ fullName +'</span>\
						<span class="email">('+ email +')</span>\
						<span class="owner-badge">ME</span>\
					</div>\
				</div>\
			</div>\
			<div class="col xl-4-12 text-uppercase dropdown access">\
				<a href="#">'+ shareText +' <i class="fa fa-caret-down change-access"></i></a>\
				<ul class="no-delay right selectable change-access">\
					<li class="'+ ( mStatus == "shared" ? "selected" : "" ) +' hide-if-me"><a href="#" data-action="changeshareaccess">THIS '+dataType+'</a></li>\
					<li class="'+ ( mStatus == "project" ? "selected" : "" ) +' hide-if-me hide-when-project" data-action="changeshareaccess"><a href="#">WHOLE PROJECT</a></li>\
					<li class="'+ ( mStatus == "owner" ? "selected" : "" ) +' hide-if-not-owner"><a href="#" data-action="makeownerof" data-confirm="Are you sure you want to make this user owner of this '+dataType+'?">'+dataType+' OWNER</a></li>\
					<li><a href="#" data-action="unshare" data-confirm="Are you sure you want to remove access for this user?">REMOVE ACCESS</a></li>\
				</ul>\
			</div>\
		</li>\
	';

}



// HELPERS:
function currentUrl() {

	return window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;

}

function removeQueryArgFromCurrentUrl(arg) {


	// If being force reinternalizing, update the URL
	if (history.replaceState) {
	    var newurl = queryParameter(currentUrl(), arg, "");
	    if (newurl != currentUrl()) window.history.replaceState({path:newurl},'',newurl);
	}


}

function cleanHTML(s, allowBRs = false) {

	if (allowBRs) {

		s = s.replace(/<(br)[^>]+>/ig,'<$1>');
		return s.replace(/(<(?!br\s*\/?)[^>]+>)/ig,"");

	}


	return s.replace(/(<([^>]+)>)/ig,"");
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function queryParameter(url, key, value = null) {


	var urlParsed = new URL(url);
	var query_string = urlParsed.search;
	var search_params = new URLSearchParams(query_string);


	if (value == "") search_params.delete(key);
	else search_params.set(key, value);


	if (value == null) return getParameterByName(key, url);


	urlParsed.search = search_params.toString();
	var new_url = urlParsed.toString();

	return new_url;
}

function timeSince(date) {

  var seconds = Math.floor((new Date() - date) / 1000);

  var interval = Math.floor(seconds / 31536000);

  if (interval > 1) {
    return interval + " years";
  }
  interval = Math.floor(seconds / 2592000);
  if (interval > 1) {
    return interval + " months";
  }
  interval = Math.floor(seconds / 86400);
  if (interval > 1) {
    return interval + " days";
  }
  interval = Math.floor(seconds / 3600);
  if (interval > 1) {
    return interval + " hours";
  }
  interval = Math.floor(seconds / 60);
  if (interval > 1) {
    return interval + " minutes";
  }
  //return Math.floor(seconds) + " seconds";
  return "about a minute";
}

function ajax(type, givenData = {}) {

	givenData['type'] = type;

	return $.ajax({
		method: "POST",
		url: ajax_url,
		data: givenData,
		//async: false,
		dataType: 'json',
		timeout: 10000
	});

}

function log(log, arg1) {
	//console.log(log, arg1);
}