<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

function apnsPush($dbregid, $content){
	$apnsHost = 'gateway.push.apple.com';
	$apnsPort = 2195;
	$apnsCert = 'apns-prod.pem';
	$deviceToken = $dbregid;

	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

	$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $streamContext);

	$payload['aps'] = array('alert' => $content, 'badge' => 1, 'sound' => 'default');
	$payload = json_encode($payload);

	$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
	$result = fwrite($apns, $apnsMessage, strlen($apnsMessage));

	// if(!$result) { 
		// echo $error.' ~ '.$errorString;
	// }else{
	// 	echo 'hmmm';
	// }
	@socket_close($apns);
	fclose($apns);	
}


?>