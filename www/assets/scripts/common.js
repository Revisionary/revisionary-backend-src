$(function() {


	// Prevent clicking '#' links
	$('a[href="#"]').click(function(e) {
		e.preventDefault();
	});


	// New Page/Project Modal
	$(document).on('click', '.add-new-box', function(e) {

		var type = $(this).attr('data-type');

		var modalSelector = '#add-new-'+ type;

		openModal(modalSelector);

		var thisBlock = $(this).parent().parent();
		var catID = thisBlock.prevAll('.cat-separator:first').attr('data-cat-id') || 0;
		var catName = thisBlock.prevAll('.cat-separator:first').find('.name').text();
		var orderNumber = thisBlock.prev('.block').attr('data-order') || 0;

		$(modalSelector + ' .to').html("To <b></b> Section");
		$(modalSelector + ' .to > b').text(catName);

		if (catName == "Uncategorized")
			$(modalSelector + ' .to').html("");


		// Category ID input update
		$(modalSelector + ' input[name="category"]').attr('value', catID);


		// Order number input update
		$(modalSelector + ' input[name="order"]').attr('value', ( typeof orderNumber !== 'undefined' ? parseInt(orderNumber) + 1 : 0 ));


		// Focus to the input
		setTimeout(function() {

			$(modalSelector + ' input[autofocus]').focus();

		}, 500);


		e.preventDefault();
		return false;

	});


	// Close Modal
	$('.cancel-button').on('click', function(e) {

		var popup = $(this).parents('.popup-window').attr('id');

		closeModal('#' + popup);

		e.preventDefault();
		return false;

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



	// Share Modal
	$(document).on('click', '.share-button', function(e) {

		dataType = $(this).attr('data-type') || dataType;

		var projectShare = $(this).hasClass('project');

		var theBox = projectShare && dataType == "page" ? $('.under-main-title') : $(this).parents('.block').first();
		var boxName = projectShare && dataType == "page" ? $('h1.project-title').text() : theBox.find('.name').text();
		var objectID = projectShare && dataType == "page" ? $('h1.project-title').attr('data-id') : theBox.attr('data-id');

		if (!objectID) objectID = $(this).attr('data-object-id');


		console.log('Data Type: ', dataType); console.log('projectShare: ', projectShare); console.log('OBJECT ID: ', objectID);


		// Change the name
		if (boxName) $('#share .to > b').text(boxName);


		// Add the ID
		$('.share-email').attr('data-id', objectID);


		// Page or project modal?
		$('#share-email').attr('data-type', projectShare ? "project" : "page");


		// Hide PHP project shares
		if (projectShare)
			$('#share .project-shares').hide();
		else
			$('#share .project-shares').show();


		// Correct the title
		$('#share .data-type').text(dataType == 'project' || projectShare ? 'Project' : 'Page');


		// Remove the old people
		$('#share .members:not(.project-php):not(.page-php)').html('');


		// Add the people !!! Fill the members from DB !!!
		if (theBox.length) {

			theBox.find('.people > a').each(function(i, member) {

				var mStatus = $(member).attr('data-mstatus');
				var email = $(member).attr('data-email');
				var fullName = $(member).attr('data-fullname');
				var nameabbr = $(member).attr('data-nameabbr');
				var userImageUrl = $(member).attr('data-avatar');
				var user_ID = $(member).attr('data-userid');
				var unremoveable = $(member).attr('data-unremoveable');


				$('#share .members:not(.project-php)').append(
					memberTemplate(mStatus, email, fullName, nameabbr, userImageUrl, user_ID, unremoveable, objectID)
				);

			});

		}


		// Open the modal
		openModal('#share');


		e.preventDefault();
		return false;

	});


	// Share input
	$('#share .share-email').on('keyup', function() {

		var inputVal = $(this).val();

		if ( inputVal.length > 0 ) {

			$('#share button.add-member').prop('disabled', false);

		} else {

			$('#share button.add-member').prop('disabled', true);

		}

	});


	// Add Member Button
	$('.add-member').on('click', function(e) {

		addShare( $('#share-email') );

		e.preventDefault();
		return false;
	});


	// Add new member
	$('.share-email').keydown(function (e){


	    if(e.keyCode == 13) {

			addShare( $(this) );

		    e.preventDefault();
		    return false;
	    }

	});


	function addShare(element) {

		var input = element;
		var type = element.attr('data-type');
		var objectID = element.attr('data-id');
		var theBox = type == 'project' && $('.under-main-title .people').length ? $('.under-main-title') : $('.block[data-id="'+objectID+'"]');
		var memberList = $('.members');



	    input.prop('disabled', true);

	    console.log(type, nonce, objectID, input.val());


		// Start the process
		var actionID = newProcess();

		// AJAX Send data
		$.post(ajax_url, {

			'type'		: 'share',
			'data-type'	: type,
			'nonce'		: nonce,
			'object_ID'	: objectID,
			'email'		: input.val()

		}, function(result){

			$.each(result.data, function(key, data){


				console.log(key, data);


				// If user added
				if ( data.status == "user-added" ) {

					// Add to members
					$('#share .members:not(.project-php)').append(
						memberTemplate('user', input.val(), data.user_fullname, data.user_nameabbr, data.user_avatar, data.user_ID, "", objectID)
					);


					// Add to the box
					if (theBox.length) {

						theBox.find('.people').append(
							memberTemplateSmall('user', input.val(), data.user_fullname, data.user_nameabbr, data.user_avatar, data.user_ID, "")
						);

					}


					input.removeClass('error');
					input.val('');
					$('#share button.add-member').prop('disabled', true);


				} else if ( data.status == "email-added" ) {

					// Add to members
					$('#share .members:not(.project-php)').append(
						memberTemplate('email', input.val(), '', '', '', '', '', objectID)
					);


					// Add to the box
					if (theBox.length) {

						theBox.find('.people').append(
							memberTemplateSmall('email', input.val(), '', '', '', '', '')
						);

					}


					input.removeClass('error');
					input.val('');
					$('#share button.add-member').prop('disabled', true);

				} else if ( data.status == "invalid-email" ) {

					input.addClass('error');

				} else {

					input.addClass(data.status);

				}

				input.prop('disabled', false);


				// Finish the process
				endProcess(actionID);

			});

		}, 'json');


    }


	// Unshare Member
	$(document).on('click', '.remove-member', function(e) {

		var type = $('#share-email').attr('data-type');
		var member = $(this).parent();
		var memberID = $(this).attr('data-userid');
		var objectID = $(this).attr('data-id');


		console.log(memberID, objectID);


		if ( confirm('Are you sure you want to unshare?') ) {

			// Start the process
			var actionID = newProcess();

			// AJAX Send data
			$.post(ajax_url, {

				'type'		: 'unshare',
				'data-type'	: type,
				'nonce'		: nonce,
				'object_ID'	: objectID,
				'user_ID'	: memberID

			}, function(result){

				$.each(result.data, function(key, data){

					console.log(key, data);

					// If member is unshared
					if ( data.status == "unshared" ) {

						// Remove the member
						member.remove();


						// Remove from box people
						$('.block[data-id="'+objectID+'"] .people a[data-userid="'+memberID+'"]').remove();
						$('.block[data-id="'+objectID+'"] .people a[data-email="'+memberID+'"]').remove();

						// If project members, remove from the project members section under the title
						if ( type == "project" && $('.under-main-title .people').length ) {
							$('.under-main-title .people a[data-userid="'+memberID+'"]').remove();
							$('.under-main-title .people a[data-email="'+memberID+'"]').remove();
						}


						// Finish the process
						endProcess(actionID);

					}


				});

			}, 'json');

		}



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
			var actionID = newProcess();

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

					} else {

					}

					input.prop('disabled', false);

					// Finish the process
					endProcess(actionID);

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



	// Add new screen to modal
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


	// Delete selected screen from the list
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


	// ACTIONS - Archive, delete, recover, rename, ...
	$('[data-action]').click(function(e) {

		var object_ID = $(this).attr('data-id') || null;
		var object_type = $(this).attr('data-type') || null;
		var action = $(this).attr('data-action') || null;


		if (action == "rename") {

			var parent_item = $(this).parent().parent();

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
			confirm_text = `Are you sure you want to archive this ${object_type}?`;

		if ( action =='delete' )
			confirm_text = `Are you sure you want to delete this ${object_type}?`;

		if ( action =='recover' )
			confirm_text = `Are you sure you want to recover this ${object_type}?`;

		if ( action =='remove' )
			confirm_text = `Are you sure you want to completely remove this ${object_type}? Keep in mind that no one will be able to access this ${object_type}`+ (object_type == 'project' ? ' and its pages' : '') +` anymore!`;



		// If confirmed, send data
		if (action == "rename" || confirm(confirm_text) ) {

			var url = $(this).attr('href');
			var block = $(this).parents('.block').first();
			var itemsToHide = $('.item[data-type="'+object_type+'"][data-id="'+object_ID+'"]');


			// Start progress bar action
			var actionID = newProcess();

			// AJAX Send data
			$.get(url, {ajax:true, inputText: ( action == "rename" ? input.val() : '' )}, function(result){

				$.each(result.data, function(key, data){

					console.log(key, data);

					// Progressbar Update
					if ( data.status == "successful" ) {


						if (action == "rename") {


							input.attr('value', input.val() );
							itemsToHide.find('.name').text( input.val() );


						} else if (action == "archive" || action == "delete" || action == "remove" || action == "recover") {

							itemsToHide.remove();

							// Update the add new blocks
							if ( object_type == "category" ) addNewPageButtons();


						} else {


							console.log('Done!');


						}

						// End the process
						endProcess(actionID);


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


	// Plain text paste on content editable blocks
	$('[contenteditable]').on('paste',function(e) {


		e.preventDefault();

		var plain_text = (e.originalEvent || e).clipboardData.getData('text/plain');

		if(typeof plain_text !== 'undefined')
			document.execCommand('insertText', false, plain_text);

		console.log('PASTED: ', plain_text);


	});


});



// When everything is loaded
$(window).on("load", function (e) {


	// Pins Section Content
	$(".scrollable-content").mCustomScrollbar({
		alwaysShowScrollbar: false
	});


});


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
function memberTemplate(mStatus, email, fullName, nameabbr, userImageUrl, user_ID, unremoveable, objectID) {

	var hasPic = 'class="has-pic"';
	var printPic = 'style="background-image: url('+userImageUrl+');"';
	var ownerBadge = '';

	if (mStatus != 'email' ) email = '('+email+')';
	if (mStatus == 'email' ) nameabbr = '<i class="fa fa-envelope" aria-hidden="true"></i>';
	if (mStatus == 'email' ) user_ID = email;
	if (mStatus == 'owner' ) ownerBadge = '<span class="owner-badge">Owner</span>';
	if (userImageUrl == "") hasPic = printPic = "";

	return '\
		<li class="inline-guys member '+mStatus+' '+unremoveable+'">\
			<picture class="profile-picture big" '+printPic+'>\
				<span '+hasPic+'>'+nameabbr+'</span>\
			</picture>\
			<div>\
				<span class="full-name">'+fullName+'</span>\
				<span class="email">'+email+'</span>\
				'+ownerBadge+'\
			</div>\
			<a href="#" class="remove remove-member" data-userid="'+user_ID+'" data-id="'+objectID+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
		</li>\
	';

}

function memberTemplateSmall(mStatus, email, fullName, nameabbr, userImageUrl, user_ID, unremoveable) {

	var hasPic = 'class="has-pic"';
	var printPic = 'style="background-image: url('+userImageUrl+');"';

	if (userImageUrl == "") hasPic = printPic = "";

	return '\
		<a href="#" data-tooltip="'+(mStatus == 'email' ? email : fullName)+'" data-mstatus="'+mStatus+'" data-fullname="'+fullName+'" data-nameabbr="'+nameabbr+'" data-email="'+email+'" data-avatar="'+userImageUrl+'" data-userid="'+user_ID+'" data-unremoveable="'+unremoveable+'">\
			<picture class="profile-picture" '+printPic+'>\
				<span '+hasPic+'>'+(mStatus == 'email' ? '<i class="fa fa-envelope" aria-hidden="true"></i>' : nameabbr)+'</span>\
			</picture>\
		</a>\
	';

}



// HELPERS:
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