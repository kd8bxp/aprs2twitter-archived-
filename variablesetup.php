<?PHP 

//APRS 2 Twitter Script Version 13b Last Updated Mar 27, 2014

/* 
APRS2Twitter Copyright (C) 2010  LeRoy F. Miller, KD8BXP

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/

*/

$debug = '0'; //Normal operation this should be set to ZERO (0), running it debug mode will display more information, should be used for troubleshooting
//When using DEBUG mode, it is best to pipe the output to a text file, The script will display a lot of extra information, most of which is not helpful
//if it goes by too fast! Most OSes have a limited scroll back buffer, so piping will make it much easier to read and understand.
// Normal pipe command:
//php aprs.php >sometextfile.txt

//Nothing below this line should need editing.

$version = '13b';

$setup = parse_ini_file('setup.ini');
$linebreak = "\n";
$call = $setup['call'];
$fiapikey = $setup['fiapikey'];
$homedir = $setup['homedirectory'];
$dispurl = 1; 
$profilelocation = $setup['updateprofile'];
$profilelatlon = $setup['dmslatlon']; 
$profilecity = $setup['profilecity'];
$profiledeclatlon = $setup['decimallatlon'];
$usunits = $setup['usunits']; 
$phonenumber = $setup['phonenumber'];
$token = $setup['token'];
$fromcall = $setup['fromcall'];
$dlat = $setup['dlat'];
$dlon = $setup['dlon'];
$arnradius = $setup['arnradius'];
$arradius2 = $setup['arnradius2'];
$arnsms = $setup['arnsms'];
$arnband2 = $setup['arn2meter'];
$arnband4 = $setup['arn440'];
$dzipcode = $setup['zipcode'];
$fstoken = $setup['foursquaretoken']; 
$fspostfacebook = $setup['fspostfacebook'];
$fsposttwitter = $setup['fsposttwitter'];
$fspostto = $setup['fspostto'];
$postbeaconcomment = $setup['postbeaconcomment'];
$twitterapikey = $setup['twitterapikey'];
$twitterapisecret = $setup['twitterapisecret'];
$twitteraccesstoken = $setup['twitteraccesstoken'];
$twitteraccesssecret = $setup['twitteraccesssecret'];

//Init All Other Variables

$speed = 0;
$distance = 0;
$check1 = '';
$checksum2 = '';
$checks4 = '';
$check2 = '';
$check3 = '';
$checksum3 = '';

echo "Setting up Variables.... $linebreak";

//API Keys

$arnflag = '0';
$band = '';
$call = strtoupper($call);
$credit = 'Script by LeRoy Miller, KD8BXP';

$useragent = 'APRS2Twitter/'.$version.'(http://code.google.com/p/aprs2twitter)';
$callarry=explode(",", $call);
$checksum1=array();

$filen= $homedir . 'aprscheck.txt';
$tinyurlurl = 'http://tinyurl.com/api-create.php?url=';

$updateurl = 'http://code.google.com/p/aprs2twitter';
$currentdate = date ("M d");
$big = NULL;
$arnurl = 'http://rptr.amateur-radio.net/cgi-bin/exec.cgi'; 
$title = 'APRS2Twitter Copyright (C) 2010 LeRoy F. Miller, KD8BXP. Under GNU General Public License (GPLv3) Version ' . $version;
$locapi = 'http://api.aprs.fi/api/get?name=' . urlencode($call) . '&what=loc&apikey=' . $fiapikey;
$msgapi = 'http://api.aprs.fi/api/get?dst=' . urlencode($call) . '&what=msg&apikey=' . $fiapikey;
$flagprofile = '0';
$distance = '0';
$fsapi = 'https://api.foursquare.com/v2/';

echo $title;
ini_set('user_agent', $useragent);

echo "$linebreak Openning check file..... ";

if (file_exists($filen)) {echo " script check file is Ok $linebreak ";} else {$handle = fopen ($filen, "w"); fwrite($handle, "0"); 
fclose($handle); }
$handle=fopen($filen, 'r');
$check1 = trim(fgets($handle), "\r\n"); //last timestamp from aprs (latitude/instamapper)
$check2 = trim(fgets($handle), "\r\n"); //last zipcode
//$check3 = trim(fgets($handle), "\r\n"); //removed, Can be readded later, Line 4 has become line 3, however the variable was not changed. 
$check4 = trim(fgets($handle), "\r\n"); //latitude/longitude used to calculator circles
fclose ($handle);
if ($check2 == "") {$line2 = $dzipcode;} else {$line2 = $check2;}
if ($check2 == "0") {$line2 = $dzipcode;} else {$line2 = $check2;}
if ($check2 == NULL) {$line2 = $dzipcode;} else {$line2 = $check2;}
$line4 = explode(",",$check4);

echo "Done. $linebreak";

?>
