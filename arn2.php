<?php
//Repeater Database from APRS -> Provided by Amateur-Radio.net repeater Database <- 
//limitations - Jun 22, 2010 - No way to remove closed repeaters as of yet & IRLP & Echolink nodes not yet sent. Still very much a work in progress
//Script Version 8.01 APRS2Twitter Script v10.00b

if ($debug == '1') {echo "Current Script: ARN2.PHP $linebreak";}

if ($arnband2 == '1') {$band = $band . '144%2C';}
if ($arnband4 == '1') {$band = $band . '440%2C';}

if ($arradius2 <=9) {$arradius2 = '10';}

$arndistance = distance($dlat, $dlon, $lat, $lon);

if ($usunits == '1') {$arndistance = $arndistance * .621371192;}
if ($arndistance <= $arnradius) {echo "Not out of radius $linebreak"; $checks4 = $line4[0].','.$line4[1]; return;}

$chklat = $line4[0];
$chklon = $line4[1];

if ($chklat == NULL) {$chklat = '0';}
if ($chklon == NULL) {$chklon = '0';}

$arndistance = distance($chklat, $chklon, $lat, $lon);

if ($usunits == '1') {$arndistance = $arndistance * .621371192;}
if ($arndistance <= $arradius2) {
	echo "Not out of radius $linebreak"; $checks4 = $chklat.','.$chklon;
	return;}

$xy = convertdegrees ($lat, $lon, 1);
$dmslat = $xy[0];
$dmslon = $xy[1];

if (mb_substr($dmslat,0,1) == '-') {$arnlat = mb_substr($dmslat, 0, 8);} else {$arnlat = mb_substr($dmslat, 0, 7);}
if (mb_substr($dmslon,0,1) == '-') {$arnlon = mb_substr($dmslon, 0, 8);} else {$arnlon = mb_substr($dmslon, 0, 7);}
$arnlat=str_replace('.', '', $arnlat);
$arnlon=str_replace('.', '', $arnlon);
if ($arnlat < 0) {$tarnlat = str_replace('-', '', $arnlat); $arnlat=$tarnlat . 'S';} else {$arnlat = $arnlat . 'N';}
if ($arnlon < 0) {$tarnlon = str_replace('-', '', $arnlon); $arnlon=$tarnlon . 'W';} else {$arnlat = $arnlat . 'E';}


$buffer1 = getarn($arnurl, $post);

$buffer2=strip_tags($buffer1);
$buffer3 = mb_strcut($buffer2, mb_strpos($buffer2, 'Source')+6);
$buffer4 = mb_strcut($buffer3,  0, mb_strpos($buffer3, 'Websearch'));

if ($buffer4 == NULL) {echo "Possable error - no repeaters found. $linebreak"; $checks4=$chklat.','.$chklon; return;}

$lines1 = explode("\n", $buffer4);

foreach ($lines1 as $key => $value) {
        if ($value == "") {
        unset($lines1[$key]);
}
}

$lines = array_values($lines1);
$count = count($lines);

if ($lines == NULL) {echo "Error in location or No Repeater Data $linebreak"; $checks4=$chklat.','.$chklon; return;}

for ($counter1=0; $counter1 <= $count-1; $counter1 +=1)
{
$temp1 = $lines[$counter1]; 
$temp = explode("`", $temp1);

if ($temp[8] == "DSTAR") {$temp[4] = $temp[5];}

$temp[3] = rtrim(trim($temp[3], '0'), '.');
$temp[4] = rtrim(trim($temp[4], '0'), '.');
$repeater[$counter1] = array('city' => $temp[1], 'state' => $temp[2], 'freq' => $temp[3], 'pl' => $temp[4], 'call' => $temp[5], 'distance' => trim($temp[6]), 'notes' => $temp[8]);

}


foreach($repeater as $item) {
    $output_array[$item['city']][] = array(
            'freq' => $item['freq'],
            'pl' => $item['pl']
        );
}

foreach($output_array as $city => $freq_list) {
    $freqs = array();
    foreach($freq_list as $freq) {
        $freq_str = $freq['freq'];
	if($freq['pl']) $freq_str .= "/" . $freq['pl'];
        $freqs[] = $freq_str;
    }
    $rpt[] = $city . " " . implode(", ", $freqs);
}

$count = count($rpt);

for ($counter1=0; $counter1 <= $count-1; $counter1 +=1)
{$msg = str_replace("/none", '', $rpt[$counter1]);
$strcount = mb_strlen($msg);

if ($strcount > 120) {$status1 = mb_strcut($msg,0, 120); $status2 = mb_substr($status1, 0, strripos($status1, ",")); $msg=$status2 . ' more';} 


echo "$msg $linebreak";

if ($arnsms == '1') {
	$temp = sms($msg);
	echo "Sleeping.... $linebreak";
	sleep(10);}

}

$msg = 'Repeaters provided by Amateur-Radio.net with many thanks';
if ($arnsms == '1') {
		$temp = sms($msg);}
$msg = '----';
if ($arnsms == '1') {
		$temp = sms($msg); }

$arnflag = '1';
$checks4=$lat.','.$lon;
return;

?>
