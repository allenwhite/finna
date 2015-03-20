<!-- al how did you make this work -->

<?php
	
include_once("php_includes/check_login_status.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title><?php echo $log_username; ?></title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style/style.css">
	<script src="js/main.js"></script>
	<script type="text/javascript">
	function restrict(elem){
		var tf = _(elem);
		var rx = new RegExp;
		if(elem == "phone"){
			rx = /[^0-9]/g;
		}
		tf.value = tf.value.replace(rx, "");
	}
	</script>
</head>
<body class="editprofile">
	<header>
		<h1 class="editheader">Edit Profile</h1>
	</header>
	<div id="generalbody">
		<section>
			<form class="form" action="php_parsers/photo_system.php" method="POST" enctype="multipart/form-data">
				<!-- <img src="images/ic_action_edit.png"  class="editicons"> <input placeholder="Username" class="edittextbox" type="text" name="Name"><br>
				<br> -->
				<span class="edittextbox"><b><?php echo $log_username; ?></b></span><br><br>
				<img src="images/ic_action_edit.png" class="editicons"> <textarea rows="2" cols="19" placeholder="Bio" class="edittextbox" type="text" name="bio"></textarea><br>
				<br>
				<img src="images/ic_action_secure.png" class="editicons"> <input placeholder="Password" class="edittextbox" type="password" name="pword"><br>
				<br>
				<img src="images/ic_action_secure.png" class="editicons">  <input placeholder="Confirm Password" class="edittextbox" type="password" name="cpword"><br>
				<br>
				<img src="images/ic_action_call.png" class="editicons"> <input placeholder="Phone" class="edittextbox" type="text" name="phone"><br>
				<br> 
				<img src="images/ic_action_camera.png" class="editicons"> <input style="width:85%; font-size:16px;" placeholder="Profile Picture" class="edittextbox" type="file" name="img">
				<br>
				<br>
				<br>
				<center>
					<input style="padding: 10px;
font-size: 16px;
font-weight: bold;
margin-left: 15px;
background: rgb(98,200,236);
border:none;
color: white;
border-radius:7px; -webkit-appearance: none;" type="submit" class="statusbutts" value="Save Changes" name="submit"> 
				</center>
			</form>
		</section>
	</div>
</body>

</html>