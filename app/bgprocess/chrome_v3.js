// CLI Direct Screenshot:
// chrome --headless --disable-gpu --hide-scrollbars --window-size=1440,900 --screenshot=screen.png https://www.twelve12.com/


const puppeteer = require('puppeteer');
//const devices = require('puppeteer/DeviceDescriptors'); // NO NEED FOR NOW !!!

const argv = require('minimist')(process.argv.slice(2));
const fs = require('fs');
const util = require('util');
const URL = require('url').URL;


// CLI Args
const url = argv.url || 'https://www.google.com';
const parsedRemoteUrl = new URL(url);

const viewportWidth = argv.viewportWidth || 1440;
const viewportHeight = argv.viewportHeight || 900;


const pageScreenshot = argv.pageScreenshot || 'done';
const projectScreenshot = argv.projectScreenshot || 'done';

const htmlFile = argv.htmlFile || 'done';
const CSSFilesList = argv.CSSFilesList || 'done';
const fontFilesList = argv.fontFilesList || 'done';

const siteDir = argv.siteDir || 'done';
const logDir = argv.logDir;


const format = argv.format === 'png' ? 'png' : 'jpeg';
const userAgent = argv.userAgent || false;
const fullPage = argv.full || false;
const delay = argv.delay || 0;


// Console Info, browser.log
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
		headless: false
	});



	// Open a new tab
	const page = await browser.newPage();



	// List the requests and responses
	const responses = [];
	page.on('request', request => {


		// Write to the requests file
		fs.appendFile(logDir + '/requests.log', request.resourceType + ' -> ' + request.url + ' \r\n', (err) => {

			if (err) console.log(err);
			console.log('Request Added: ', request.resourceType + ' -> ' + request.url);

		});



	}).on('response', resp => {

	  responses.push(resp);

	});




	// Download the necessary responses
	let cssCount = 0;
	let fontCount = 0;
	let jsCount = 0;
	page.on('load', () => {


		// Create the necessary files and folders

		// Logs folder
		if (!fs.existsSync(logDir)){
		    fs.mkdirSync(logDir);
		}

		// CSS folder
		if (!fs.existsSync(siteDir + '/css')){
		    fs.mkdirSync(siteDir + '/css');
		}

		// Fonts folder
		if (!fs.existsSync(siteDir + '/fonts')){
		    fs.mkdirSync(siteDir + '/fonts');
		}

		// Create the log files
		fs.closeSync(fs.openSync(logDir+'/_css.log', 'w'));
		fs.closeSync(fs.openSync(logDir+'/_font.log', 'w'));


		responses.map(async (resp, i) => {

			// The request info
			const request = await resp.request();

			// Get the URL
			const url = request.url;
			const parsedUrl = new URL(url);

			// Get the file type
			const fileType = request.resourceType;

			// Get the file content
			const buffer = await resp.buffer();

			// Get the filename
			const split = parsedUrl.pathname.split('/');
			let fileName = split[split.length - 1];

			if (fileName == '') {
			  fileName += 'file';
			}

			if (!fileName.includes('.')) {

			    if (fileType == 'document') {

					fileName += '.html';

			    } else if (fileType == 'stylesheet') {

					fileName += '.css';

			    } else if (fileType == 'script') {

					fileName += '.js';

			    }

			}

			// Get the file extension
			const extsplit = fileName.split('.');
			const fileExtension = extsplit[extsplit.length - 1];


			// If on the same host
			if ( parsedRemoteUrl.hostname == parsedUrl.hostname ) {


				// HTML File
				if (fileType == 'document' && htmlFile != 'done') {

					// Create the file
				    fs.writeFileSync(htmlFile, buffer);
				    console.log('HTML DOWNLOADED: ', fileName + " -> " + htmlFile);

				}


				// CSS Files
				if (fileType == 'stylesheet' && CSSFilesList != 'done' ) {

					cssCount++;


					// Create the file
				    fs.writeFileSync(siteDir + '/css/' + cssCount + '.' + fileExtension, buffer);


					// Write to the downloaded CSS list file
					fs.appendFile(logDir+'/_css.log', cssCount+'.'+fileExtension + ' -> ' + request.url + ' \r\n', (err) => {

						if (err) console.log(err);
						console.log('CSS Downloaded: ', cssCount+'.'+fileExtension + ' -> ' + request.url);

					});

				}


				// Font Files
				if (fileType == 'font' && fontFilesList != 'done') {

					fontCount++;

					// Create the file
				    fs.writeFileSync(siteDir + '/fonts/' + fontCount+'.'+fileExtension, buffer);


				    // Write to the downloaded fonts list file
					fs.appendFile(logDir+'/_font.log', fontCount+'.'+fileExtension + ' -> ' + request.url + ' \r\n', (err) => {

						if (err) console.log(err);
						console.log('Font Downloaded: ', fontCount+'.'+fileExtension + ' -> ' + request.url);

					});

				}


/*
				// JS Files - NO NEED YET
				if (fileType == 'script') {

					jsCount++;

					// Create the file
				    fs.writeFileSync(jsCount + '.' + fileExtension, buffer);
				    console.log('JS DOWNLOADED: ', fileName + " is written as " + jsCount + '.' + fileExtension);

				}
*/


			}



			console.log('****************');
			console.log('File Type: ', fileType);
			console.log('URL: ', url);
			console.log('File Name: ', fileName);
			console.log('File Extension: ', fileExtension);
			console.log('****************');

		});


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
	await page.goto(url, {waitUntil: 'networkidle'});



	// If delay needed
	if (delay > 0) console.log('Waiting ' + delay + ' miliseconds');
	await wait(delay);



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



	// Rename the log files
	fs.rename(logDir+'/_css.log', CSSFilesList, (err) => {

		if (err) console.log(err);
		console.log('CSS DOWNLOADS HAVE BEEN COMPLETED!');

	});

	fs.rename(logDir+'/_font.log', fontFilesList, (err) => {

		if (err) console.log(err);
		console.log('FONT DOWNLOADS HAVE BEEN COMPLETED!');

	});



	// Close the browser !!! Before that, do the other device works?!!
	await browser.close();


})();