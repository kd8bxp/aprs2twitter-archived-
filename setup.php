<?php
//Setup.php version 2.0 for version 13b of APRS2Twitter script
//By LeRoy F. Miller, KD8BXP Copyright (C) 2011 ~ 2014

$i = clear();

$filename = "setup.ini";
$handle = fopen($filename, "a+");


echo "Setup for APRS2Twitter By LeRoy F. Miller, KD8BXP Copyright (C) 2011\n";
echo "This program is free software: you can redistribute it and/or modify\n";
echo "it under the terms of the GNU General Public License as published by\n";
echo "the Free Software Foundation, either version 3 of the License, or\n";
echo "(at your option) any later version.\n\n";
echo "This program is distributed in the hope that it will be useful,\n";
echo "but WITHOUT ANY WARRANTY; without even the implied warranty of\n";
echo "MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n";
echo "GNU General Public License for more details.\n\n";
echo "You should have received a copy of the GNU General Public License\n";
echo "along with this program.  If not, see http://www.gnu.org/licenses/\n\n";

echo "While it is possable to have this script work outside the United States, there are some issues, and some things don't work\n";
echo "It is important to remember the APRS information does seem to work\n";
echo "But some other features do NOT appear to work out side of the United State. Please bear this in mind.\n\n";
echo "Do you live outside of the U.S.? (Y or N): ";
$temp = yesno();
if ($temp == '1') {
echo "Do you understand that some features may or may not work out of the U.S.? (Y or N): ";
$temp = yesno();
if ($temp == '1') {echo "Please keep this in mind while setting up, setup.ini can be edited in a text editor should you find\na feature that doesn't work.\n\n";}
}
echo "Privacy Statement: APRS Data is transmitted in the clear, If you DO NOT wish to have this information placed on\n";
echo "very public location. Then this script is not for you. However the APRS info can be found by anyway who knows where to look\n";
echo "Please read the privacy policys for the other sites that this script uses. And decide for yourself. This script works by using\n";
echo "various APIs from different sources. You run this script on your local machine, any privacy issue is up to you\n\n";

echo "Do you agree to the privacy statement: (Y or N): ";
$temp = yesno();
if ($temp == '0') {echo "Thank you for giving the script a look. Please consider usage later.\n\n"; fclose($handle); die();}

echo "Please answer the questions, and follow the instructions to setup the APRS2Twitter Script\n\n";
fwrite($handle, "[General Setup]\n");
echo "Step 1: General Setup\n";
echo "Please enter the callsigns you wish to follow on APRS.FI (Up to 10 callsigns seperated by commas)? ";
$call = trim(fgets(STDIN));
$wrg = write("call", $call);

echo "Please login to your APRS.IF account and click on 'My Account', there you'll find your APRS.FI Key, please copy and paste it\nhere: ";
$fiapikey = trim(fgets(STDIN));
$wrg = write("fiapikey", $fiapikey);

echo "Please enter the 'Home Directory' of your script\nIE: /var/username/aprs or  c:/aprs/\nNotice the trailing / must be included: ";
echo "\nOn Linux Installs this directory must have read/write access.\n This normally isn't a issue for a Windows install.\n:";
$homedir = trim(fgets(STDIN));
$wrg = write("homedirectory",$homedir);


$i = clear();
echo "Step 2: Twitter Profile Questions\n";
echo "Change profile location on twitter? (Y or N): ";
$profilelocation = yesno();
$wrg = write("updateprofile", $profilelocation);
$profilelatlon = '0';
$profilecity = '0';
$profiledeclatlon = '0';
if ($profilelocation == '1') {
	echo "Please select how you'd like your profile location information displayed:\n(1) Lat/Lon in Deg/Min/Sec format\n(2) City and State\n(3)Lat/Lon in decimal format\nPlease enter (1,2,3)";
	$temp = trim(fgets(STDIN));
	if ($temp == '1') {$profilelatlon = '1';}
	if ($temp == '2') {$profilecity = '1';}
	if ($temp == '3') {$profiledeclatlon = '1';}
	}
$wrg = write("dmslatlon", $profilelatlon);
$wrg = write("profilecity", $profilecity);
$wrg = write("decimallatlon", $profiledeclatlon);


