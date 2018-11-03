$(function() {


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

		if (!dataType || dataType == null) var dataType = $(this).attr('data-type');

		var projectShare = $(this).hasClass('project');

		var theBox = projectShare && dataType == "page" ? $('.under-main-title') : $(this).parents('.block');
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







});



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
		dataType: 'json'
	});

}

function log(log, arg1) {
	//console.log(log, arg1);
}