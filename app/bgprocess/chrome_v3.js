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

if(delay > 0) console.log("Will delay for " + delay + "miliseconds for screenshot");

console.log("Width: " + viewportWidth + " Height: " + viewportHeight);
if (userAgent) console.log("User Agent: " + userAgent);


/*
// Delay Function !!! Do we need that?
function wait(ms) {
	return new Promise(r => setTimeout(r, ms)).then(() => "Yay");
}
*/



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
fs.writeFileSync(logDir+'/_css.log', '');
fs.writeFileSync(logDir+'/_font.log', '');



(async() => {


	// Launch the Chrome Browser
	const browser = await puppeteer.launch({
		//executablePath: '/Applications/Google\ Chrome\ Canary.app/Contents/MacOS/Google\ Chrome\ Canary',
		headless: true
	});



	// Open a new tab
	const page = await browser.newPage();



	// List the requests and responses
	const responses = [];
	let cssCount = 0;
	let fontCount = 0;
	await page.on('request', request => { // List the requests


/*
		// NO NEED !!!
		// Write to the requests file
		fs.appendFileSync(logDir + '/browser-requests.log', request.resourceType + ' -> ' + request.url + ' \r\n');
		console.log('Request Added: ', request.resourceType + ' -> ' + request.url);
*/



	}).on('response', resp => { // List the responses

		responses.push(resp);

	}).on('load', () => { // Download and list the files on response


/*
		// Wait before downloading !!! Do we need that?
		console.log('Waiting ' + delay + ' miliseconds to download files');
		wait(delay*10);
*/


		const totalResponse = responses.length;

		// Foreach for responses
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


			// LOGS
			console.log('****************');
			console.log('File Type: ', fileType);
			console.log('URL: ', url);
			console.log('File Name: ', fileName);
			console.log('File Extension: ', fileExtension);
			console.log('****************');


			// If on the same host
			if ( parsedRemoteUrl.hostname == parsedUrl.hostname ) {


/*
				// HTML File - DOESN'T WORK IN SOME CASES !!! (wsj.com for example)
				if (fileType == 'document' && htmlFile != 'done') {

					// Create the file
				    fs.writeFileSync(htmlFile, buffer);
				    console.log('HTML DOWNLOADED: ', fileName + " -> " + htmlFile);

				    // INDEX THE HTML ELEMENTS HERE !!!

				}
*/


				// CSS Files
				if (fileType == 'stylesheet' && CSSFilesList != 'done' ) {

					cssCount++;


					// Create the file
				    fs.writeFileSync(siteDir + '/css/' + cssCount + '.' + fileExtension, buffer);


					// Write to the downloaded CSS list file
					fs.appendFileSync(logDir+'/_css.log', cssCount+'.'+fileExtension + ' -> ' + request.url + ' \r\n');
					console.log('CSS Downloaded: ', cssCount+'.'+fileExtension + ' -> ' + request.url);

				}


				// Font Files
				if (fileType == 'font' && fontFilesList != 'done') {

					fontCount++;

					// Create the file
				    fs.writeFileSync(siteDir + '/fonts/' + fileName, buffer);


				    // Write to the downloaded fonts list file
					fs.appendFileSync(logDir+'/_font.log', fileName + ' -> ' + request.url + ' \r\n');
					console.log('Font Downloaded: ', fileName + ' -> ' + request.url);

				}


			}


			if (totalResponse == i + 1) {

				// Rename the log files
				fs.renameSync(logDir+'/_css.log', CSSFilesList);
				console.log('CSS DOWNLOADS HAVE BEEN COMPLETED!');

				fs.renameSync(logDir+'/_font.log', fontFilesList);
				console.log('FONT DOWNLOADS HAVE BEEN COMPLETED!');

			}


		}); // Responses loop


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
	const response = await page.goto(url, {
        waitUntil: 'networkidle2'
        //timeout: 3000000
    });
    const html = await response.text();


	if (htmlFile != 'done') {

		// Create the file
	    fs.writeFileSync(htmlFile, html);
	    console.log('HTML is written');

	}



	// If delay needed
	if ( delay > 0 && (pageScreenshot != "done" || projectScreenshot != "done") ) {

		await console.log('Waiting ' + delay + ' miliseconds for screenshot');
		await page.waitFor(delay);

	}



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



	// Close the browser !!! Before that, do the other device works?!!
	await browser.close();


})();