$i = clear();
echo "Step 3: SMS Information Questions:\n";
echo "The script has the ability to send SMS messages to you for some events namely Repeaters you are close while traveling, and WIFI Hotspots\nIf you would like to set this you will need a Tropo.com API token, please goto\nTropo.com now and get yours, then come back and continue the setup. \nWould you like to use SMS? (Y or N): ";
$temp = yesno();
$phonenumber = '';
$token = '';
		if ($temp == '1') {
			echo "Please enter your phone number (NO minus signs, or parenthes) Include area code: ";
			$phonenumber = trim(fgets(STDIN));
			echo "IT's best to copy and paste this\nPlease enter your Tropo.com API Token: ";
			$token = trim(fgets(STDIN));
			}

$wrg = write("phonenumber", $phonenumber);
$wrg = write("token", $token);

$i=clear();
echo "Step 4: Local Information Questions:\n";
echo "The Following Questions are about some local information that the script needs to work correctly\nUsing ZIPINFO.com/search/zipcode.htm you can find your lat/lon in this format XX.XXX,\nif you need a minus please remember to use it\nPlease enter your default latitude: (XX.XXX): ";
$dlat = trim(fgets(STDIN));
echo "Please enter your default longitude: (xxx.xxx): ";
$dlon = trim(fgets(STDIN));
echo "Please enter your zipcode: (XXXXX): ";
$dzipcode = trim(fgets(STDIN));
echo "Use U.S. units IE: Farinheit, Miles, MPH? (Y or N): ";
$usunits = yesno();

$wrg = write('dlat', $dlat);
$wrg = write('dlon', $dlon);
$wrg = write('zipcode', $dzipcode);
$wrg = write('usunits', $usunits);


$i = clear();

echo "Step 5: Amateur Radio Network Questions: \n";
echo "The Amateur Radio Network is kind enough to provide repeater information, this information can be sent to SMS or Twitter or Both\nIt will also be sent to Tumblr if Tumblr is being used.\nThis part takes a little explaining:";
echo "The script uses two circles based on distance in miles, this first circle is how many miles from your home lat/lon you'd like to go before\nyou get repeaters sent to you, the 2nd circle also in miles, is the distance from the location of the last time repeaters where sent\nIn other words: \nIf you have your 1st circle set for 10 miles, and your 2nd circle set for 10 miles (10 being the smallest distance that can be used)\nIF you travel from your home location 15 miles away (Call that point B), You'll get repeater updates\nLet's say you continue on to point C which is only 3 miles from point B\nYou will not get any new repeaters,  You continue to point D which is 15 miles from point C - you will get repeaters. Now Lets say Point D is \ninside the 1st circle - YOU Will not get repeater updates.\nThis setting need a to be used a few times, and the right combination needs to be found for your location\nsetup.txt can be edited in notepad or gedit, the block you are looking for is [Amateur Radio Network Information]\n";
echo "Please tell me what you'd like your 1st circle to be set to in miles: ";
$arnradius = trim(fgets(STDIN));
echo "Please tell me what you'd like your 2nd circle to be set to in miles: ";
$arradius2 = trim(fgets(STDIN));
echo "The script can send the repeaters to your phone by SMS\nNOTE: This maybe a lot of repeaters, standard SMS rates will apply\nWould you like to have the repeater updates send to your phone: (Y or N): ";
$arnsms = yesno();
echo "Amateur Radio Network lets you choice which type of repeaters you'd like to look up\nLookup 2 meter repeaters? (Y or N): ";
$arnband2 = yesno();
echo "Lookup 440 repeaters? (Y or N): ";
$arnband4 = yesno(); 

$wrg = write('arnradius', $arnradius);
$wrg = write('arnradius2', $arradius2);
$wrg = write('arnsms', $arnsms);
$wrg = write('arn2meter', $arnband2);
$wrg = write('arn440', $arnband4);

echo "Step 6: Setting up FourSquare oAuth2 Tokens\n";
echo "Script needs access to the Four Square API, what this means is you will need to grant access to the API.\nYou do not need to let the script post to Four Square unless you want to, but it does\nneed access to the API to work.";


echo "Foursquare uses oAuth v2 for authorization, you wil need to goto this website:\n";
		echo "https://foursquare.com/oauth2/authenticate?client_id=NQ34I2GKBL124EOQ3ZAIJLWY0UVMG44OQ1DQ4XDFRL4BA5N0&response_type=code&redirect_uri=http://kd8bxp.net63.net/gettoken.php\n";
		echo "and authorize the aprs2twitter app access to update your foursquare account.\n";
		echo "You Will be redirected to your TOKEN, it will be displayed, please copy and paste it here\n(P.S. See the special notes in the readme file)";
		echo "Foursquare Token: ";
		$fstoken = trim(fgets(STDIN));
		
