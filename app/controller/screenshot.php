<?php
use Cocur\BackgroundProcess\BackgroundProcess;



$chrome_dir = '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome';


// Initiate Internalizator
$process = new BackgroundProcess("$chrome_dir --headless --hide-scrollbars --disable-gpu --screenshot=".dir."/app/screen.png --window-size=1440,900 https://www.twelve12.com/");
$process->run(Page::ID(27)->logDir."/screenshot.log", true);