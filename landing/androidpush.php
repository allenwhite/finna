<?php
 
// API access key from Google API's Console
// define( 'API_ACCESS_KEY', 'AIzaSyBaTJq2zII0Bg0-UgY6LMXSgoF00J213QQ' );
// define( 'API_ACCESS_KEY', 'AIzaSyCzG_qGf-hioC5w_ypCrR4eO-mWwlgEHLY' );
 define( 'API_ACCESS_KEY', 'AIzaSyCDY_vpsc0WLsxh18_tW0f7Bm-C4JyB3tY');
 
$registrationIds = array( $_GET['id'] );
 
// prep the bundle
$msg = array
(
	'message' 	=> 'I\'m finna make my first million',
	'title'		=> 'New finna from buckpal',
	'tickerText'	=> "WEEEEEEEEEEEEEEEEE \n OOOOOOOOOOO \nWEEEEEEEEEEEE\n OOOOOOOOOOOOOO\n WEEEEEEEEEEEEE \nOOOOOOOOOOOOO",
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
 
echo $result;
?>

<!--http://www.androidhive.info/2012/10/android-push-notifications-using-google-cloud-messaging-gcm-php-and-mysql/

iphone
http://stackoverflow.com/questions/21237445/send-ios-push-notification-from-php
http://stackoverflow.com/questions/24095177/apple-push-notification-php
http://stackoverflow.com/questions/22717275/apns-push-notifications-not-working-on-production
APA91bEgJKxyannjv9T_kes958hMcTbY9yAJhUFbA7J-lnJBIgLKg4F5UMuFELNv-Nm5RKpjjVD7ddxWLOl6PcZ9e0Flti7vkCSVxqIF189SgQWRXGTZsd2PYbLAgKFr4-4VTN38wsVHLbuau03-rQYC0cFfHerb1Mx3yuz3dN0BKZi_NtMdgcs






//////gcm
FINNA
Project Number: 290560500650 (ID)
Project ID: brave-smile-815 (Name)

API Key for browser applications: AIzaSyCzG_qGf-hioC5w_ypCrR4eO-mWwlgEHLY


http://grosh.co/finnaRoot/landing/androidpush.php?id=APA91bGC5-pHh3wLIM_Y4k3pVgbaMGAlD36NPvOfRzjunXMnay3o8nsKE-VQtT71O3C8IlR9ChN2c9BFqEPVWyPlESlORyYdxQsyjW5g7-7bgxPap2WyxbL1fdboDmPzzZFxDGKQ2Mi17C_wPSedUZpcz0aiIxubifMQZhyE6bgdQUmgwi534jg
http://grosh.co/finnaRoot/landing/androidpush.php?id=APA91bGC5pHh3wLIMY4k3pVgbaMGAlD36NPvOfRzjunXMnay3o8nsKEVQtT71O3C8IlR9ChN2c9BFqEPVWyPlESlORyYdxQsyjW5g77bgxPap2WyxbL1fdboDmPzzZFxDGKQ2Mi17CwPSedUZpcz0aiIxubifMQZhyE6bgdQUmgwi534jg


