<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	$peeps = "";
	include_once("php_includes/check_login_status.php");
	
	if(isset($_GET["u"])){
		$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
		$sql = "SELECT users.username, users.avatar, users.signup
			FROM
			(SELECT user1 AS user FROM friends WHERE user2='$u' AND accepted='1' 
				UNION 
			SELECT user2 AS user FROM friends WHERE user1='$u' AND accepted='1' ) AS friendz
			INNER JOIN
			users
			ON friendz.user=users.username
			ORDER BY username ASC";
	} else if(isset($_GET["osid"])){
		$osid = preg_replace('#[^a-z0-9]#i', '', $_GET['osid']);
		$sql = "SELECT users.username, users.avatar, users.signup
				FROM
				(SELECT account_name FROM user_likes WHERE osid='$osid') as likers 
				INNER JOIN
				users
				ON likers.account_name=users.username
				ORDER BY username ASC";
	}else {
		$sql = "SELECT username, avatar FROM users ORDER BY username ASC";

		$peeps .= '<div class="person" style="height:50px; padding:7px; border-bottom:1px solid rgb(98,200,236);">
						<div class="user_info" style="margin-left:30px; font-size:16px; color: rgb(98,200,236);">
							<input onkeyup="search()" type="text" id="searchbox" placeholder="Search..." style="width:100%; height:100%; border:none; outline:none;">
						</div>
					</div>';

	}
	$current = 0;

	if(isset($_GET["st"])){//if the status text is set, type must be B******************************************************************
		// checkbox to select
		$friend_button = '<span id="friendBtn_'.$current.'" style="float:right; margin-left:7px;" ><div class="chexbox"><input type="checkbox" value="None" id="chexbox'.$current.'" onchange="checkAll(this)" name="check" unchecked /><label for="chexbox'.$current.'"></label></div></span>';
		$peeps .= '<div class="person" style="min-height:50px; padding:7px; border-bottom:1px solid rgb(98,200,236);">
						<div class="user_info" style="margin-left:30px; font-size:16px; color: rgb(98,200,236);"><b><i>All my friends</i></b>'.$friend_button.
						'</div>
					</div>';
	}
	$query = mysqli_query($db_conx, $sql);
	
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		
		$current++;

		$username = $row['username'];

		$isFriend = false;
		if($row["username"] != $log_username && $user_ok == true){
			$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$username' AND accepted='1' OR user1='$username' AND user2='$log_username' AND accepted='1' LIMIT 1";
			if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
		        $isFriend = true;
		    }
		}
		$friend_button = '';
		// LOGIC FOR FRIEND BUTTON
		if($isFriend == true){
			if(isset($_GET["st"])){//if the status text is set, type must be B******************************************************************
				// checkbox to select
				$friend_button = '<span id="friendBtn_'.$current.'" style="float:right; margin-left:7px;" ><div class="chexbox"><input type="checkbox" value="'.$username.'" id="chexbox'.$current.'" onchange="uncheck(this)" name="check" unchecked /><label for="chexbox'.$current.'"></label></div></span>';
			}else{
				$friend_button = '<span id="friendBtn_'.$current.'" style="float:right; margin-left:7px;" ><button style="float: right; font-size: 10px; padding: 7px; width: 80px; height:30px; margin-top: 10px;" class="statusbutts" onclick="friendToggle(\'unfriend\',\''.$username.'\',\'friendBtn_'.$current.'\')">Unfriend</button></span>';
			}
		} else if($user_ok == true && $username != $log_username){
			$friend_button = '<span id="friendBtn_'.$current.'" style="float:right; margin-left:7px;"><button style="float: right; font-size: 10px; padding: 7px; width: 80px; height:30px; margin-top: 10px; padding-bottom:6px;" class="statusbutts" onclick="friendToggle(\'friend\',\''.$username.'\',\'friendBtn_'.$current.'\')"><img src="images/ic_action_add_person.png" style="width:16px;" /></button></span>';
		}
		
		
		$avatar = $row["avatar"];
		$userpic = '<img src="user/'.$username.'/'.$avatar.'" alt="'.$username.'" style="float:left; width:50px; height:50px; border-radius:100px;" class="user_pic">';
		if($avatar == NULL){
			$userpic = '<img style="float:left; width:50px; height:50px; border-radius:100px;" src="images/avatardefault.jpg" alt="'.$username.'" class="user_pic">';
		}
		$peeps .= '<div class="person" style="min-height:50px; padding:7px; border-bottom:1px solid rgb(98,200,236);">
						<a href="user.php?u='.$username.'">'.$userpic.'</a>
						<div class="user_info" style="margin-left:60px;"><b>'
							.$username.'</b>'.$friend_button.
						'</div>
					</div>';

		
	}//
	if(isset($_GET["st"])){//submitbuttom******************************************************************
		// checkbox to select
		$status = $_GET["st"];
		$status = urldecode($status);
		$status = htmlentities($status);
		$status = mysqli_real_escape_string($db_conx, $status);

		$loc = $_GET["lc"];
		$loc = urldecode($loc);
		$loc = htmlentities($loc);
		$loc = mysqli_real_escape_string($db_conx, $loc);

		$tm = $_GET["tm"];
		$tm = urldecode($tm);
		$tm = htmlentities($tm);
		$tm = mysqli_real_escape_string($db_conx, $tm);

		$peeps .= '<div class="person" style="min-height:55px;"></div>';
		$peeps .= '<button id="sendButton" onclick="postToStatus(\'status_post\',\'c\',\''.$log_username.'\',\''.$status.'\',\''.$loc.'\',\''.$tm.'\')"><div class="sendem"><span id="sendText"><b><i>Send >></i></b></span><img id="sendgif" src="images/leftshark.gif"></div></button>'; 
	}
