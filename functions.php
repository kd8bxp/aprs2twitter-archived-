<?php
//modified 9/23/13 for version 12b
//Functions Go Here:

function pingDomain($domain){
    $starttime = microtime(true);
    $file      = fsockopen ($domain, 80, $errno, $errstr, 10);
    $stoptime = microtime(true);
    $status    = 0;

    if (!$file) $status = -1;  // Site is down
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}

function getaprs($getapi) {
//Getting All Information from APRS.FI 
$json = file_get_contents($getapi);
if (!$json) {
       print "api call failed, bad JSON returned $linebreak";
       exit();
}

$arr = json_decode($json, TRUE);
if ($arr['result'] != 'ok') {
       print "api call failed: $arr[description] $linebreak";
       exit();
}
return $arr;
}

function updatecurl($url,$stats,$user,$password) {

echo "$url \n$stats \n$user \n$password \n";

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$url");
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "$stats");
curl_setopt($curl_handle, CURLOPT_USERPWD, "$user:$password");
$buffer=curl_exec($curl_handle);
curl_close($curl_handle);

echo "$buffer \n";

return $buffer;
}



function getDirection($bearing) {
$dirs = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
$direction = $dirs[round($bearing/45)];
        return $direction;
}

function convertdegrees ($latdd, $londd, $arn) {
$vars = explode(".",$latdd);
    $latdeg = $vars[0];
    $tempma = "0.".$vars[1];
    $tempma = $tempma * 3600;
    $latmin = floor($tempma / 60);
    $latsec = round($tempma - ($latmin*60),0);

if (strlen($latmin) < '2') ($latmin = '0'.$latmin);
if (strlen($latsec) < '2') ($latsec = '0'.$latsec);

$vars = explode(".",$londd);
    $londeg = $vars[0];
    $tempma = "0.".$vars[1];
    $tempma = $tempma * 3600;
    $lonmin = floor($tempma / 60);
    $lonsec = round($tempma - ($lonmin*60),0);

if (strlen($lonmin) < '2') ($lonmin = '0'.$lonmin);
if (strlen($lonsec) < '2') ($lonsec = '0'.$lonsec);

if ($arn == '1') {
	$dmslat = round($latdeg.$latmin.str_replace('.', '', $latsec),4);
	$dmslon = round($londeg.$lonmin.str_replace('.', '', $lonsec),4);
} else {$dmslat = $latdeg.'°'.$latmin."'".$latsec.'"';
$dmslon = $londeg.'°'.$lonmin."'".$lonsec.'"';
}

$xy[0] = $dmslat;
$xy[1] = $dmslon;

return $xy;
}

function distance ($lat1, $lon1, $lat2, $lon2) {
$theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist);
$distance = $dist * 111.18957696;

return $distance;
}

function xmldecode($txt)
{
    $txt = str_replace('&amp;',		'&',	$txt);
    $txt = str_replace('&lt;',		'<',	$txt);
    $txt = str_replace('&gt;',		'>',	$txt);
    $txt = str_replace('&apos;',	"'",	$txt);
    $txt = str_replace('&quot;', 	'"',	$txt);
    return $txt;
}

function split_to_chunks($to,$text){
	$total_length = (135 - strlen($to));
	$text_arr = explode(" ", $text);
	$i=0;
	$message[0]="";
	foreach ($text_arr as $word){
		if ( strlen($message[$i] . $word . ' ') <= $total_length) {
			if ($text_arr[count($text_arr)-1] == $word){
				$message[$i] .= $word;
			} else {
				$message[$i] .= $word . ' ';
			}
		} else {
			$i++;
			if ($text_arr[count($text_arr)-1] == $word) {
				$message[$i] = $word;
			} else {
				$message[$i] = $word . ' ';
			}
		}
	}
	return $message;
}

function getdm($user,$password, $dmurl) {
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$dmurl");
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_USERPWD, "$user:$password");
$buffer=curl_exec($curl_handle);
curl_close($curl_handle);

return $buffer;
}

function distroydm($destroyurl,$user,$password) {
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$destroyurl");
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($curl_handle, CURLOPT_USERPWD, "$user:$password");
$buffer=curl_exec($curl_handle);
curl_close($curl_handle);

return $buffer;
}

function getarn($arnurl, $post) {
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$arnurl");
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "$post");
$buffer1=curl_exec($curl_handle);
curl_close($curl_handle);
return $buffer1;
}

