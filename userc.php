<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');

include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
// Make sure the _GET username is set, and sanitize it
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
	if($log_username !== ''){
		$u = $log_username;
		
	}else{
    	header("location: index.php");
    	exit();
    }
}
if($log_username !== ''){
	
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


// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
	echo "That user does not exist or is not yet activated, press back";
    exit();	
}
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
}
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$avatar = $row["avatar"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
}
if($avatar != ""){
	$pic = 'user/'.$u.'/'.$avatar.'';
} else {
	$pic = 'images/avatardefault.jpg';
}
?><?php
$isFriend = false;
if($u != $log_username && $user_ok == true){
	$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
        $isFriend = true;
    }
}
?><?php


$bio ='';
$sql = "SELECT bio FROM users WHERE username='$u' LIMIT 1";
$bio_query = mysqli_query($db_conx, $sql);
$biorow = mysqli_fetch_array($bio_query, MYSQLI_ASSOC);
$biostring = $biorow["bio"];
if($biostring != NULL){

	$bio = $biostring;
}

?><?php 
$friend_button = '';
// LOGIC FOR FRIEND BUTTON
if($isFriend == true){
	$friend_button = '<button class="statusbutts" style="float:right; margin-right:7px; margin-top:10px;" onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
} else if($user_ok == true && $u != $log_username){
	$friend_button = '<button class="statusbutts" style="float:right; margin-right:7px; margin-top:10px;" onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
}else{
	$friend_button = '<a href="seeusers.php"><button style="float:right;padding-bottom:6px;width:60px; margin-top:10px; margin-left:7px; margin-right:7px; " class="statusbutts"><img src="images/ic_action_add_person.png" style="width:16px;" /></button></a>';
	$friend_button .= '<a href="editprofile.php"><button style="float:right;padding-bottom:6px;width:60px; margin-top:10px;" class="statusbutts"><img src="images/ic_action_settings.png" style="width:16px;" /></button></a>';
}
?><?php
$friendsHTML = '';
$friends_view_all_link = '';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friendsHTML = "";
} else {
	$max = 36;
	$all_friends = array();
	// $sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	// $query = mysqli_query($db_conx, $sql);
	// while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	// 	array_push($all_friends, $row["user1"]);
	// }
	// $sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	// $query = mysqli_query($db_conx, $sql);
	// while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	// 	array_push($all_friends, $row["user2"]);
	// }
	$sql = "SELECT user1 AS user FROM friends WHERE user2='$u' AND accepted='1'
			UNION
			SELECT user2 AS user FROM friends WHERE user1='$u' AND accepted='1' 
			ORDER BY user ASC";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user"]);
	}



	$friendArrayCount = count($all_friends);
	if($friendArrayCount > $max){
		array_splice($all_friends, $max);
	}
	if($friend_count > $max){
		$friends_view_all_link = '<a href="seeusers.php?u='.$u.'">view all</a>';
	}
	$orLogic = '';
	foreach($all_friends as $key => $user){
		$orLogic .= "username='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT username, avatar FROM users WHERE $orLogic ORDER BY signup DESC";
	$query = mysqli_query($db_conx, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_username = $row["username"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
		} else {
			$friend_pic = 'images/avatardefault.jpg';
		}
		$friendsHTML .= '<a href="user.php?u='.$friend_username.'">
							<div class="userpics" style="background-image:url('.$friend_pic.'); background-position: center center; background-repeat: no-repeat; background-size:47px; z-index:2000; width:47px; height:47px; display:inline-block;" ></div>
						</a>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $u; ?></title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
	function friendToggle(type,user,elem){
		_(elem).innerHTML = '<img style="width:53px; height:30px;" src="images/leftshark.gif">';
		var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				if(ajax.responseText == "friend_request_sent"){
					_(elem).innerHTML = 'Friend Request Sent!';
				} else if(ajax.responseText == "unfriend_ok"){
					_(elem).innerHTML = '<button class="statusbutts" style="float:right; margin-right:7px;" onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
				} else {
					// alert(ajax.responseText);
					// Android.showToast(ajax.responseText);
					// _(elem).innerHTML = 'Try again later';
					_(elem).innerHTML = 'Friend Request Sent!'
				}
			}
		}
		ajax.send("type="+type+"&user="+user);
	}
</script>
</head>
<body>
	<div id="UserBioPage">
<div id="pageMiddle">
	<div id="nonfeedCrap">
		
				<div> 
				<div id="profile_pic_box" style="
											margin-right: auto;
											margin-left: auto;
											margin-top: 20px;
											border-radius: 100px;
											border: 1px solid white;
											width: 150px;
											height: 150px;
											overflow-x: hidden;
											overflow-y: hidden;
											margin-bottom: 30px;
											">
					<?php echo '<div style="background-image:url('.$pic.'); 
										z-index: 2000;
										width: 75px;
										height: 75px;
										border-radius: 100px;
										background-position: center center; 
										background-repeat: no-repeat;
										background-size:80px;
										z-index:2000;
										width:80px;
										height:80px;" ></div>'; 
					?>
				</div> 
			</div>
				
			<?php echo $bio; ?>

			<h2 class="UserNameDisplay" style="
											  text-align:center;
											  color: #fff;
											  text-shadow: 2px 2px #000;
											  ">

					<?php echo $u; ?>
				</h2>
				
			</div>
			
				
				
				
				<div><?php echo $friend_button; ?></div>
			</div>
		</div>
		
		<p style="margin-left:auto; margin-right: auto; font-size:16px;"><?php echo $friend_count." friends ".$friends_view_all_link; ?>  </p>
		
		<div style="width:100%; margin-right:auto; margin-left:auto;" class="friends"> <?php echo $friendsHTML; ?> </div>
	</div>
	<hr style="margin-bottom:0px; margin-top:0px;"/>
	<?php include_once("template_status_feed.php"); ?>
</div>
</div>
</body>
</html>