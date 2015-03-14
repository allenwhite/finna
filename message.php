<?php
$message = "";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if($msg == "activation_failure"){
	$message = '<h2>Activation Error</h2> Sorry there seems to have been an issue activating your account at this time. We have already notified ourselves of this issue and we will contact you via email when we have identified the issue.';
} else if($msg == "activation_success"){
	$message = '<h2>Activation Success</h2> Your account is now activated. <a href="login.php">Click here to log in</a>';
} else {
	$message = $msg;
}
?>
<?php
include_once("php_includes/check_login_status.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Finna</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style/style.css">
</head>
<body>
	<div style="text-align:center;" id="pageMiddle">
		<div><?php echo $message; ?></div>	
	</div>
</body>
</html>