function message($msgcount, $msgsub, $line2, $smscheck) {
//Attempt to limit messages sent to twitter to just recent and current messages - also attempts to not duplicate

//echo "msgcount = $msgcount, check# = $line2 \n";

//print_r($msgsub);

$count = 0;
$msg = '';

for ($counter1 = 0; $counter1<=($msgcount)-1; $counter1+=1) {
			if ($msgsub[$counter1]['messageid'] > $line2) {
			$msgsub1[$count]['messageid']=$msgsub[$counter1]['messageid'];
			$msgsub1[$count]['time'] = $msgsub[$counter1]['time'];
			$msgsub1[$count]['srccall'] = $msgsub[$counter1]['srccall'];
			$msgsub1[$count]['dst'] = $msgsub[$counter1]['dst'];
			$msgsub1[$count]['message'] = $msgsub[$counter1]['message'];
			$count = $count +1;} 
			}
//print_r($msgsub1);

//echo "count = $count \n";

if ($count == '0') {$msgcount = '0'; $checksum2 = $line2; return array($msg,$checksum2);}
		else {
			$msgcount = count($msgsub1);
			if ($msgsub1['0']['messageid'] > $line2) {$checksum2 = $msgsub1['0']['messageid'];}
				else {$checksum2 = $line2;}
			}


if ($GLOBALS['dispmessage'] == '1' && $msgcount != '0') {
		echo "Sending APRS messages to Twitter & SMS (IF enabled)...... " . $GLOBALS['linebreak'];
		for ($counter2=0; $counter2<=($msgcount)-1; $counter2+=1) {
		$message = $msgsub1[$counter2]['message'];
		$srccall = $msgsub1[$counter2]['srccall'];
		$dstcall = $msgsub1[$counter2]['dst'];
		$timedatemsg = $msgsub1[$counter2]['time'];
		$msgid = $msgsub1[$counter2]['messageid'];
		$timecheck = date("M d", $timedatemsg);
		$timedatemsg = date("M d, H:iT", $timedatemsg);

		if ($GLOBALS['currentdate'] == $timecheck) {
			$status = 'status=@' . $GLOBALS['uname'] . ' DE ' . $srccall . ' ' . $message . ' ' . $timedatemsg . ' via APRS ';
			updatecurl($GLOBALS['twitter_url'],$status,$GLOBALS['uname'],$GLOBALS['pwd']);
			echo "Twitter: $status " . $GLOBALS['linebreak'];

		if ($GLOBALS['hamohioupdate'] == '1') {
			$xml = updatecurl($GLOBALS['hamohiourl'],$status,$GLOBALS['hamohiouname'],$GLOBALS['hamohiopass']);
			}

			$msg = ' DE ' . $srccall . ' ' . $message . ' ' . $timedatemsg . ' via APRS';
			
		if ($smscheck == '1') {
				//include ('sms.php');
				$temp = sms($msg);
				echo "SMS: $msg " . $GLOBALS['linebreak'];
				}

			echo "Done. " . $GLOBALS['linebreak'];
      			}
		}
	}
	
	return array($msg, $checksum2);
}


function sms($msg) {

$tropo = 'https://api.tropo.com/1.0/sessions?action=create&token=' . $GLOBALS['token'] . '&numberToDial=' . $GLOBALS['phonenumber'] . '&msg='. urlencode($msg);
$sms = file_get_contents ($tropo);

$GLOBALS['post_body'] = $GLOBALS['post_body'] . "\n" . $msg;

return;
}


function comment($coment) {

if ($GLOBALS['postbeaconcomment'] == '0') {return;}

$status2 = 'APRS Beacon Comment: ' . urlencode($coment) . ' v ' . $GLOBALS['version'] . ' ' . date("h:i A", $GLOBALS['timedate']);
$GLOBALS['post_body'] = $GLOBALS['post_body'] . "\n" . $status2;
$status2 = 'status=' . $status2;

updatecurl($GLOBALS['twitter_url'],$status2,$GLOBALS['uname'],$GLOBALS['pwd']);
echo "Twitter: $status2 " . $GLOBALS['linebreak'];

if ($GLOBALS['hamohioupdate'] == '1') {
			updatecurl($GLOBALS['hamohiourl'],$status2,$GLOBALS['hamohiouname'],$GLOBALS['hamohiopass']);
			}
return;
}


function getfoursq($urls) {

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, $urls);
curl_setopt($curl_handle, CURLOPT_HTTPGET, 1);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$buffer=curl_exec($curl_handle);
curl_close($curl_handle);

$venue = json_decode($buffer, TRUE);

//print_r($venue);

return $venue;
}

?>
