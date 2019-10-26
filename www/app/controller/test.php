<pre><?php


// if(extension_loaded('gd')) {
//     print_r(gd_info());
// }
// else {
//     echo 'GD is not available.';
// }

// if(extension_loaded('imagick')) {
//     $imagick = new Imagick();
//     print_r($imagick->queryFormats());
// }
// else {
//     echo 'ImageMagick is not available.';
// }

// exit;


// // Resizer
// $img = resize_image(cache."/bill.png", 1920, 1920);
// die($img);


//die_to_print( getimagesize(cache."/bill.png") );


if( isset($_FILES['image']) && $User->getInfo('user_level_ID') === 1 ) {

	// print_r( $_FILES );
	// exit;

	$temp_file_location = $_FILES['image']['tmp_name'];	
	$file_name = str_replace(' ', '-', basename($_FILES['image']['name'])); // Update the name

	//die_to_print([$file_name, $temp_file_location, basename($temp_file_location)]);
	//die_to_print( dirname(cache."/$file_name") );

	resize_image("$temp_file_location", 250, 250);

	//die_to_print($img);

	$file = new File($temp_file_location);
	$result = $file->upload(
		cache."/$file_name", "local",
		//"avatars/$file_name", "s3",
		true
	);

	//echo parse_url($result, PHP_URL_PATH)."<br>";

	var_dump($result);

}

?></pre>
<form action="" method="POST" enctype="multipart/form-data">
	<input type="file" name="image" data-max-size="3145728">
	<button>Send</button>
</form>

<pre>
<?php



$client = $s3;


// List all files and folders
$objects = $s3->ListObjects();
die_to_print( $objects );


// Listing all Spaces in the region
$spaces = $s3->ListSpaces();
//die_to_print( $spaces );


// foreach ($spaces['Buckets'] as $space){
//     echo $space['Name']."\n";
// }



// Delete a file/folder.
//$result = $s3->DeleteObject("test");
//die_to_print( $result );


// Delete a file/folder recursive
// $s3->DeleteObject("asd/", true);


//var_dump($result);
exit;




$flush = true;
if ($flush) $cache->flush();
echo "
<details>
	<summary><h2 style='display: inline;'>CACHES (".count($cache->getAllKeys()).")</h2></summary>";
echo "<p style='padding-left: 20px;'>Array <br>(<br>";
$count = 0;
foreach ($cache->getAllKeys() as $key) {

	echo "    [$key] => ";
	echo !is_array($cache->get($key)) ? print_r( $cache->get($key), true) : "Array(...)";
	echo "<br>";

	$count++;
}
echo ")";
echo "</p>
</details>";


