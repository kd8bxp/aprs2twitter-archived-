<?php
//WIFI Hotspot locator script version 3 - Orignally added for version 7.00.4b of the APRS2Twitter Script
//Updated Mar 30, 2014 for the new Yahoo API, and version 13b 
//WIFI attempts to locate hotspots you are near and send them to you via SMS, Not directions, but addresses & distances
//These may or may not be Free WIFI hotspots, but attempts were made to find Free ones


if ($debug === '1') {echo "Current Script: WIFI.php $linebreak";}
if ($arnflag === '0') {echo "Not out of Radius... $linebreak"; return;}


$wifilookup = 'select * from local.search where latitude="'. $lat . '" and longitude="' . $lon . '" and radius = 5 and query="free wifi"';
$jsonwifi = file_get_contents("http://query.yahooapis.com/v1/public/yql?q=". $wifilookup . "&format=json");
$wifi1 = json_decode($jsonwifi, TRUE);
$wifi = $wifi1['query'];

if ($debug == '1') {print_r($wifi);}

if ($wifi['count'] > '0') {$wificount = $wifi['count'];

$wifi2 = $wifi['results'];
if ($debug == '1') {print_r($wifi2);}

if ($wificount === '1') {
		$msg = 'WIFI update: ' . $wifi2['Result']['Title'] . ' ' . $wifi2['Result']['Address'] . ' ' . $wifi2['Result']['City'] . ', ' . $wifi2['Result']['State'] .  ' ' . $wifi2['Result']['Distance'] . 'm'; 
		echo "$msg \n";
		$temp = sms($msg);
		echo "Sleeping.... $linebreak";
		sleep(10);
		}


for ($counter1=0; $counter1<=($wificount)-1; $counter1+=1) {
		$msg = 'WIFI update: ' . $wifi2['Result'][$counter1]['Title'] . ' ' . $wifi2['Result'][$counter1]['Address'] . ' ' . $wifi2['Result'][$counter1]['City'] . ', ' . $wifi2['Result'][$counter1]['State'] . ' ' . $wifi2['Result'][$counter1]['Distance'] . 'm';
		echo "$msg \n"; 
		$temp = sms($msg);
		echo "Sleeping.... $linebreak";
		sleep(10);
	}

$msg = '---';
$temp = sms($msg);

}


?>
