<?php

//The Main script (APRS.PHP) should call this final script in the event of any errors, and to finish up everything if there are no errors detected
//end.php version 3 for version 10.00b of the APRS2Twitter
//modified 9/23/13 - for version 12b
//modified 3/30/14 - for version 13b 
//Profile updates have not been fixed and are still going to be a work in progress.

if ($debug == '1') {echo "Current Script: END.PHP $linebreak";}

if ($flagprofile === '1') {
include ('arn2.php');
include ('wifi.php'); }
if ($flagprofile === '0') {$checks4 = $line4[0] . ',' . $line4[1];}

//Update Profile Location

/*
if ($profilelocation == '1' && $flagprofile == '1') {
	echo "$linebreak Updating Twitter Profile page.....";
	if ($profilelatlon == '1') {$xyarr = convertdegrees($lat, $lon, '0'); $lat_dms = $xyarr[0]; $lon_dms = $xyarr[1]; $pflocation = 'APRS ' . urlencode($lat_dms) . ',' . urlencode($lon_dms);}
	if ($profilecity == '1') {

if ($debug == '1') {echo "Current Script: getbearing.php $linebreak";}


//This yahoo api still needs to be changed, but not sure what I was doing here a few years ago
$x = file_get_contents('http://local.yahooapis.com/LocalSearchService/V3/localSearch?appid='.urlencode($yahooappkey).'&query=city&latitude='.urlencode($lat).'&longitude='.urlencode($lon).'&results=1&output=json');
$temparray = json_decode($x, TRUE);
$arr = $temparray['ResultSet']['Result'];

$lat2 = $arr['Latitude'];
$lon2 = $arr['Longitude'];
$lat1 = $lat;
$lon1 = $lon;

$bearingradians = atan2(asin($lon2-$lon)*cos($lat),
cos($lat2)*sin($lat) - sin($lat2)*cos($lat)*cos($lon2-$lon));
$bearingdegrees = rad2deg($bearingradians);
$bearing = mb_substr($bearingdegrees, 0, strpos($bearingdegrees, '.'));
if ($bearing < 0) {$bearing = 360 + $bearing % 360;}

$theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist);
$distance = $dist * 111.18957696;

if ($debug == '1') {echo "*Debug: getbearing.php Bearing = $bearing, Distance = $distance $linebreak";}

$direction = getDirection ($bearing);
$pflocation = 'APRS ' . $direction. ' of ' . $city . ', ' . $state;
}
	if ($profiledeclatlon == '1') {$pflocation = urlencode('APRS ' . $lat . ',' . $lon);}
$pflocation = "location=" . $pflocation;
updatecurl ($twitterprofileurl,$pflocation,$uname,$pwd);
echo "Done. $linebreak";
}
*/

if ($debug == '1') {echo "*Debug: END.PHP writting aprscheck.txt $linebreak";}

$handle=fopen($filen, 'w');
fwrite($handle,$check1 . "\r\n");
fwrite($handle,$checksum2 . "\r\n");
fwrite($handle,$checks4 . "\r\n");
fclose($handle);

if ($debug == '1') {echo "*Debug: END.PHP Check For Updated Script. $linebreak";}

echo "Script End: " . date("D M d, Y @ h:i a") . "\n";
exit();

?>
