const fs = require('fs');
const http = require('http');
const { URL } = require('url');
let {
	DEBUG,
	HEADFUL,
	CHROME_BIN,
	PORT
} = process.env;

//HEADFUL = true;

const puppeteer = require('puppeteer');
const cdnDetector = require("cdn-detector");
const jimp = require('jimp');
const pTimeout = require('p-timeout');
const LRU = require('lru-cache');
const cache = LRU({
	max: process.env.CACHE_SIZE || Infinity,
	maxAge: 1000 * 60, // 1 minute
	noDisposeOnSet: true,
	dispose: async (url, page) => {
		try {
			if (page && page.close) {
				console.log('🗑 Disposing ' + url);
				page.removeAllListeners();
				await page.deleteCookie(await page.cookies());
				await page.close();
			}
		} catch (e) {}
	}
});
setInterval(() => cache.prune(), 1000 * 60); // Prune every minute

const blocked = require('./blocked.json');
const blockedRegExp = new RegExp('(' + blocked.join('|') + ')', 'i');

const truncate = (str, len) => str.length > len ? str.slice(0, len) + '…' : str;









// REVISIONARY - CLI Args
/*
const pageScreenshot = argv.pageScreenshot || 'done';
const projectScreenshot = argv.projectScreenshot || 'done';

const htmlFile = argv.htmlFile || 'done';
const CSSFilesList = argv.CSSFilesList || 'done';
const JSFilesList = argv.JSFilesList || 'done';
const fontFilesList = argv.fontFilesList || 'done';

const siteDir = argv.siteDir || 'done';
const logDir = argv.logDir;

const format = argv.format === 'png' ? 'png' : 'jpeg';
const fullPage = argv.full || false;
const delay = argv.delay || 0;
*/









let browser;

