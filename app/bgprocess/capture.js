var webpage = require('webpage').create(),
	system = require('system'),
	url, width, height, output_image, output_html;
var fs = require('fs');

url = system.args[1];
width = system.args[2];
height = system.args[3];
output_image = system.args[4];
if (system.args[5].length) output_html = system.args[5];

webpage.viewportSize = { width: width, height: height };

webpage.open(url).then(function(status){

	if (status == "success") {

		console.log("The title of the page is: "+ webpage.title);

		// Two seconds later
		window.setTimeout(function () {

			// Output the screenshot
			webpage.render(output_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING

			// Output the HTML
			if (system.args[5].length) fs.write(output_html, webpage.content, 'w'); // !!! SEPARATE THIS AND CAPTURING

			webpage.close();
			slimer.exit();

		}, 2000);


	} else {
		console.log("Sorry, the page is not loaded");

		webpage.close();
		slimer.exit();
	}


});