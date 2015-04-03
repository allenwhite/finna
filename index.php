<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');
include_once("php_includes/check_login_status.php");
if($user_ok != true){
	header("location: signup.php");
}else{
	//probably referred by app (view my profile)
	//check for reg_id
	if(isset($_GET["id"])){
		//if set, check if it matches what is there already
		$reg_id = $_GET['id'];

		//set that b
		mysqli_query($db_conx, "UPDATE users SET gcm_regid='' WHERE gcm_regid='$reg_id' AND username!='$log_username' LIMIT 1");

		//set that b
		mysqli_query($db_conx, "UPDATE users SET gcm_regid='$reg_id' WHERE username='$log_username' LIMIT 1");
		
	}else if(isset($_GET["APNSid"])){
		//if set, check if it matches what is there already
		$reg_id = $_GET['APNSid'];
		
		mysqli_query($db_conx, "UPDATE users SET apnsID='' WHERE apnsID='$reg_id' AND username!='$log_username' LIMIT 1");

		//bitch try me
		mysqli_query($db_conx, "UPDATE users SET apnsID='$reg_id' WHERE username='$log_username' LIMIT 1");
		// type and words
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>Finna</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	
	<link rel="stylesheet" href="style/style.css">

	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
	<div id="pageMiddle">
		<?php
			if($user_ok == true){
				include_once("template_status_feed.php");
			}
		?>
	</div>
</body>
</html>