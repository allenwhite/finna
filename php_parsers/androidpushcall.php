<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once("../php_includes/check_login_status.php");
$sql = "SELECT gcm_regid FROM users WHERE username='alpal' AND activated='1' LIMIT 1";
$regid_query = mysqli_query($db_conx, $sql);
$regid_row = mysqli_fetch_row($regid_query);
$dbregid = $regid_row[0];
if($dbregid != "" && $dbregid != null){
	include_once("androidpush.php");
	echo $dbregid."\n</br>";
	sendNotification($dbregid,"Finna","thomas jefferson said they were finna!", "thomas jefferson said they were finna!");
	echo "ohhh";
}else{
	echo "boner";
}
mysqli_close($db_conx);
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





