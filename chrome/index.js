const fs = require('fs');
const http = require('http');
const { URL } = require('url');
const urlParser = require('url');
const {
	DEBUG,
	HEADFUL,
	CHROME_BIN,
	PORT
} = process.env;

const puppeteer = require('puppeteer');
const cdnDetector = require("cdn-detector");
const jimp = require('jimp');
const pTimeout = require('p-timeout');
/*
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
*/

const blocked = require('./blocked.json');
const blockedRegExp = new RegExp('(' + blocked.join('|') + ')', 'i');

const truncate = (str, len) => str.length > len ? str.slice(0, len) + '…' : str;



let browser;

require('http').createServer(async (req, res) => {
	const { host } = req.headers;

	if (req.url == '/') { // !!!
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
			//pages: cache.keys(),
			process: {
				versions: process.versions,
				memoryUsage: process.memoryUsage(),
			},
		}, null, '\t'));
		return;
	}

	const queryData = urlParser.parse(req.url, true).query;
	const action = queryData.action || '';
	const url = queryData.url || '';


	// URL CHECKS
	if (!url) {
		res.writeHead(400, {
			'content-type': 'text/plain',
		});
		res.end('Something is wrong. Missing URL.');
		return;
	}

	if (!/^https?:\/\//i.test(url)) {
		res.writeHead(400, {
			'content-type': 'text/plain',
		});
		res.end('Invalid URL.');
		return;
	}


