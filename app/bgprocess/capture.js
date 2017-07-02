var webpage = require('webpage').create(),
	system = require('system'),
	url, width, height, output;

url = system.args[1];
width = system.args[2];
height = system.args[3];
output = system.args[4];

webpage.viewportSize = { width: width, height: height };

webpage.open(url).then(function(){

	window.setTimeout(function () {

		webpage.render(output, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING
		slimer.exit();

	}, 2000);

});