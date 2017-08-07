const chromeLauncher = require('chrome-launcher');

const CDP = require('chrome-remote-interface');
const argv = require('minimist')(process.argv.slice(2));
const fs = require('fs');
const util = require('util');


// CLI Args
const url = argv.url || 'https://www.google.com';
const format = argv.format === 'jpeg' ? 'jpeg' : 'png';
const viewportWidth = argv.viewportWidth || 1440;
const viewportHeight = argv.viewportHeight || 900;
const userAgent = argv.userAgent;
const fullPage = argv.full;
const delay = argv.delay || 0;


if(fullPage){
  console.log("Will capture full page")
}


// Delay Function
function wait(ms) {
	return new Promise(r => setTimeout(r, ms)).then(() => "Yay");
}

// Optional: set logging level of launcher to see its output.
// Install it using: yarn add lighthouse-logger
// const log = require('lighthouse-logger');
// log.setLevel('info');

/**
 * Launches a debugging instance of Chrome.
 * @param {boolean=} headless True (default) launches Chrome in headless mode.
 *     False launches a full version of Chrome.
 * @return {Promise<ChromeLauncher>}
 */
function launchChrome(headless=true) {
  return chromeLauncher.launch({
	chromePath: "/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome", // Activate and change this if you have more than one type of Chrome
	port: 9222, // Uncomment to force a specific port of your choice.
	chromeFlags: [
		//'--window-size='+viewportWidth+','+viewportHeight,
		'--disable-gpu',
		'--hide-scrollbars',
		headless ? '--headless' : ''
	]
  });
}

launchChrome().then(chrome => {
	console.log(`Chrome debuggable on port: ${chrome.port}`);

	//chrome --headless --disable-gpu --hide-scrollbars --window-size=1440,900 --screenshot=screen.png https://www.twelve12.com/



	CDP(async (client) => {


		// Extract used DevTools domains.
	    const {DOM, Emulation, Network, Page, Runtime, Security} = client;


	    // ignore all the certificate errors
	    Security.certificateError(({eventId}) => {
	        Security.handleCertificateError({
	            eventId,
	            action: 'continue'
	        });
	    });


	    try {


		    // Enable events on domains we are interested in.
	        await Page.enable();
			await DOM.enable();
			await Network.enable();
			await Security.enable();


	        // Enable the SSL override
	        await Security.setOverrideCertificateErrors({override: true});


	        // Disable cache?
	        //await Network.setCacheDisabled({cacheDisabled: true});


			// If user agent override was specified, pass to Network domain
			if (userAgent) {
				await Network.setUserAgentOverride({userAgent});
			}


			// Set up viewport resolution, etc.
			const deviceMetrics = {
				width: viewportWidth,
				height: viewportHeight,
				deviceScaleFactor: 0,
				mobile: false,
				fitWindow: false,
			};
			await Emulation.setDeviceMetricsOverride(deviceMetrics);
			await Emulation.setVisibleSize({width: viewportWidth, height: viewportHeight});


			// setup handlers
		    Network.requestWillBeSent((params) => {
		        //console.log(params.request.url);
		    });


			// Navigate to target page
	        await Page.navigate({url});


	        // Wait for page load event to take screenshot
	        await Page.loadEventFired();


/*
	        // Save the HTML ?
	        const result = await Runtime.evaluate({
	            expression: 'document.documentElement.outerHTML'
	        });
	        const html = result.result.value;
	        fs.writeFile("index.html", html, 'utf8', function(err) {

				if (err) {

		        	console.error(err);

		        } else {

			        console.log('HTML Saved');

			    }

			});
*/


			// Full page screenshots
			if (fullPage) {
				const {root: {nodeId: documentNodeId}} = await DOM.getDocument();
				const {nodeId: bodyNodeId} = await DOM.querySelector({
					selector: 'body',
					nodeId: documentNodeId,
				});

				const {model: {height}} = await DOM.getBoxModel({nodeId: bodyNodeId});
				await Emulation.setVisibleSize({width: viewportWidth, height: height});
				await Emulation.setDeviceMetricsOverride({
					width: viewportWidth,
					height: height,
					screenWidth: viewportWidth,
					screenHeight: height,
					deviceScaleFactor: 1,
					fitWindow: false,
					mobile: false
				});
				await Emulation.setPageScaleFactor({pageScaleFactor:1});
			}


			// If delay needed
			if (delay > 0) console.log('Waiting ' + delay + ' miliseconds');
			await wait(delay);


	        const {data} = await Page.captureScreenshot({
		        format: format,
		        fromSurface: true,
		        quality: 80,
/*
		        clip: {
			        width: 500,
			        height: 500,
			        x: 10,
			        y: 400,
			        scale: 3
		        }
*/
		    });

	        fs.writeFile('screenshot.' + format, Buffer.from(data, 'base64'), 'base64', function(err) {

		        if (err) {

		        	console.error(err);

		        } else {

		        	console.log('Screenshot saved');

		        }

		    });


	    } catch (err) {

	        console.error(err);

	    } finally {

	        await client.close();
	        chrome.kill();

	    }


	}).on('error', (err) => {

	    console.error(err);

	});









  //chrome.kill();
}).catch((err) => {

	console.log(err);

});