// If logged in
$user = User::ID();
if ($user) {


	// USER
	echo "
	<details>
		<summary><h2 style='display: inline;'>USER</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getInfo(), true )."</p>
	</details>";


	// NOTIFICATIONS
	$notifications = $user->getNotifications();
	echo "
	<details>
		<summary><h2 style='display: inline;'>NOTIFICATIONS (".$notifications['totalCount'].")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $notifications['notifications'], true )."</p>
	</details>";


	// PROJECT CATEGORIES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PROJECT CATEGORIES (".count($user->getProjectCategories()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getProjectCategories(), true )."</p>
	</details>";


	// PROJECTS
	echo "
	<details>
		<summary><h2 style='display: inline;'>PROJECTS (".count($user->getProjects()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getProjects(), true )."</p>
	</details>";


	// PAGE CATEGORIES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PAGE CATEGORIES (".count($user->getPageCategories()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPageCategories(), true )."</p>
	</details>";


	// PAGES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PAGES (".count($user->getPages()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPages(), true )."</p>
	</details>";


	// PHASES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PHASES (".count($user->getPhases()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPhases(), true )."</p>
	</details>";


	// DEVICES
	echo "
	<details>
		<summary><h2 style='display: inline;'>DEVICES (".count($user->getDevices()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getDevices(), true )."</p>
	</details>";


	// PINS
	echo "
	<details>
		<summary><h2 style='display: inline;'>PINS (".count($user->getPins()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPins(), true )."</p>
	</details>";


	// SCREENS
	echo "
	<details>
		<summary><h2 style='display: inline;'>SCREENS (".count($user->getScreens()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getScreens(), true )."</p>
	</details>";


}
echo "<br><br>";
if ($flush) $cache->flush();



//$cache->set("foo", 2);
//$cache->increment("foo", 2);
// $cache->set('foo', 'fasdsasd');


// var_dump(

// 	$cache->get('foo')

// );

//die();



// echo get_redirect_final_target('http://bilaltas.net');
// die();

// use Aws\S3\S3Client;



/*
$full_url = 'http://user:pass@www.pref.okinawa.jp:8080/path/to/page.html?query=string#fragment';
$parsed_url = parse_url($full_url);

print_r($parsed_url);


use Pdp\Cache;
use Pdp\CurlHttpClient;
use Pdp\Manager;
use Pdp\Rules;


$manager = new Manager(new Cache(), new CurlHttpClient());
$rules = $manager->getRules(); //$rules is a Pdp\Rules object
$parsed_domain = $rules->resolve($parsed_url['host']); //$domain is a Pdp\Domain object

echo "REG: ".$parsed_domain->getRegistrableDomain();

die_to_print($parsed_domain);
*/





/*
var_dump(

	getUserInfo(3)

); exit;



die_to_print(

	getUserInfo(123)

);
*/



//die_to_print( parseUrl("http://lori.twelve12.co/wp-content/plugins/goodlayers-core/plugins/combine/fontawesome/fontawesome-webfont.eot#1552307384")['full_path'] );


/*
var_dump( Page::ID(6)->getUsers(true) );
exit;
*/

/*
var_dump( $User->canAccess(23, "device") );
exit;
*/


/*
$date = new DateTime('2019-03-10 21:22:36');
echo $date->format('g:i A - F j, Y');
exit;
*/

/*
var_dump( checkAvailableUserName('bill-tas') );

exit;



$notifications = $User->getNotifications();
die_to_print($db->totalCount);

var_dump( $notifications );
exit;

$i = 0;
while ($i < 10) { $i++;


	//var_dump( User::ID($i)->canAccess(33, "pin") );

	Notify::ID(6)->web("sent test notification $i", "pin", 1);
	sleep(2);

}

exit;
*/

/*
$parsed_url = $User->getNotifications();
die_to_print( $parsed_url );
*/



//echo Project::ID(6)->getInfo('user_ID');

/*
$the_user = $User;

print_r($the_user);

if ( $the_user ) echo "YES";
else var_dump($the_user);
*/



/*
// Your crawler was sent to this page.
$url = 'http://example.com/page';

// Example of a relative link of the page above.
$relative = '/hello/index.php';

// Parse the URL the crawler was sent to.
$url = parse_url($url);

if(FALSE === filter_var($relative, FILTER_VALIDATE_URL))
{
    // If the link isn't a valid URL then assume it's relative and
    // construct an absolute URL.
    print $url['scheme'].'://'.$url['host'].'/'.ltrim($relative, '/');
}
exit;
*/





/*
var_dump(split_url('https://www.apple.com/wss/fonts?families=SF+Pro,v2|SF+Pro+Icons,v1'));
die();
*/

// var_dump(url_to_absolute('https://www.apple.com/wss/fonts?families=SF+Pro,v2|SF+Pro+Icons,v1', '/wss/fonts/SF-Pro-Display/v2/sf-pro-display_regular-italic.ttf'));


/*
Mail::ASYNCSEND(
	'bilalltas@gmail.com,bill@twelve12.com',
	urlencode('Bulk mail test'),
	urlencode('Test mail')
);
die();
*/

/*
Mail::SEND(
	'bilalltas@gmail.com,bill@twelve12.com',
	'asdasd',
	'Test mail'
);
die();
*/

/*
$message = "Test Message çıktı";
//die_to_print( urldecode( urlencode($message) ) );



$sent = Notify::ID([1, 5])->mail(
	'Konu test',
	$message
);
if ($sent) echo "SENT! $sent";
*/