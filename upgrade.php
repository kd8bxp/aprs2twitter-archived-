<?PHP 
//APRS 2 Twitter Script Version 13b
//upgrade from v12b to v131b

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

$call = $setup['call'];
$uname = $setup['uname']; 
$pwd = $setup['password']; 
$fiapikey = $setup['fiapikey'];
$homedir = $setup['homedirectory'];

$tokens = parse_ini_file($homedir . 'tokens.txt');

$disptemp = $setup['displaytemp'];
$dispurl = $setup['displayurl']; 
$profilelocation = $setup['updateprofile'];
$profilelatlon = $setup['dmslatlon']; 
$profilecity = $setup['profilecity'];
$profiledeclatlon = $setup['decimallatlon'];
$dispmessage = $setup['displaymsg']; 
$usunits = $setup['usunits']; 
$phonenumber = $setup['phonenumber'];
$token = $setup['token'];
$posttumblr = $setup['posttotumblr'];
$tumblr_email    = $setup['tumblremail'];
$tumblr_password = $setup['tumblrpassword'];
$fromcall = $setup['fromcall'];
$dlat = $setup['dlat'];
$dlon = $setup['dlon'];
$arnradius = $setup['arnradius'];
$arradius2 = $setup['arnradius2'];
$arntwitter = $setup['arntwitter'];
$arnsms = $setup['arnsms'];
$arnband2 = $setup['arn2meter'];
$arnband4 = $setup['arn440'];
$dzipcode = $setup['zipcode'];
$onefortyapi = $setup['140api'];
$hashtag = $setup['hashtag'];
$useoneforty = $setup['use140plus'];
$fstoken = $tokens['foursquare']; //token
$fspostfacebook = $setup['fspostfacebook'];
$fsposttwitter = $setup['fsposttwitter'];
$fspostto = $setup['fspostto'];
$postbeaconcomment = $setup['postbeaconcomment'];

echo "UPDATING From version 12b to version 13b\nAPRS2Twitter By LeRoy F. Miller, KD8BXP Copyright (C) 2011-2013\n";
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

echo "While it is possable to have this script work outside the United States, there are some issues, \n";
echo "and some features that do NOT appear to work out side of the United State. Please bear this in mind.\n\n";
echo "Do you live outside of the U.S.? (Y or N): ";
$temp = yesno();
if ($temp == '1') {
echo "Do you understand that some features may or may not work out of the U.S.? (Y or N): ";
$temp = yesno();
if ($temp == '1') {echo "Please keep this in mind while setting up, setup.ini can be edited in a text editor should you find\na feature that doesn't work.\n\n";}
}
echo "Privacy Statement: APRS Data is transmitted in the clear, If you DO NOT wish to have this information placed on\n";
echo "very public location. Then this script is not for you. However the APRS info can be found by anyone who knows where to look\n";
echo "Please read the privacy policys for the other sites that this script uses. And decide for yourself. This script works by using\n";
echo "various APIs from different sources. You run this script on your local machine, any privacy issue is up to you\n\n";

echo "Do you agree to the privacy statement: (Y or N): ";
$temp = yesno();
if ($temp == '0') {echo "Thank you for giving the script a look. Please consider usage later.\n\n"; die();}


echo "\n\nSetup oAuth for Twitter:\nIn order and as per Twitter's api useage rules, everyone needs to setup their own 'app', this is fairly simple and fairly straight forward.\n";
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


if ($foursquaretoken === '') {
echo "Step 11: Setting up FourSquare oAuth2 Tokens\n";
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

}









echo "\n\nPlease wait while I delete some old files, and remove some and update other features, this should NOT take that long, but is needed.. Thanks. \n\n";


$handle=fopen("setup.ini", 'w');
fwrite($handle,"[General Setup]\n");
fwrite($handle,"call = " .$call."\n");
fwrite($handle,"fiapikey = ".$fiapikey."\n");
fwrite($handle,"homedirectory = ".$homedir."\n");
fwrite($handle,"displayurl = ". $dispurl."\n");
fwrite($handle,"updateprofile = " .$profilelocation."\n");
fwrite($handle,"dmslatlon = ".$profilelatlon."\n");
fwrite($handle,"profilecity = ".$profilecity."\n");
fwrite($handle,"decimallatlon = ".$profiledeclatlon."\n");
fwrite($handle,"usunits = ".$usunits."\n");
fwrite($handle,"phonenumber = ".$phonenumber."\n");
fwrite($handle,"token = ".$token."\n");
fwrite($handle,"fromcall = ".$fromcall."\n");
fwrite($handle,"dlat = ".$dlat."\n");
fwrite($handle,"dlon = ".$dlon."\n");
fwrite($handle,"arnradius = ".$arnradius."\n");
fwrite($handle,"arnradius2 = ".$arradius2."\n");
fwrite($handle,"arnsms = ".$arnsms."\n");
fwrite($handle,"arn2meter = ".$arnband2."\n");
fwrite($handle,"arn440 = ".$arnband4."\n");
fwrite($handle,"zipcode = ".$dzipcode."\n");
fwrite($handle,"fspostfacebook = ".$fspostfacebook."\n");
fwrite($handle,"fsposttwitter = ".$fsposttwitter."\n");
fwrite($handle,"fspostto = ".$fspostto."\n");
fwrite($handle,"postbeaconcomment = ".$postbeaconcomment."\n");
fwrite($handle,"twitterapikey = " . $twitterapikey."\n");
fwrite($handle,"twitterapisecret = " . $twitterapisecret."\n");
fwrite($handle,"twitteraccesstoken = " . $twitteraccesstoken ."\n");
fwrite($handle,"twitteraccesssecret = " . $twitteraccesssecret ."\n");
fwrite($handle,"foursquaretoken = " . $fstoken ."\n");
fclose($handle);


echo "Weather reporting is no longer being supported, there are plenty of other ways to get Weather updates, now. \n\n";
echo "The following files are no longer needed or required: \nvariablesetup2.php\ncurrentwx.php\nforecastwx.php\ntumblr.php\nwxalert.php\n The following files have been updated - setup.ini \nIF you msg.php still in your directory, this can be manually removed, as it is no longer supported.";
echo "\nThe Foursquare token has been moved back into the setup.ini file, tokens.txt will remain just in case there is a problem, but the file is no longer needed.\n\n";


unlink('variablesetup2.php');
unlink('currentwx.php');
unlink('forecastwx.php');
unlink('tumblr.php');
unlink('wxalert.php');


echo "Your APRS directory should contain only the following files: \naprs.php\narn2.php\nend.php\nfoursquare.php\nfunctions.php\nvariablessetup.php\nwifi.php\ntmhOAuth.php\ncacert.perm\ncomposer.json\n";
echo "aprscheck.txt\nsetup.ini\ntokens.txt";
echo "\nThe following files can be removed when this upgrade is complete: \nsetup.php and upgrade.php can also be removed, they are causing no harm.\n";


//Functions go here

function yesno() {
$temp = trim(fgets(STDIN));
$temp = strtoupper($temp);
if ($temp == 'Y') {$returns='1';} else {$returns='0';}
return ($returns);
}

function clear() {
$i=0;
for ($i=0; $i <= 60; $i += 1) {echo "\n";}
return;
}


?>
