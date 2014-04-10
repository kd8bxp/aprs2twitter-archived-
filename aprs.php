
<?php

//date_default_timezone_set('America/New_York');
//APRS script Version 13b modified Mar 30, 2014
require ('functions.php');
include ('tmhOAuth.php');


echo "Script Start: " . date("D M d, Y @ h:i a") . "\n";

$icheck = pingDomain("www.google.com");
if ($icheck == '-1') {echo "$linebreak - Error - Probably NO or VERY SLOW internet connection\n\n"; exit();}

include ('variablesetup.php');

if ($debug == '1') {echo "Current Script: aprs.php $linebreak";}

$count=count($callarry);
echo "$linebreak You have " . $count . " call signs listed $linebreak";
echo "Working....";

$locationarr = getaprs($locapi);
$locationsub1 = $locationarr['entries'];
$locationcount = count($locationsub1);

if ($debug == '1') {echo "locationarr =  "; print_r($locationarr); echo "locationsub1 = "; print_r($locationsub1); echo "locationcount = $locationcount=\n";}

for ($counter1=0; $counter1<=($locationcount)-1; $counter1+=1) {

$loc[$counter1]['name'] = $locationsub1[$counter1]['name'];
$loc[$counter1]['lat'] = $locationsub1[$counter1]['lat'];
$loc[$counter1]['lng'] = $locationsub1[$counter1]['lng'];
$loc[$counter1]['comment'] = $locationsub1[$counter1]['comment'];

if (array_key_exists('speed', $locationsub1[$counter1])) {$loc[$counter1]['speed'] = $locationsub1[$counter1]['speed'];}
else {$loc[$counter1]['speed'] = '0';}

if (array_key_exists('altitude', $locationsub1[$counter1])) {$loc[$counter1]['altitude'] = $locationsub1[$counter1]['altitude'];}
else {$loc[$counter1]['altitude'] = '0';}

$loc[$counter1]['lasttime'] = $locationsub1[$counter1]['lasttime'];

if ($dispurl == '1') {$aprsloc = 'http://aprs.fi/?call=' . urlencode($locationsub1[$counter1]['name']); $urldisp = file_get_contents
($tinyurlurl.$aprsloc);}
 else {$urldisp = ' ';}

$loc[$counter1]['url'] = $urldisp;

}

if ($debug == '1') {echo "*Debug: loc from line 43"; print_r($loc);}


echo "Grabbed APRS Locations Data....";

echo "You have $locationcount sources total. $linebreak";
echo "Working....";

for ($counter1=0; $counter1<=($locationcount)-1; $counter1+=1) {
 if ($loc[$counter1]['lasttime'] > $check1) {$big = $counter1; $check1 = $loc[$counter1]['lasttime'];}
}

if ($big === NULL) {echo "Already Updated via APRS sources$linebreak"; include ('end.php'); exit();}

	echo "Using APRS....".$linebreak."Working....";
	$counter1 = $big;
	$source = strtoupper($loc[$counter1]['name']);
	$lat=$loc[$counter1]['lat'];
	$lon=$loc[$counter1]['lng'];
	$speed=$loc[$counter1]['speed'];
	$timedate=$loc[$counter1]['lasttime'];
	$timecheck = date ("M d", $timedate);
	$comment = $loc[$counter1]['comment'];

if ($debug == '1') {echo "Variables if APRS is being updated\ncounter1 = $counter1, source = $source, lat = $lat, lon = $lon, speed = $speed, timedate = $timedate, timecheck = $timecheck, comment = $comment \n";}

/*
$lookup = 'select * from geo.placefinder where text="'.$lat.','.$lon.'" and gflags="R"';
$json = file_get_contents('http://query.yahooapis.com/v1/public/yql?q='.urlencode($lookup).'&format=json');

if ($debug == '1') {echo "$json \n";echo "url = http://query.yahooapis.com/v1/public/yql?q=".urlencode($lookup)."&format=json&diagnostics=true&callback=place\n";}

$add = json_decode($json, TRUE);

if ($debug == '1') {print_r($add);}

$addy = $add['query']['results']['Result'];

$street = $addy['line1'];
$zipcode = $addy['postal'];
$city = $addy['city'];
$state = $addy['statecode'];

$status = $street . ' ' . $city . ', ' . $state . ' ' . $zipcode;

if ($debug == '1') {echo "Yahoo Address: Line 145: $status $linebreak";}
*/

$fields = "$lat,$lon&limit=1&v=20130101";
$url= $fsapi . 'venues/search?ll=' . $fields . '&oauth_token=' . $fstoken; 
$venue = getfoursq($url);
$names = $venue['response']['venues']['0']['name'];
$locations = $venue['response']['venues']['0']['location'];

$city = $locations['city'];
$state = $locations['state'];

$status = "$names, $city, $state"; 

if ($speed <= '8') {$status = 'Near ' . $status; include ('foursquare.php');} else {$status = 'Passing ' . $status; $xxl = comment($comment);}

echo "Working.....";

if ($debug == '1') {echo "Variable Dump for Disptemp of script: zipcode = $zipcode, checksum2 = $checksum2  $linebreak";}

if ($usunits == '1') {
$distance=$distance*.621371192;
$speed= $speed*.621371192 . ' MPH';
        }
else { $speed=$speed . ' KPH'; }

$distance = mb_substr($distance, 0, strpos($distance, '.')+4);
$distance = round($distance,1 );
$speed = round($speed, 2);

if ($usunits == '1') {
$distance = $distance . ' Miles';
$speed = $speed . 'mph';
        }
      else {
        $distance = $distance . ' Km';
        $speed = $speed . 'kph';
}

if ($debug == '1') {echo "*Debug: After Convertion to U.S. units Distance= $distance, Speed = $speed $linebreak";}

echo "$linebreak Building Status Updates......";
echo "Working....";

$urldisp = $loc[$counter1]['url'];

echo "Almost Done....$linebreak";

$geostatus = "&long=" . $lon . "&lat=" . $lat;

//if ($currentdate == $timecheck)  {
echo "$linebreak Sending Data to networks.... $linebreak";
echo "$status $linebreak $linebreak";

//Think this is where $status should be checked for 140ch or more

$status = "$source $status $urldisp #APRS $version";

echo "\n\n$status";

if ($debug == '1') {echo "Variable dump after The Almost Done part of script: urldisp = $urldisp, geostatus = $geostatus, currentdate = $currentdate, timecheck = $timecheck, status = $status $linebreak";}

$tmhOAuth = new tmhOAuth(array(
  'consumer_key' => $twitterapikey,
  'consumer_secret' => $twitterapisecret,
  'token' => $twitteraccesstoken,
  'secret' => $twitteraccesssecret,
));

echo "\n\ntwitterapikey = $twitterapikey\ntwitterapisecret = $twitterapisecret\ntwitteraccesstoken = $twitteraccesstoken\ntwitteraccesssecret = $twitteraccesssecret\n\n";

$xml = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array('status' => $status,
	'long' => $lon,
	'lat' => $lat
));

echo "$xml \n\n";

//$xml1 = json_decode($xml, TRUE);
//print_r($xml1);

if ($debug == '1') {echo "*Debug: variable dump after update to twitter XML = $xml, Decode = $decode $linebreak";}

$flagprofile = '1';

//}

include ('end.php');

?>
