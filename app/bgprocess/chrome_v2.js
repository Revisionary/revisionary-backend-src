// CLI Direct Screenshot:
// chrome --headless --disable-gpu --hide-scrollbars --window-size=1440,900 --screenshot=screen.png https://www.twelve12.com/


const puppeteer = require('puppeteer');
const devices = require('puppeteer/DeviceDescriptors');

const argv = require('minimist')(process.argv.slice(2));
const fs = require('fs');
const util = require('util');


// CLI Args
const url = argv.url || 'https://www.google.com';
const viewportWidth = argv.viewportWidth || 1440;
const viewportHeight = argv.viewportHeight || 900;


const pageScreenshot = argv.pageScreenshot || 'done';
const projectScreenshot = argv.projectScreenshot || 'done';
const htmlFile = argv.htmlFile || 'done';
const resourcesFile = argv.resourcesFile || 'done';
const logDir = argv.logDir;


const format = argv.format === 'png' ? 'png' : 'jpeg';
const userAgent = argv.userAgent || false;
const fullPage = argv.full;
const delay = argv.delay || 0;


// Console Info
console.log('URL: ' + url);
if(fullPage) console.log("Will capture full page");

if(delay > 0) console.log("Will delay for " + delay + "miliseconds");

console.log("Width: " + viewportWidth + " Height: " + viewportHeight);
if (userAgent) console.log("User Agent: " + userAgent);


// Delay Function
function wait(ms) {
	return new Promise(r => setTimeout(r, ms)).then(() => "Yay");
}


(async() => {


	// Launch the Chrome Browser
	const browser = await puppeteer.launch({
		//executablePath: '/Applications/Google\ Chrome\ Canary.app/Contents/MacOS/Google\ Chrome\ Canary',
		headless: true
	});



	// Open a new tab
	const page = await browser.newPage();
	await page.setRequestInterceptionEnabled(true);



	// List the requests to the requests file
	page.on('request', request => {

		// Write to the resources file
		fs.appendFile(resourcesFile, request.url + ' \r\n', (err) => {

			if (err) console.log(err);
			console.log(request.resourceType + ' Resource added: ' + request.url);

		});

		console.log(request.resourceType + ' -> ' + request.url);

		request.continue();
	});



	// Apply the device !!!
	//await page.emulate(devices['iPhone 6']);



	// Set the size
	await page.setViewport({
		width: viewportWidth,
		height: viewportHeight,
	});



	// If user agent override was specified, pass to Network domain
	if (userAgent) {
		await page.setUserAgent(userAgent);
	}


	// Navigate to the URL
	const response = await page.goto(url, {waitUntil: 'networkidle'});
	const html = await response.text();


	// Save the HTML if not already
	if ( htmlFile != "done" ) {


		fs.writeFile(htmlFile, html, (err) => {

			if (err) console.log(err);
			console.log('HTML file has been created: ' + htmlFile);

		});


	}


	// If delay needed
	if (delay > 0) console.log('Waiting ' + delay + ' miliseconds');
	await wait(delay);


/*
	// Take a screenshot
	await page.screenshot({
		type: format,
		quality: 80,
		path: 'screenshot.' + format,
	});
*/


	// Take the page screenshot if not already
	if ( pageScreenshot != "done" ) {


		// Take a screenshot
		await page.screenshot({
			type: format,
			quality: 80,
			path: pageScreenshot,
		});

		await console.log('Page screenshot saved');


	}


	// Take the project screenshot if not already
	if ( projectScreenshot != "done" ) {


		// Take a screenshot
		await page.screenshot({
			type: format,
			quality: 80,
			path: projectScreenshot,
		});

		await console.log('Project screenshot saved');


	}



	// Write to the resources file
	fs.appendFile(resourcesFile, 'DONE', (err) => {

		if (err) console.log(err);
		console.log('DONE - Browser job is ended.');

	});



	// Close the browser !!!
	await browser.close();


})();