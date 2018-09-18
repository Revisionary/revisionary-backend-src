// CLI Direct Screenshot:
// chrome --headless --disable-gpu --hide-scrollbars --window-size=1440,900 --screenshot=screen.png https://www.twelve12.com/

// REQUIREMENTS
const puppeteer = require('puppeteer');
const headless = true;
//const devices = require('puppeteer/DeviceDescriptors'); // NO NEED FOR NOW !!!

const argv = require('minimist')(process.argv.slice(2));
const fs = require('fs');
const util = require('util');
const URL = require('url').URL;
const cdnDetector = require("cdn-detector");



// CLI Args
const url = argv.url || 'https://www.google.com';
const parsedRemoteUrl = new URL(url);

const viewportWidth = argv.viewportWidth || 1440;
const viewportHeight = argv.viewportHeight || 900;

const pageScreenshot = argv.pageScreenshot || 'done';
const projectScreenshot = argv.projectScreenshot || 'done';

const htmlFile = argv.htmlFile || 'done';
const CSSFilesList = argv.CSSFilesList || 'done';
const JSFilesList = argv.JSFilesList || 'done';
const fontFilesList = argv.fontFilesList || 'done';

const siteDir = argv.siteDir || 'done';
const logDir = argv.logDir;

const format = argv.format === 'png' ? 'png' : 'jpeg';
const userAgent = argv.userAgent || false;
const fullPage = argv.full || false;
const delay = argv.delay || 0;



// Blocked Requests
const blocked = require(__dirname + '/blocked.json');
const blockedRegExp = new RegExp('(' + blocked.join('|') + ')', 'i');

const truncate = (str, len) => str.length > len ? str.slice(0, len) + '…' : str;



// Console Info, browser.log
console.log('URL: ' + url);
if(fullPage) console.log("Will capture full page");

if(delay > 0) console.log("Will delay for " + delay + " miliseconds for screenshot");

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

// JS folder
if (!fs.existsSync(siteDir + '/js')){
    fs.mkdirSync(siteDir + '/js');
}

// Fonts folder
if (!fs.existsSync(siteDir + '/fonts')){
    fs.mkdirSync(siteDir + '/fonts');
}

// Create the log files
fs.writeFileSync(logDir+'/_css.log', '');
fs.writeFileSync(logDir+'/_js.log', '');
fs.writeFileSync(logDir+'/_font.log', '');



(async() => {



	// Launch the Chrome Browser
	const browser = await puppeteer.launch({
		args: ['--no-sandbox', '--disable-setuid-sandbox'], // Couses security issue !!!
		//executablePath: '/Applications/Google\ Chrome\ Canary.app/Contents/MacOS/Google\ Chrome\ Canary',
		headless: headless
	});



	// Open a new tab
	const page = await browser.newPage();



	// REQUEST
	let timeIsOver = false;
	let reqCount = 0;
	const nowTime = +new Date();
	await page.setRequestInterception(true);
	page.on('request', (request) => {

        const { url, method, resourceType } = request;

        // Skip data URIs
        if (/^data:/i.test(url)){
          request.continue();
          return;
        }

        const seconds = (+new Date() - nowTime) / 1000;
        const shortURL = truncate(url, 70);
        const otherResources = /^(manifest|other)$/i.test(resourceType);
        // Abort requests that exceeds 15 seconds
        // Also abort if more than 100 requests
        if (seconds > 15 || reqCount > 100 || timeIsOver){
          console.log(`❌⏳ ${method} ${shortURL}`);
          request.abort();
        } else if (blockedRegExp.test(url) || otherResources){
          console.log(`❌ ${method} ${shortURL}`);
          request.abort();
        } else {
          console.log(`✅ ${method} ${shortURL}`);
          request.continue();
          reqCount++;
        }

    });



	// RESPONSE
	let responseCount = 0;
	let htmlCount = 0;
	let jsCount = 0;
	let cssCount = 0;
	let fontCount = 0;

	let responseReject;
	const responsePromise = new Promise((_, reject) => {
		responseReject = reject;
	});
    page.on('response', async (resp) => {

		responseCount++;


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
		  fileName += 'index';
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


		// Prevent infinite redirects
		const location = resp.headers['location'];
        if (location && location.includes(parsedRemoteUrl.hostname)){
          responseReject(new Error('Possible infinite redirects detected.'));
        }


		// If on the same host
		if (
			(parsedRemoteUrl.hostname == parsedUrl.hostname || cdnDetector.detectFromHostname(parsedUrl.hostname) != null)
			&& !timeIsOver
		) {


			// LOGS
			console.log('****************');
			console.log('File Type: ', fileType);
			console.log('URL: ', url);
			console.log('File Name: ', fileName);
			console.log('File Extension: ', fileExtension);
			console.log('****************');


			// HTML File
			if (fileType == 'document' && htmlFile != 'done' && htmlCount == 0) {

				htmlCount++;

				// Create the file
			    fs.writeFileSync(htmlFile, buffer);
			    console.log('HTML Downloaded: ', fileName + " -> " + htmlFile);

			    // INDEX THE HTML ELEMENTS HERE !!!

			}


			// CSS Files
			if (fileType == 'stylesheet' && CSSFilesList != 'done' ) {

				cssCount++;


				// Create the file
			    fs.writeFileSync(siteDir + '/css/' + cssCount + '.' + fileExtension, buffer);


				// Write to the downloaded CSS list file
				fs.appendFileSync(logDir+'/_css.log', cssCount+'.'+fileExtension + ' -> ' + request.url + ' \r\n');
				console.log('CSS Downloaded: ', cssCount+'.'+fileExtension + ' -> ' + request.url);

			}


			// JS Files
			if (fileType == 'script' && JSFilesList != 'done' ) {

				jsCount++;


				// Create the file
			    fs.writeFileSync(siteDir + '/js/' + jsCount + '.' + fileExtension, buffer);


				// Write to the downloaded CSS list file
				fs.appendFileSync(logDir+'/_js.log', jsCount+'.'+fileExtension + ' -> ' + request.url + ' \r\n');
				console.log('JS Downloaded: ', jsCount+'.'+fileExtension + ' -> ' + request.url);

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
        waitUntil: 'networkidle0',
        timeout: 3000000
    });



/*
	// Pause all media and stop buffering - Gives an error now !!!
	page.frames().forEach((frame) => {
		frame.evaluate(() => {
			document.querySelectorAll('video, audio').forEach(m => {
				if (!m) return;
				if (m.pause) m.pause();
				m.preload = 'none';
			});
		});
	});
*/



	// COMPLETE THE JOB
	//await page.waitFor(10000);
	// Rename the log files
	fs.renameSync(logDir+'/_css.log', CSSFilesList);
	console.log('CSS DOWNLOADS HAVE BEEN COMPLETED!');

	fs.renameSync(logDir+'/_js.log', JSFilesList);
	console.log('JS DOWNLOADS HAVE BEEN COMPLETED!');

	fs.renameSync(logDir+'/_font.log', fontFilesList);
	console.log('FONT DOWNLOADS HAVE BEEN COMPLETED!');

	timeIsOver = true;



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