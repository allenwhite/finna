<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
function sendNotification($dbregid, $title, $content, $ticker){
		// API access key from Google API's Console
	// define( 'API_ACCESS_KEY', 'AIzaSyBaTJq2zII0Bg0-UgY6LMXSgoF00J213QQ' );
	// define( 'API_ACCESS_KEY', 'AIzaSyCzG_qGf-hioC5w_ypCrR4eO-mWwlgEHLY' );
	if (!defined('API_ACCESS_KEY')) define('API_ACCESS_KEY', 'AIzaSyCDY_vpsc0WLsxh18_tW0f7Bm-C4JyB3tY');
	 
	$registrationIds = array($dbregid);
	
	// prep the bundle
	$msg = array
	(
		'message' 	=> $content,
		'title'		=> $title,
		'tickerText'	=> $ticker,
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	 
	$fields = array
	(
		'registration_ids' 	=> $registrationIds,
		'data'			=> $msg
	);
	 
	$headers = array
	(
		'Authorization: key=' . API_ACCESS_KEY,
		'Content-Type: application/json'
	);
	 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );
	//echo $result;
}

?>




