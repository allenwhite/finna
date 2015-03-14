<?php
include_once("php_includes/check_login_status.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Finna</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style/style.css">
	<style type="text/css">
		.userpics{width:100px; height:100px; margin:2px;}
	</style>
	<script src="js/main.js"></script>
</head>
<body>
	<?php include_once("template_pageTop.php"); ?>
	<div id="pageMiddle">
		<?php
			if($user_ok == true){
				$usersHTML = '';
				$max = 50;
				$all_users = array();
				$sql = "SELECT username, avatar FROM users WHERE activated='1' ORDER BY RAND() LIMIT $max";
				$query = mysqli_query($db_conx, $sql);
				while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
					$username = $row["username"];
					$avatar = $row["avatar"];
					if($avatar != ""){
						$pic = 'user/'.$username.'/'.$avatar.'';
					} else {
						$pic = 'images/avatardefault.jpg';
					}
					$usersHTML .= '<a href="user.php?u='.$username.'"><img class="userpics" src="'.$pic.'" alt="'.$username.'" title="'.$username.'"></a>';
				}
				echo $usersHTML;
				echo "<p><a href='logout.php'>Log out</a></p>";
			}else{
				echo "<p><a href='signup.php'><b>Sign Up</b></a></p><p><a href='login.php'><b>Log in</b></a></p>";
			}
		?>
	</div>
	<?php include_once("template_pageBottom.php"); ?>
</body>
</html>