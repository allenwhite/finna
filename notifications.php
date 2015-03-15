<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');
include_once("php_includes/check_login_status.php");
include_once("php_parsers/timestamp_system.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}else{
	//probably referred by app (view my profile)
		//check for reg_id
		if(isset($_GET["id"])){
			//if set, check if it matches what is there already
			$reg_id = $_GET['id'];
			
			$sql = "SELECT gcm_regid FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
			$regid_query = mysqli_query($db_conx, $sql);
			$regid_row = mysqli_fetch_row($regid_query);
			$dbregid = $regid_row[0];

			//set that mo fucka
			mysqli_query($db_conx, "UPDATE users SET gcm_regid='$reg_id' WHERE username='$log_username' LIMIT 1");
			
		}else if(isset($_GET["APNSid"])){
			//if set, check if it matches what is there already
			$reg_id = $_GET['APNSid'];
			
			$sql = "SELECT apnsID FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
			$regid_query = mysqli_query($db_conx, $sql);
			$regid_row = mysqli_fetch_row($regid_query);
			$dbregid = $regid_row[0];

			//set it 
			mysqli_query($db_conx, "UPDATE users SET apnsID='$reg_id' WHERE username='$log_username' LIMIT 1");
		
		}
}
$notesquery = mysqli_query($db_conx, "SELECT noteschecked FROM users WHERE username='$log_username' LIMIT 1");
$notesrow = mysqli_fetch_row($notesquery);
$notescheck = $notesrow[0];


$notification_list = "";
$sql = "SELECT * FROM notifications WHERE username='$log_username' ORDER BY date_time DESC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
$i = 0;

$didFinna[] = '';