echo "The script can automatically check you in to foursqaure, this feature is still experimental\n(Sort of, the script does a fairly good job of checking you into the correct location but it may not. If you are worried\n";
echo "about this I would say to not setup this feature.\n";
echo "Would You like to setup Foursquare for autocheck ins? (Y or N): ";
$temp =yesno();
$fspostfacebook = '';
$fsposttwitter = '';
	if ($temp == '1') {
		echo "Foursquare Can also update your Facebook & Twitter status, \n";
		echo "Would you like Foursquare to also update Facebook? (Y or N): ";
		$fspostfacebook = yesno();
		echo "Would you like Foursquare to also update Twitter? (Y or N): ";
		$fsposttwitter = yesno();
}
$wrg = write('foursquaretoken', $fstoken);
$wrg = write('fspostfacebook', $fspostfacebook);
$wrg = write('fsposttwitter', $fsposttwitter);
$wrg = write('fspostto', $temp);



$i = clear();
echo "Step 7: Using/Posting Beacon Comments\n";
echo "The script can post your beacon comments to twitter, this could be handy if you have your current Frequency or use them to tell how you can \n";
echo "be contacted off twitter. (I put an IRLP node I am monitoring in most of the time)\n";
echo "Would you like to post your beacon comments: (Y or N)? ";
$temp = yesno();

$wrg = write('postbeaconcomment', $temp);

$i = clear();
echo "Step 8: Setting up Twitter oAuth\n";
echo "In order and as per Twitter's api useage rules, everyone needs to setup their own 'app', this is fairly simple and fairly straight forward.\n";
echo "Point your web browser to https://dev.twitter.com and sign in to your twitter account. IF you are not a developer you may need to setup your developer account\n";
echo "Once you log-in slide over your icon in the upper right hand conner, a menu will slide down, one of the options will be 'My applications' click on that \n";
echo "Next you will see a list of the applications you own, or a button saying 'Create New App' click the button\n";
echo "You can name your app anything you like - I call mine KD8BXP-APRS-Script\n For a describition I said 'Sends your APRS position to twitter'\nFor the website: 'http://code.google.com/p/aprs2twitter'\nYou will not need a callback URL leave this blank\n";
echo "Agree to the terms: and click next, You should now see the details of your app \n Click on API Keys, these will be what we need to setup the script to send updates to twitter\n\n";

echo "There will be four keys that will be needed. Near the bottom of the page you need to click on 'Create my access token' you may need to refresh the page to see them\n";
echo "1st lets look at the Application settings: The script will need your API key Please enter it now, copy and paste is the best way if you can.\n: ";

$twitterapikey = trim(fgets(STDIN));
echo "\nNext we will need your Application API Secret, again copy and paste is the best\n:";
$twitterapisecret = trim(fgets(STDIN));
echo "\nAlmost done, Look in the 'Your access token' section, and we need your Access Token\n:";
$twitteraccesstoken = trim(fgets(STDIN));
echo "\nLast we need your Secret Access Token\n:";
$twitteraccesssecret = trim(fgets(STDIN));

$wrg = write('twitterapikey', $twitterapikey);
$wrg = write('twitterapisecret', $twitterapisecret);
$wrg = write('twitteraccesstoken', $twitteraccesstoken);
$wrg = write('twitteraccesssecret', $twitteraccesssecret);


//This is the end of the setup anything that needs to be added or changed, should be above this line.
$i=clear();
echo "Setup is complete, if you need to change anything you can edit setup.txt with any text editor\n";
echo "\n\nAPRS2Twitter Project can be found at http://code.google.com/p/aprs2twitter\nAPRS copyright (c) Bob Burninga, WB4APR.\nAPRS2Twitter Script makes use of the following APIs with much Thanks.\nYahoo Business & Local Search API, APRS.FI API, Tumblr API, Status.net API, Instamapper.com API, Google Latitude, Weather Bug API, Amateur-Radio.net,\nSupertweet.net API (Both for Supertweet & the Local API), Twitter, Tropo.com API, 140plus.com, and any other APIs I may have forgotten.\nWith respect to all copyright holders\n\n";


fclose($handle);

//Functions go here

function yesno() {
$temp = trim(fgets(STDIN));
$temp = strtoupper($temp);
if ($temp == 'Y') {$returns='1';} else {$returns='0';}
return ($returns);
}

function write($temps, $name) {
fwrite ($GLOBALS['handle'],$temps . " = " . $name . "\n");
return;
}

function clear() {
$i=0;
for ($i=0; $i <= 60; $i += 1) {echo "\n";}
return;
}

?>