?>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>Finna</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style/style.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
</head>
<body>
	<?php 
		echo $peeps; 
	?>
</body>
<script type="text/javascript">
		function search() {
			var searchtext = document.getElementById('searchbox');
		    var peopleBoxes = document.getElementsByClassName('person');
		    
		    for (var i = peopleBoxes.length - 1; i >= 0; i--) {
		    	var box = peopleBoxes[i].getElementsByClassName('user_info')[0].getElementsByTagName('b')[0];
		    	if (typeof(box) != 'undefined' && box != null){
		    		if(event.keyCode != 8){
			    		if(searchtext.value.toLowerCase() != box.innerHTML.substring(0,searchtext.value.length) && box.innerHTML.indexOf(searchtext.value.toLowerCase()) === -1){
							peopleBoxes[i].style.display='none';
						}    		
		    		}else{//backspace, show all the thing we just erased
		    			if(searchtext.value.toLowerCase() === box.innerHTML.substring(0,searchtext.value.length) || box.innerHTML.indexOf(searchtext.value.toLowerCase()) > -1){
							peopleBoxes[i].style.display='block';
						}
		    		}
		    	}
		    }
		     
		 }

		 function friendToggle(type,user,elem){
			_(elem).innerHTML = '<img style="width:53px; height:30px;" src="images/leftshark.gif">';
			var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					if(ajax.responseText == "friend_request_sent"){
						_(elem).innerHTML = 'Friend Request Sent!';
					} else if(ajax.responseText == "unfriend_ok"){
						_(elem).innerHTML = 'Friend Request Sent!';
					} else {
						//alert(ajax.responseText);
						_(elem).innerHTML = 'Friend Request Sent!';
					}
				}
			}
			ajax.send("type="+type+"&user="+user);
		}

		function checkAll(ele) {
		     var checkboxes = document.getElementsByTagName('input');
		     if (ele.checked) {
		         for (var i = 0; i < checkboxes.length; i++) {
		             if (checkboxes[i].type == 'checkbox') {
		                 checkboxes[i].checked = true;
		             }
		         }
		     }else{
		     	for (var i = 0; i < checkboxes.length; i++) {
		             if (checkboxes[i].type == 'checkbox') {
		                 checkboxes[i].checked = false;
		             }
		         }
		     }
		 }
		 function uncheck(ele) {
		     var checkboxes = document.getElementsByTagName('input');
		     if (!ele.checked) {
		         _("chexbox0").checked = false;
		     }
		 }
		///add ajax script to send username, status text and type=B to status system. friends as well(long string with seperator char?)************************************8***
		function postToStatus(action,type,user,ta,loc,tyme){
			var data = ta;
			if(data == ""){
				return false;
			}
			var friends = '';
			if(_("chexbox0").checked == true){
				type="a";
			}else{
				var checkboxes = document.getElementsByTagName('input');
				for (var i = 0; i < checkboxes.length; i++) {
		            if (checkboxes[i].type == 'checkbox' && checkboxes[i].checked == true) {
		        		friends = friends + checkboxes[i].value + '.';
		            }
		        }
			}
			_("sendButton").disabled = true;
			var ajax = ajaxObj("POST", "php_parsers/status_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					var datArray = ajax.responseText.trim().split("|");
					if(datArray[0] == "post_ok"){
						var sid = datArray[1];
						data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
						window.location.assign("index.php");

					} else {
						//alert(ajax.responseText);
					}
				}
			}
			ajax.send("action="+action+"&type="+type+"&user="+user+"&data="+data+"&friends="+friends+"&loc="+loc+"&time="+tyme);
			// alert("action="+action+"&type="+type+"&user="+user+"&data="+data+"&friends="+friends);
		}
	</script>
</html>