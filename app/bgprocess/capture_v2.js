var webpage = require('webpage').create(),
	system = require('system'),
	fs = require('fs'),
	url, width, height, page_image, output_html;


// Get arguments
url = system.args[1];
width = system.args[2];
height = system.args[3];
page_image = system.args[4];
project_image = system.args[5];
log_dir = system.args[6];



// Viewport size
webpage.viewportSize = { width: width, height: height };


// Open the site
webpage.open(url);


// Requested resources
webpage.onResourceRequested = function(requestData, networkRequest) {
    //console.log(JSON.stringify(requestData));
};


// Received resources
webpage.onResourceReceived = function(response) {
    //console.log(JSON.stringify(response));
    //console.log(response.url);
    fs.write(log_dir + '/resources.log', response.contentType + ' -> ' + response.url + '\r\n', 'a');
};


// When completely loaded
webpage.onLoadFinished = function(status){

	if (status == "success") {


/*
		// Two seconds later
		window.setTimeout(function () {



		}, 2000);
*/



		// Four seconds later
		window.setTimeout(function () {

			var date = new Date();
			var logDate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

			// Output the page screenshot
			if (page_image != "done") {
				webpage.render(page_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING
				console.log("["+logDate+"] - Page is captured: '" + page_image + "'");
			}

			// Output the project screenshot
			if (project_image != "done") {
				webpage.render(project_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING
				console.log("["+logDate+"] - Project is captured: '" + project_image + "'");
			}

			// Output the HTML
			//if (system.args[5].length === 1) fs.write(output_html, webpage.content, 'w'); // !!! SEPARATE THIS AND CAPTURING


			// All the resources are written
			fs.write(log_dir + '/resources.log', 'DONE \r\n', 'a');


			webpage.close();
			slimer.exit();

		}, 4000);


	} else {
		console.log("["+logDate+"] - Sorry, the page is not loaded");

		webpage.close();
		slimer.exit();
	}

};