/*
	// TOO MUCH CACHE
	if (cache.itemCount > 20) {
		res.writeHead(420, {
			'content-type': 'text/plain',
		});
		res.end(`There are ${cache.itemCount} pages in the current instance now. Please try again in few minutes.`);
		return;
	}
*/


	let page, pageURL;
	try {


		console.log('🌎 URL: ', url, ' 💥 Action: ', action);


		const parsedRemoteUrl = new URL(url);
		const { origin,	hostname, pathname, searchParams } = new URL(url);
		const path = decodeURIComponent(pathname);
		const output = queryData.output;

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

		pageURL = origin + path; console.log('🌎 pageURL: ', pageURL);
		let realPageURL = pageURL;
		let actionDone = false;
		const width = parseInt(queryData.width, 10) || 1024;
		const height = parseInt(queryData.height, 10) || 768;
		const fullPage = queryData.fullPage == 'true' || false;
		const siteDir = queryData.sitedir || 'site/x/y/z/';
		const logDir = siteDir+'logs/';


		// Create the log folder if not exist
		if (!fs.existsSync(logDir)) {
			fs.mkdirSync(logDir);
			try{ fs.chownSync(logDir, 33, 33); } catch(e) {}
		}

		// Create the log file
		fs.writeFileSync(logDir+'browser.log', 'Started');
		try{ fs.chownSync(logDir+'browser.log', 33, 33); } catch(e) {}




		let downloadableRequests = [];


		// If the page is already open
		//page = cache.get(pageURL); if (page) console.log('Page found from cache.');

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


				// Update the real page URL
				if (page.url() != 'about:blank' && realPageURL != page.url()) {
					realPageURL = page.url();
					console.log('🌎 Real Page URL: ', page.url());
				}
				const parsedRealURL = new URL(realPageURL);
				const ourHost = parsedRealURL.hostname;


				const url = request.url();
				const parsedUrl = new URL(url);
				const requestHost = parsedUrl.hostname;
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
						ourHost == requestHost ||
						cdnDetector.detectFromHostname(requestHost) != null
					) {

						let shouldDownload = true;
						let newFileName = "noname.txt";
						let newDir = "temp/";


						// HTML File
						if (resourceType == 'document' && htmlCount == 0) {

							htmlCount++;
							newDir = "";
							newFileName = 'index.html';

						}

						// CSS Files
						else if (fileType == 'stylesheet') {

							cssCount++;
							newDir = "css/";
							newFileName = cssCount + '.' + fileExtension;

						}

						// JS Files
						else if (fileType == 'script') {

							jsCount++;
							newDir = "js/";
							newFileName = jsCount + '.' + fileExtension;

						}

						// Font Files
						else if (fileType == 'font') {

							fontCount++;
							newDir = "fonts/";
							newFileName = fileName;

						}

						// If none of them
						else {

							shouldDownload = false;
							console.log(`📄❌ NOT ALLOWED TYPE: ${fileType} ${fileName} ${shortURL}`);

						}



						// Add to the list
						if (shouldDownload) {

							// Prepend the site directory
							newDir = siteDir + newDir;

							downloadableRequests[downloadableRequests.length] = {
								remoteUrl: url,
								fileType: fileType,
								fileName: fileName,
								newDir: newDir,
								newFileName: newFileName,
								buffer: null
							};
							console.log('📄📋 #'+downloadableRequests.length+' '+fileType.toUpperCase()+' to Download: ', fileName + ' -> ' + siteDir + newDir + newFileName);

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
						console.log(`📋✅ #${downloadedIndex} (${bufferCount}/${downloadableRequests.length}) ${method} ${resourceType} ${url} -> ` + downloadableRequests[downloadedIndex].newUrl);

					}, e => {
						console.error(`📋❌${response.status()} ${response.url()} failed: ${e}`);
					});


				}



			}); // THE RESPONSES LOOP



			// Set the viewport
			console.log('🖥 Setting viewport sizes as ' + width + 'x' + height);
			await page.setViewport({
				width,
				height,
			});



			// Go to the page
			console.log('⬇️ Fetching ' + pageURL);
			await Promise.race([
				responsePromise,
				page.goto(pageURL, {
					waitUntil: 'networkidle2',
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


			//downloadableRequests = cache.get(pageURL + 'downloadableRequests');


			// Set the viewport
			console.log('🖥 !!! Setting viewport sizes: ' + width + 'x' + height);
			await page.setViewport({
				width,
				height,
			});


		}









		console.log('💥 Perform action: ' + action);

		switch (action) {
			case 'internalize': {

				//console.log('🌎 Real Page URL: ', realPageURL);


				// Create the site folder if not exist
				if (!fs.existsSync(siteDir)) {
					fs.mkdirSync(siteDir);
					try{ fs.chownSync(siteDir, 33, 33); } catch(e) {}
				}


				let downloadableTotal = downloadableRequests.length;
				let downloadedFiles = [];


				// DOWNLOAD
				downloadableRequests.forEach(function(downloadable, i) {


					try {

						// Create the folder if not exist
						if (!fs.existsSync(downloadable.newDir)) {
							fs.mkdirSync(downloadable.newDir);
							try{ fs.chownSync(downloadable.newDir, 33, 33); } catch(e) {}
						}

						// Write to the file
						fs.writeFileSync(downloadable.newDir + downloadable.newFileName, downloadable.buffer);
						try{ fs.chownSync(downloadable.newDir + downloadable.newFileName, 33, 33); } catch(e) {}


						// Add to the list
						downloadedFiles[i] = {
							remoteUrl: downloadable.remoteUrl,
							fileType: downloadable.fileType,
							fileName: downloadable.fileName,
							newDir: downloadable.newDir,
							newFileName: downloadable.newFileName,
						};


						const downloadedCount = downloadedFiles.length;
						const downloadableTotal = downloadableRequests.length;
						const downloadedIndex = i + 1;

						console.log(`⏬✅ (${downloadedIndex}/${downloadableTotal}) ${downloadable.fileType} ${downloadable.remoteUrl} -> ` + downloadable.newDir + downloadable.newFileName);

					} catch (err) {

						console.error(`⏬❌ ${downloadable.fileType} ${downloadable.remoteUrl} -> ` + downloadable.newDir + downloadable.newFileName + ' ERROR: ' + err);

					}


				});


				// SCREENSHOTS
				try {

					const screenshot = await pTimeout(page.screenshot({
						type: 'jpeg'
					}), 20 * 1000, 'Screenshot timed out');

					// Page Screenshot Saving
					const pageScreenshotDir = siteDir + "../";
					const pageScreenshot = pageScreenshotDir + 'page.jpg';
					if (!fs.existsSync(pageScreenshotDir)) fs.mkdirSync(pageScreenshotDir);
					fs.writeFileSync(pageScreenshot, screenshot);
					console.log('📸 Page Screenshot Saved: ', pageScreenshot);

					// Project Screenshot Saving if not exists
					const projectScreenshotDir = pageScreenshotDir + "../../";
					const projectScreenshot = projectScreenshotDir + 'project.jpg';
					if (!fs.existsSync(projectScreenshotDir)) fs.mkdirSync(projectScreenshotDir);
					if (!fs.existsSync(projectScreenshot)) {

						fs.writeFileSync(projectScreenshot, screenshot);
						console.log('📸 Project Screenshot Saved: ', projectScreenshot);

					}


				} catch (err) {

					console.log('📷❌ Screenshots could not be saved: ', err);

				}


				const dataString = JSON.stringify({
					status: (downloadedFiles.length ? 'success' : 'error'),
					realPageURL : realPageURL,
					downloadedFiles: downloadedFiles
				}, null, '\t');


				// Write to the log file
				fs.writeFileSync(logDir+'browser.log', dataString);


				// JSON OUTPUT
				res.writeHead(200, {
					'content-type': 'application/json',
				});
				res.end(dataString);


				break;
			}
			case 'render': {
				const raw = queryData.raw || false;

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
				const format = queryData.format || null;
				const pageRanges = queryData.pageRanges || null;

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
				const thumbWidth = parseInt(queryData.thumbWidth, 10) || null;
				const clipSelector = queryData.clipSelector;

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

		actionDone = true;
		console.log('💥 Done action: ' + action);




		// Close the page
		try {

			if (page && page.close) {
				console.log('🗑 Disposing ' + url);
				page.removeAllListeners();
				await page.close();
			}

			if (browser) {
				console.log('🔌 Close the browser for ' + url);
				browser.close();
				browser = null;
			}

		} catch (e) {

			console.log('🐞 Closing error: ' + e);

		}








/*
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
*/



	} catch (e) {
		if (!DEBUG && page) {
			console.error(e);
			console.log('💔 Force close ' + pageURL);
			page.removeAllListeners();
			page.close();
		}
		//cache.del(pageURL);
		//cache.del(pageURL + 'downloadableRequests');
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