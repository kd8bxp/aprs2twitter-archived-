<?php

//foursquare script verions 1.2b APRS2twitter script version 9.0b
//modified to use file_get_contents for use with windows 7
//modified to include $comment from becon postings.


if ($fspostto == '0') {return;} 

echo "Foursquare Auto Check-In Script using APRS information \n\n";

/*
//This Block Find a Close Veneu
//This is already done now in APRS.php
$fields = "$lat,$lon&limit=1&v=20130101";

$url= $fsapi . 'venues/search?ll=' . $fields . '&oauth_token=' . $fstoken; 

echo "$url \n";

$venue = getfoursq($url);
*/


$vilat = $venue['response']['venues']['0']['location']['lat'];
$vilon = $venue['response']['venues']['0']['location']['lng'];

$vidistance = distance ($lat, $lon, $vilat, $vilon);
echo round($vidistance,2). "\n";

if (round($vidistance,2) > '.5') {$vid='0';} 
else {
$vid = strtoupper($venue['response']['venues']['0']['id']);
}

//echo "$vid\n";

//block done...should have choose the 1st VID

$broadcast = 'public';

if ($fspostfacbook = '1') {$broadcast = $broadcast . ',facebook';}
if ($fsposttwitter = '1') {$broadcast = $broadcast . ",twitter";}

if ($comment == NULL) {$shout = "Updated via APRS";} else {$shout = $comment;}

$postfields = "&shout=$shout&ll=$lat,$lon&v=20130101&broadcast=$broadcast&oauth_token=$fstoken";

//This block will autoupdate/checkin
if ($vid != '0') {
$url= $fsapi . 'checkins/add?venueId=' . $vid;

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, $url);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$buffer=curl_exec($curl_handle);
curl_close($curl_handle);

$json = json_decode($buffer, TRUE);

//print_r($json);

//Block Done.

} else {

echo "No Venues found close, No Autocheck-In\n"; 
}


?>