require('http').createServer(async (req, res) => {
	const { host } = req.headers;

	if (req.url == '/') {
		res.writeHead(200, {
			'content-type': 'text/html; charset=utf-8',
			'cache-control': 'public,max-age=31536000',
		});
		res.end(fs.readFileSync('index.html'));
		return;
	}

	if (req.url == '/favicon.ico') {
		res.writeHead(204);
		res.end();
		return;
	}

	if (req.url == '/status') {
		res.writeHead(200, {
			'content-type': 'application/json',
		});
		res.end(JSON.stringify({
			pages: cache.keys(),
			process: {
				versions: process.versions,
				memoryUsage: process.memoryUsage(),
			},
		}, null, '\t'));
		return;
	}

	const [_, action, url] = req.url.match(/^\/(screenshot|render|pdf|internalize)?\/?(.*)/i) || ['', '', ''];

	if (!url) {
		res.writeHead(400, {
			'content-type': 'text/plain',
		});
		res.end('Something is wrong. Missing URL.');
		return;
	}
	const remoteUrl = url;
	const parsedRemoteUrl = new URL(remoteUrl);

	if (cache.itemCount > 20) {
		res.writeHead(420, {
			'content-type': 'text/plain',
		});
		res.end(`There are ${cache.itemCount} pages in the current instance now. Please try again in few minutes.`);
		return;
	}

	let page, pageURL;
	try {
		if (!/^https?:\/\//i.test(url)) {
			throw new Error('Invalid URL');
		}

		const { origin,	hostname, pathname, searchParams } = new URL(url);
		const path = decodeURIComponent(pathname);
		const output = searchParams.get('output');

		await new Promise((resolve, reject) => {
			const req = http.request({
				method: 'HEAD',
				host: hostname,
				path,
			}, ({
				statusCode,
				headers
			}) => {
				if (!headers || (statusCode == 200 && !/text\/html/i.test(headers['content-type']))) {
					reject(new Error('Not a HTML page'));
				} else {
					resolve();
				}
			});
			req.on('error', reject);
			req.end();
		});

		pageURL = origin + path;
		let actionDone = false;
		const width = parseInt(searchParams.get('width'), 10) || 1024;
		const height = parseInt(searchParams.get('height'), 10) || 768;

		let downloadableRequests = [];


		// If the page is already open
		page = cache.get(pageURL);

		// If page is not already open
		if (!page) {


			// Launch the browser if browser is not already open
			if (!browser) {
				console.log('🚀 Launch browser!');
				const config = {
					ignoreHTTPSErrors: true,
					args: [
						'--no-sandbox',
						'--disable-setuid-sandbox',
						'--disable-dev-shm-usage',
						'--enable-features=NetworkService',
						'-—disable-dev-tools',
					],
					devtools: false,
				};
				if (DEBUG) config.dumpio = true;
				if (HEADFUL) {
					config.headless = false;
					config.args.push('--auto-open-devtools-for-tabs');
				}
				if (CHROME_BIN) config.executablePath = CHROME_BIN;
				browser = await puppeteer.launch(config);
			}



			// Open a new tab
			page = await browser.newPage();



			// REQUEST
			let htmlCount = 0;
			let jsCount = 0;
			let cssCount = 0;
			let fontCount = 0;

			const nowTime = +new Date();
			let reqCount = 0;
			await page.setRequestInterception(true);
			page.on('request', (request) => {

				const url = request.url();
				const parsedUrl = new URL(url);
				const shortURL = truncate(url, 70);
				const method = request.method();
				const resourceType = request.resourceType();

				// Skip data URIs
				if (/^data:/i.test(url)) {
					request.continue();
					return;
				}

				// Get the filename
				const split = parsedUrl.pathname.split('/');
				let fileName = split[split.length - 1];

				if (fileName == '') fileName += 'index';

				if (!fileName.includes('.')) {

				    if (resourceType == 'document') fileName += '.html';
				    else if (resourceType == 'stylesheet') fileName += '.css';
				    else if (resourceType == 'script') fileName += '.js';

				}

				// Get the file extension
				const extsplit = fileName.split('.');
				const fileExtension = extsplit[extsplit.length - 1];
				const fileType = resourceType;



				const seconds = (+new Date() - nowTime) / 1000;
				const otherResources = /^(manifest|other)$/i.test(resourceType);
				// Abort requests that exceeds 15 seconds
				// Also abort if more than 100 requests
				if (seconds > 15) {
					console.log(`❌⏳ ${method} ${resourceType} ${shortURL}`);
					request.abort();
				} else if (reqCount > 100) {
					console.log(`❌📈 ${method} ${resourceType} ${shortURL}`);
					request.abort();
				} else if (actionDone) {
					console.log(`❌🔚 ${method} ${resourceType} ${shortURL}`);
					request.abort();
				} else if (blockedRegExp.test(url)) {
					console.log(`❌🚫 ${method} ${resourceType} ${shortURL}`);
					request.abort();
				} else if (otherResources) {
					console.log(`❌♨️ ${method} ${resourceType} ${shortURL}`);
					request.abort();
				} else {
					console.log(`✅ ${method} ${resourceType} ${fileName} ${shortURL}`);
					request.continue();
					reqCount++;



					// If on the same host, or provided by a CDN
					if (
						parsedRemoteUrl.hostname == parsedUrl.hostname ||
						cdnDetector.detectFromHostname(parsedUrl.hostname) != null
					) {

						let shouldDownload = true;
						let newFileName = "noname.txt";
						let newDir = "site/temp/";


						// HTML File
						if (resourceType == 'document' && htmlCount == 0) {

							htmlCount++;
							newDir = "site/";
							newFileName = 'index.html';

						}

						// CSS Files
						else if (fileType == 'stylesheet') {

							cssCount++;
							newDir = "site/css/";
							newFileName = cssCount + '.' + fileExtension;

						}

						// JS Files
						else if (fileType == 'script') {

							jsCount++;
							newDir = "site/js/";
							newFileName = jsCount + '.' + fileExtension;

						}

						// Font Files
						else if (fileType == 'font') {

							fontCount++;
							newDir = "site/font/";
							newFileName = fileName;

						}

						// If none of them
						else {

							shouldDownload = false;
							console.log(`📄❌ NOT ALLOWED TYPE: ${fileType} ${fileName} ${shortURL}`);

						}



						// Add to the list
						if (shouldDownload) {

							downloadableRequests[downloadableRequests.length] = {
								remoteUrl: url,
								fileType: fileType,
								fileName: fileName,
								newDir: newDir,
								newFileName: newFileName,
								newUrl: newDir + newFileName,
								buffer: null
							};
							console.log('📄📋 #'+downloadableRequests.length+' '+fileType.toUpperCase()+' to Download: ', fileName + ' -> ' + newFileName);

						}


					// If not on our host !!!
					} else console.log(`📄❌ OTHER HOST FILE ${fileType} ${shortURL}`);


				} // If request allowed


			}); // THE REQUESTS LOOP



			// RESPONSE
			let responseCount = 0;
			let bufferCount = 0;

			let responseReject;
			const responsePromise = new Promise((_, reject) => {
				responseReject = reject;
			});
			page.on('response', (response) => {

				const headers = response._headers;
				const location = headers['location'];
				if (location && location.includes(host)) {
					responseReject(new Error('Possible infinite redirects detected.'));
				}

				responseCount++;


				// Request info
				const request = response.request();
				const url = request.url();
				const parsedUrl = new URL(url);
				const shortURL = truncate(url, 70);
				const method = request.method();
				const resourceType = request.resourceType();


				var downloadable = downloadableRequests.find(function(req) { return req.remoteUrl == url ? true : false; });

				if ( downloadable && !url.startsWith('data:') && response.ok ) {

					var downloadedIndex = downloadableRequests.indexOf(downloadable);


					// Get the buffer
					response.buffer().then(buffer => { bufferCount++;


						// Add the buffer
						downloadableRequests[downloadedIndex].buffer = buffer;


						//console.log(`${b} ${response.status()} ${response.url()} ${b.length} bytes`);
						console.log(`📋✅ #${downloadedIndex} (${bufferCount}/${downloadableRequests.length}) ${method} ${resourceType} ${url}`);

					}, e => {
						console.error(`📋❌${response.status()} ${response.url()} failed: ${e}`);
					});


				}



			});



			// Set the viewport
			await page.setViewport({
				width,
				height,
			});



			// Go to the page
			console.log('⬇️ Fetching ' + pageURL);
			await Promise.race([
				responsePromise,
				page.goto(pageURL, {
					waitUntil: 'networkidle0',
				})
			]);



			// Pause all media and stop buffering
			page.frames().forEach((frame) => {
				frame.evaluate(() => {
					document.querySelectorAll('video, audio').forEach(m => {
						if (!m) return;
						if (m.pause) m.pause();
						m.preload = 'none';
					});
				});
			});



		} else {


			downloadableRequests = cache.get(pageURL + 'downloadableRequests');


			// Set the viewport
			await page.setViewport({
				width,
				height,
			});


		}









		console.log('💥 Perform action: ' + action);

		switch (action) {
			case 'internalize': {

				let downloadableTotal = downloadableRequests.length;
				let downloadedFiles = [];

				// Check all the files
				downloadableRequests.forEach(function(downloadable, downloadedIndex) { downloadedIndex++;

					// Create the folder if not exist
					if (!fs.existsSync(downloadable.newDir)) fs.mkdirSync(downloadable.newDir);

					// Write to the file
					fs.writeFileSync(downloadable.newUrl, downloadable.buffer);



					// Add to the list
					downloadedFiles[downloadedIndex] = {
						remoteUrl: downloadable.remoteUrl,
						fileType: downloadable.fileType,
						fileName: downloadable.fileName,
						newDir: downloadable.newDir,
						newFileName: downloadable.newFileName,
						newUrl: downloadable.newUrl
					};



					const downloadedCount = downloadedFiles.length;
					let downloadableTotal = downloadableRequests.length;

					console.log(`⏬✅ (${downloadedIndex}/${downloadableTotal}) ${downloadable.fileType} ${downloadable.remoteUrl}`);

				});



				// JSON OUTPUT
				res.writeHead(200, {
					'content-type': 'application/json',
				});
				res.end(JSON.stringify({
					status: (downloadedFiles.length ? 'success' : 'error'),
					downloadedFiles: downloadedFiles
				}, null, '\t'));


				break;
			}
			case 'render': {
				const raw = searchParams.get('raw') || false;

				const content = await pTimeout(raw ? page.content() : page.evaluate(() => {
					let content = '';
					if (document.doctype) {
						content = new XMLSerializer().serializeToString(document.doctype);
					}

					const doc = document.documentElement.cloneNode(true);

					// Remove scripts except JSON-LD
					const scripts = doc.querySelectorAll('script:not([type="application/ld+json"])');
					scripts.forEach(s => s.parentNode.removeChild(s));

					// Remove import tags
					const imports = doc.querySelectorAll('link[rel=import]');
					imports.forEach(i => i.parentNode.removeChild(i));

					const { origin,	pathname } = location;
					// Inject <base> for loading relative resources
					if (!doc.querySelector('base')) {
						const base = document.createElement('base');
						base.href = origin + pathname;
						doc.querySelector('head').appendChild(base);
					}

					// Try to fix absolute paths
					const absEls = doc.querySelectorAll('link[href^="/"], script[src^="/"], img[src^="/"]');
					absEls.forEach(el => {
						const href = el.getAttribute('href');
						const src = el.getAttribute('src');
						if (src && /^\/[^/]/i.test(src)) {
							el.src = origin + src;
						} else if (href && /^\/[^/]/i.test(href)) {
							el.href = origin + href;
						}
					});

					content += doc.outerHTML;

					// Remove comments
					content = content.replace(/<!--[\s\S]*?-->/g, '');

					return content;
				}), 10 * 1000, 'Render timed out');

				res.writeHead(200, {
					'content-type': 'text/html; charset=UTF-8',
					'cache-control': 'public,max-age=31536000',
				});
				res.end(content);
				break;
			}
			case 'pdf': {
				const format = searchParams.get('format') || null;
				const pageRanges = searchParams.get('pageRanges') || null;

				const pdf = await pTimeout(page.pdf({
					format,
					pageRanges,
				}), 10 * 1000, 'PDF timed out');

				res.writeHead(200, {
					'content-type': 'application/pdf',
					'cache-control': 'public,max-age=31536000',
				});
				res.end(pdf, 'binary');
				break;
			}
			default: {



				const thumbWidth = parseInt(searchParams.get('thumbWidth'), 10) || null;
				const fullPage = searchParams.get('fullPage') == 'true' || false;
				const clipSelector = searchParams.get('clipSelector');


				let screenshot;
				if (clipSelector) {
					const handle = await page.$(clipSelector);
					if (handle) {
						screenshot = await pTimeout(handle.screenshot({
							type: 'jpeg',
						}), 20 * 1000, 'Screenshot timed out');
					}
				} else {
					screenshot = await pTimeout(page.screenshot({
						type: 'jpeg',
						fullPage,
					}), 20 * 1000, 'Screenshot timed out');
				}




				// Page Screenshot Saving
				let pageCaptured = false;
				const pageScreenshotDir = searchParams.get('pageScreenshotDir');
				if (pageScreenshotDir) {
					if (!fs.existsSync(pageScreenshotDir)) fs.mkdirSync(pageScreenshotDir);
					pageCaptured = fs.writeFileSync(pageScreenshotDir + 'page.jpg', screenshot);
				}

				// Project Screenshot
				let projectCaptured = false;
				const projectScreenshotDir = searchParams.get('projectScreenshotDir');
				if (projectScreenshotDir) {
					if (!fs.existsSync(projectScreenshotDir)) fs.mkdirSync(projectScreenshotDir);
					projectCaptured = fs.writeFileSync(projectScreenshotDir + 'project.jpg', screenshot);
				}


				// JSON OUTPUT
				if (output == "JSON") {


					res.writeHead(200, {
						'content-type': 'application/json',
					});
					res.end(JSON.stringify({
						status: (pageCaptured || projectCaptured ? 'success' : 'error'),
						screenshots: {
							page: (pageCaptured ? pageScreenshotDir + 'page.jpg' : 'not captured'),
							project: (projectCaptured ? projectScreenshotDir + 'project.jpg' : 'not captured')
						}
					}, null, '\t'));



				// PRINT TO THE PAGE
				} else {


					res.writeHead(200, {
						'content-type': 'image/jpeg',
						'cache-control': 'public,max-age=31536000',
					});

					if (thumbWidth && thumbWidth < width) {

						const image = await jimp.read(screenshot);
						image.resize(thumbWidth, jimp.AUTO).quality(90).getBuffer(jimp.MIME_JPEG, (err, buffer) => {
							res.end(buffer, 'binary');
						});

					} else {

						res.end(screenshot, 'binary');

					}


				}




			}
		}

		actionDone = true;
		console.log('💥 Done action: ' + action);









		if (!cache.has(pageURL)) {
			cache.set(pageURL, page);

			// Try to stop all execution
			page.frames().forEach((frame) => {
				frame.evaluate(() => {
					// Clear all timer intervals https://stackoverflow.com/a/6843415/20838
					for (var i = 1; i < 99999; i++) window.clearInterval(i);
					// Disable all XHR requests
					XMLHttpRequest.prototype.send = _ => _;
					// Disable all RAFs
					requestAnimationFrame = _ => _;
				});
			});
		}

		if (!cache.has(pageURL + 'downloadableRequests')) {
			cache.set(pageURL + 'downloadableRequests', downloadableRequests);
		}



	} catch (e) {
		if (!DEBUG && page) {
			console.error(e);
			console.log('💔 Force close ' + pageURL);
			page.removeAllListeners();
			page.close();
		}
		cache.del(pageURL);
		cache.del(pageURL + 'downloadableRequests');
		const { message = '' } = e;
		res.writeHead(400, {
			'content-type': 'text/plain',
		});
		res.end('Oops. Something is wrong.\n\n' + message);

		// Handle websocket not opened error
		if (/not opened/i.test(message) && browser) {
			console.error('🕸 Web socket failed');
			try {
				browser.close();
				browser = null;
			} catch (err) {
				console.warn(`Chrome could not be killed ${err.message}`);
				browser = null;
			}
		}
	}
}).listen(PORT || 3000);

process.on('SIGINT', () => {
	if (browser) browser.close();
	process.exit();
});

process.on('unhandledRejection', (reason, p) => {
	console.log('Unhandled Rejection at:', p, 'reason:', reason);
});