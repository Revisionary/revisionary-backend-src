var webpage = require('webpage').create(),
	system = require('system'),
	url, width, height, output_image, output_html;
var fs = require('fs');

url = system.args[1];
width = system.args[2];
height = system.args[3];
output_image = system.args[4];
//if (system.args[5].length === 1) output_html = system.args[5];

// Viewport Size
webpage.viewportSize = { width: width, height: height };

// When opened
webpage.onLoadFinished = function(status){

	if (status == "success") {

		console.log("The title of the page is: "+ webpage.title);

		// Four seconds later
		window.setTimeout(function () {

			// Output the screenshot
			webpage.render(output_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING

			// Output the HTML
			//if (system.args[5].length === 1) fs.write(output_html, webpage.content, 'w'); // !!! SEPARATE THIS AND CAPTURING

			webpage.close();
			slimer.exit();

		}, 4000);


	} else {
		console.log("Sorry, the page is not loaded");

		webpage.close();
		slimer.exit();
	}


};

// Open
webpage.open(url);