// PROCESS VARS
// Existing Process ??
var processExists = false;
var processExistsText = "Changes you made may not be saved.";
var processCount = 0;

// Elements
var progressBar;
var processes;





$(document).ready(function() {

	// Elements
	progressBar = $('.progress-bar');

	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;

});




// FUNCTION: Start Process
function newProcess() {

	var newProcessID = processCount + 1;

	// Add the new process
	progressBar.append('<div class="progress process" data-process-id="'+ newProcessID +'"></div>');

	// The new process
	var process = $('.process[data-process-id="'+ newProcessID +'"]:not(.done)');

	// Reset the progress bar
	process.css('width', '0%');


	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;


	// Prevent closing window
	window.onbeforeunload = function() {
		return processExistsText;
	};


	console.log('Process Count: ', processCount);
	return newProcessID;
}

function editProcess(processID, percentage) {

	var process = $('.process[data-process-id="'+ processID +'"]:not(.done)');

	// Change the progress bar
	process.css('width', percentage+'%');


	console.log('Process Count: ', processCount);
}


// FUNCTION: Finishes the process
function endProcess(processID) {

	var process = $('.process[data-process-id="'+ processID +'"]:not(.done)');


	// Fill the progress bar
	process.css('width', '100%').addClass('done');


	// Remove the process
	process.fadeOut(300, function() {
		$(this).remove();
	});


	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;


	// Allow closing window
	if (processCount == 0) window.onbeforeunload = null;


	console.log('Process Count: ', processCount);

}