while (($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) && ($i < 15)) {
	
	$app = $row["app"];
	$note = $row["note"];
	
	if($app == "Status Reply"){
		
		$initiator = $row["initiator"];
		$post_date = $row["date_time"];

		$note = addslashes($note);
		$statusidofreply = "SELECT osid FROM status WHERE data='$note' AND author='$initiator' AND postdate='$post_date'";
		$osidquery = mysqli_query($db_conx, $statusidofreply);
		if($osidarr = mysqli_fetch_array($osidquery, MYSQLI_ASSOC)){
			$osid = $osidarr['osid'];	
		}else{
			
			$post_date = date_sub(new DateTime($post_date, new DateTimeZone('America/New_York')), date_interval_create_from_date_string('1 second'));
			$post_date = date_format($post_date, 'Y-m-d H:i:s');
			$statusidofreply = "SELECT osid FROM status WHERE data='$note' AND author='$initiator' AND postdate='$post_date'";
			$osidquery = mysqli_query($db_conx, $statusidofreply);
			$osidarr = mysqli_fetch_array($osidquery, MYSQLI_ASSOC);
			$osid = $osidarr['osid'];
		}
		
		$note = stripslashes($note);

		$thumbquery = mysqli_query($db_conx, "SELECT avatar FROM users WHERE username='$initiator' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1avatar = $thumbrow[0];
		$user1pic = '<img src="user/'.$initiator.'/'.$user1avatar.'" alt="'.$initiator.'" class="user_spic">';
		if($user1avatar == NULL){
			$user1pic = '<img src="images/avatardefault.jpg" alt="'.$initiator.'" class="user_spic">';
		}

		$date_time = $row["date_time"];
		$date_time = convert_datetime($date_time); // Convert Date Time
		$date_time = makeAgo($date_time);
		
		$notification_list .= "<div class='note friendrequests' ><a href='user.php?u=".$initiator."'>".$user1pic."</a><a class='seeallfinners' href='index.php#status_".$osid."'><div class='user_info'><b>$initiator</b><span class='statusDateAndDelete'>$date_time</span><br />$note</div></a></div>";
	
		$i++;

	}else if($app == "Status Finna" && !in_array($note, $didFinna, true)){

		$note = addslashes($note);

		$statusidoffinna ="SELECT osid FROM status WHERE data='$note' AND author='$log_username'";
		$osidquery = mysqli_query($db_conx, $statusidoffinna);
		$osidarr = mysqli_fetch_array($osidquery, MYSQLI_ASSOC);
		$osid = $osidarr['osid'];


		$finnaSql = "SELECT initiator FROM notifications WHERE username='$log_username' AND note='$note'";
		$finnaquery = mysqli_query($db_conx, $finnaSql);
		$numfinnas = mysqli_num_rows($finnaquery);
		$notification_list .= "<div class='note friendrequests'><a class='seeallfinners' href='seeusers.php?osid=".$osid."'><div class='user_info'>";
		$note = stripslashes($note);
		$r = 0;
		while (($finnarow = mysqli_fetch_array($finnaquery, MYSQLI_ASSOC)) && ($r < 6)) {
			$initiator = $finnarow["initiator"];
			$thumbquery = mysqli_query($db_conx, "SELECT avatar FROM users WHERE username='$initiator' LIMIT 1");
			$thumbrow = mysqli_fetch_row($thumbquery);
			$user1avatar = $thumbrow[0];
			$user1pic = '<img src="user/'.$initiator.'/'.$user1avatar.'" alt="'.$initiator.'" class="user_fpic">';
			if($user1avatar == NULL){
				$user1pic = '<img src="images/avatardefault.jpg" alt="'.$initiator.'" class="user_fpic">';
			}
			$notification_list .= $user1pic;
			$r++;
		}
		if($numfinnas > 6){
			if($numfinnas - 6 ==1){$notification_list .= "<br><i>and ".($numfinnas - 6)." other</i>";}
			else{$notification_list .= "<br><i>and ".($numfinnas - 6)." others</i>";}
			
		}
		$notification_list .="<br>".$note."</div></a></div>";
		$didFinna[] = $note;
		
		$i++;
	}
	
}

mysqli_query($db_conx, "UPDATE users SET noteschecked=now() WHERE username='$log_username' LIMIT 1");
?><?php
$friend_requests = "";
$sql = "SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC";
$query = mysqli_query($db_conx, $sql);
if($query){
	$numrows = mysqli_num_rows($query);	
}else{
	$numrows = 0;
}
if($numrows < 1){
	$friend_requests = '';
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$reqID = $row["id"];
		$user1 = $row["user1"];
		//$datemade = $row["date_time"];
		//$datemade = strftime("%B %d", strtotime($datemade));
		$thumbquery = mysqli_query($db_conx, "SELECT avatar FROM users WHERE username='$user1' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1avatar = $thumbrow[0];
		$user1pic = '<img src="user/'.$user1.'/'.$user1avatar.'" alt="'.$user1.'" class="user_pic">';
		if($user1avatar == NULL){
			$user1pic = '<img src="images/avatardefault.jpg" alt="'.$user1.'" class="user_pic">';
		}
		
		$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$friend_requests .= '<a href="user.php?u='.$user1.'">';
		$friend_requests .= $user1pic;
		$friend_requests .= '</a><div class="user_info" style="float:left; margin-left:0px; padding-left:0px;" id="user_info_'.$reqID.'">'.$user1.' is finna be your friend';
		$friend_requests .= '</div><span class="statusDateAndDelete"></span><br><br>';
		$friend_requests .= '<button class="statusbutts" onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button>';
		$friend_requests .= '<button class="statusbutts" onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$friend_requests .= '</div>';
	}
}
?>
<?php
if($friend_requests == '' && $notification_list == ''){
	$friend_requests .= '<div class="friendrequests" style="text-align:center;">No new notifications at this time.</div><center><a href="seeusers.php"><button style="margin-top:7px;" class="statusbutts">Find more friends 👥</button></a></center>';
}  
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Notifications and Friend Requests</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link href="style/Avenir.ttc" type='type/css'>
<link rel="stylesheet" href="style/style.css">
<style type="text/css">
div#notesBox{width:100%; margin:0px; padding:0px;}
div#friendReqBox{width:100%; padding:0px;}
div.friendrequests{min-height:70px; border-bottom:rgb(98,200,236) 1px solid; border-color:rgb(98,200,236); margin-bottom:0px;padding:7px;}
img.user_pic{float:left; width:50px; height:50px; margin-right:8px; border-radius:100px;}
img.user_spic{float:left; width:40px; height:40px; margin-right:8px; border-radius:100px;}
img.user_fpic{width:35px; height:35px; margin-right:6px; border-radius:100px;}
div.user_info{padding-left:50px; margin-top:0px; font-size:14px;word-wrap:break-word;}
.friendrequests > button{margin:2px; float:right;}
.seeallfinners{color:black;}
.seeallfinners:visited{color:black;}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function friendReqHandler(action,reqid,user1,elem){
	var conf = confirm("Press OK to '"+action+"' this friend request.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = "processing ...";
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "accept_ok"){
				_(elem).innerHTML = "<b>You are now friends</b>";
			} else if(ajax.responseText == "reject_ok"){
				_(elem).innerHTML = "<b>Request Rejected</b>";
			} else {
				_(elem).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
}
</script>
</head>
<body>
<div id="pageMiddle">
  <!-- START Page Content -->
  <div id="friendReqBox" style="padding-right:0px; padding-left:0px;"><?php echo $friend_requests; ?></div>
  <div id="notesBox" style="padding-right:0px; padding-left:0px;"><?php echo $notification_list; ?></div>
  <div style="clear:left;"></div>
  <!-- END Page Content -->
</div>
</body>
</html> 