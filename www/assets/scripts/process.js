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
function newProcess() { // Add timeout function here !!!

	var newProcessID = processCount + 1;


	// Stop auto-refresh
	if (typeof stopAutoRefresh === "function") stopAutoRefresh();


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
	process.fadeOut(1000, function() {
		$(this).remove();
	});


	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;


	// If no process left
	if (processCount == 0) {

		// Allow closing window
		window.onbeforeunload = null;


		// Restart auto-refresh
		if (typeof startAutoRefresh === "function" && page_ready) startAutoRefresh();

	}


	console.log('Process Count: ', processCount);

}