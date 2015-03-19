<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');

include_once("php_parsers/timestamp_system.php");

$statuslist = "";
$avatarsql = "SELECT avatar FROM users WHERE username='$log_username' LIMIT 1";
$avatarquery = mysqli_query($db_conx, $avatarsql);
$avatarrow = mysqli_fetch_array($avatarquery, MYSQLI_ASSOC);
$avatar = $avatarrow["avatar"];
if($avatar != ""){
	$mypic = 'user/'.$log_username.'/'.$avatar.'';
} else {
	$mypic = 'images/avatardefault.jpg';
}

?><?php
	$sql = "SELECT status.data, status.id, status.osid, status.account_name, status.author, status.postdate, status.type
			FROM
			(SELECT user1 AS user FROM friends WHERE user2='$log_username' AND accepted='1' 
				UNION 
			SELECT user2 AS user FROM friends WHERE user1='$log_username' AND accepted='1' 
				UNION 
			SELECT username AS user FROM users WHERE username='$log_username') AS friendz
			INNER JOIN
			status
			ON friendz.user=status.account_name
			ORDER BY postdate DESC";

	$query = mysqli_query($db_conx, $sql);
	$statusnumrows = mysqli_num_rows($query);
	if($statusnumrows < 1){
		$statuslist .= '<center><br><br><p style="font-size:18px;"><b><a href="seeusers.php"><button style="margin-top:7px;" class="statusbutts">Find more friends 👥</button></a></b></p></center>';
	}
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		if($row["type"] == 'c'){
		 	$osid = $row["osid"];
		 	$statuslist .= '<!--  XXX  '.$row["osid"].'  XXX  '.$osid.'  XXX  -->';
		 	$DMquery = mysqli_query($db_conx, "SELECT * FROM directMessages WHERE username='$log_username' AND osid='$osid'");
			$statusnumrows = mysqli_num_rows($DMquery);
			if($statusnumrows < 1){
				continue;
			}

		}
		if($row["type"] == 'c' || $row["type"] == 'a'){

			$statusid = $row["id"];
			$account_name = $row["account_name"];
			$author = $row["author"];
			$postdate = $row["postdate"];
			$postdate = convert_datetime($postdate); // Convert Date Time
			$postdate = makeAgo($postdate);

			$data = $row["data"];
			$data = nl2br($data);
			$data = str_replace("&amp;","&",$data);
			$data = stripslashes($data);
			$data = trim($data);

			$data = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="blank">\0</a>', $data);

			$avatarsql = "SELECT avatar FROM users WHERE username='$author' LIMIT 1";
			$avatarquery = mysqli_query($db_conx, $avatarsql);
			$avatarrow = mysqli_fetch_array($avatarquery, MYSQLI_ASSOC);
			$avatar = $avatarrow["avatar"];
			if($avatar != ""){
				$pic = 'user/'.$author.'/'.$avatar.'';
			} else {
				$pic = 'images/avatardefault.jpg';
			}

			$statusDeleteButton = '';
			//who can delete the content?
			if($author == $log_username || $account_name == $log_username ){
				$statusDeleteButton = '<span id="sdb_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS AND ITS REPLIES"><img style="margin-right:-1px;" src ="images/discard.png" alt="delete"></a></span>';
			}
			
			//GATHER UP ANY STATUS LIKES
			$statuslikes = '<span class="imfinna"><a id="imfinna_'.$statusid.'" class="imfinna" href="#" onclick="return false;" onmousedown="likeStatus('.$statusid.',\''.$log_username.'\');" title="I\'m finna!"><b>';
			$query_likes = mysqli_query($db_conx, "SELECT account_name FROM user_likes WHERE osid='$statusid'");
			if($query_likes){
				$likesnumrows = mysqli_num_rows($query_likes);	
			}else{
				$likesnumrows = 0;
			}
			$likerList = "<a href='seeusers.php?osid=$statusid' style='color:black;'>";
			$hasliked = false;
	    	if($likesnumrows > 0){
	    	    if($likesnumrows > 2){
			        for($i = 0; $i < 2; $i++){
			        	$rowlike = mysqli_fetch_array($query_likes, MYSQLI_ASSOC);
			        	$liker = $rowlike["account_name"];
						if($liker == $log_username){
							$hasliked = true;
						}
	    		    	$likerList .= $liker.', '; 
					}
					while ($rowlike = mysqli_fetch_array($query_likes, MYSQLI_ASSOC)) {
						$liker = $rowlike["account_name"];
						if($liker == $log_username){
							$hasliked = true;
						}
					}
					$likerList = rtrim($likerList);
					$likerList = rtrim($likerList,',');
					$likerList .= ' and '.($likesnumrows - 2).' others are finna';
				}else{
					while ($rowlike = mysqli_fetch_array($query_likes, MYSQLI_ASSOC)) {
						$liker = $rowlike["account_name"];
						if($liker == $log_username){
							$hasliked = true;
						}
		        		$likerList .= $liker.', ';
					}
					$likerList = rtrim($likerList);
					$likerList = rtrim($likerList,',');
					if($likesnumrows == 1){
						$likerList .= ' is finna';
					}else{
						$likerList .= ' are finna';
					}
				}
				$likerList .="</a>";
			}
			if($hasliked){
				$statuslikes .= "You are finna!</b> &nbsp;</a>".$likerList;	
			}else{
				$statuslikes .= "I'm finna</b> &nbsp;</a>".$likerList;	
			}
		
			$statuslikes .= "</span>";



			// GATHER UP ANY STATUS REPLIES
			$status_replies = "";
			$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
			$replynumrows = mysqli_num_rows($query_replies);
	    	if($replynumrows > 0){
	    	    while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
					$statusreplyid = $row2["id"];
					$replyauthor = $row2["author"];
					$replydata = $row2["data"];
					$replydata = nl2br($replydata);
					$replypostdate = $row2["postdate"];
					$replypostdate = convert_datetime($replypostdate); // Convert Date Time
					$replypostdate = makeAgo($replypostdate);

					$replydata = str_replace("&amp;","&",$replydata);
					$replydata = stripslashes($replydata);
					$replydata = trim($replydata);
					$replydata = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="blank">\0</a>', $replydata);

					$replyavatarsql = "SELECT avatar FROM users WHERE username='$replyauthor' LIMIT 1";
					$replyavatarquery = mysqli_query($db_conx, $replyavatarsql);
					$replyavatarrow = mysqli_fetch_array($replyavatarquery, MYSQLI_ASSOC);
					$replyavatar = $replyavatarrow["avatar"];
					if($replyavatar != ""){
						$replypic = 'user/'.$replyauthor.'/'.$replyavatar.'';
					} else {
						$replypic = 'images/avatardefault.jpg';
					}

					$replyDeleteButton = '';
					if($replyauthor == $log_username || $account_name == $log_username ){
						$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT"><img style="margin-right:-1px;" src ="images/discard.png" alt="delete"></a></span>';
					}
					$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes">
											<div>
												<a href="user.php?u='.$replyauthor.'">
													<img class="userpostpics" src="'.$replypic.'" alt="'.$replyauthor.'" title="'.$replyauthor.'">
												</a>
												<div class="statusData">
													<a class="statusAuthor"href="user.php?u='.$replyauthor.'">'.
														$replyauthor.'
													</a>
													<span class="statusDateAndDelete">'.$replypostdate.' '.$replyDeleteButton.'</span>
													<br />'.$replydata.'
													
												</div>
											</div>
										</div>';
	        	}
	    	}
	    	$commentIcon = '';
	    	if(strlen($status_replies) != 0){
				$commentIcon = '<div style="margin-top:-25px; float:right; margin-right: 22px;"><img src="images/comments.png" alt="Finna see comments?"></div>';
	    	}
			$statuslist .= '<div id="status_'.$statusid.'" class="status_boxes">
								<div onclick="showComments('.$statusid.')>
									<a href="user.php?u='.$author.'">
										<img class="userpostpics" src="'.$pic.'" alt="'.$author.'" title="'.$author.'">
									</a>
									<div class="statusData">
										<a class="statusAuthor" href="user.php?u='.$author.'">
											'.$author.'
										</a>
										<span class="statusDateAndDelete">'.$postdate.' '.$statusDeleteButton.'</span>
										<br />'.$data.'
										<br /><br />
										<div style="margin-bottom:5px; margin-right:45px;">
											'.$statuslikes.'
										</div>
										'.$commentIcon.'
										<a style="margin-top:-25px;" href="newReply.php?sid='.$statusid.'" class="replyStatus"><img src ="images/reply.png" alt="Finna Reply?"></a>
									</div>
								</div>
								<div class="replyGroup" id="replyGroup'.$statusid.'">'
									.$status_replies.'
								</div>
							</div>';
		}				
	}
?>
<script>
function deleteStatus(statusid,statusbox){
	var remove = confirm("Finna delete?"); 
	if(remove){
		var ajax = ajaxObj("POST", "php_parsers/status_system.php");
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				if(ajax.responseText == "delete_ok"){
					_(statusbox).style.display = 'none';
					_("replytext_"+statusid).style.display = 'none';
					_("replyBtn_"+statusid).style.display = 'none';
				} else {
					// alert(ajax.responseText);
					// Android.showToast(ajax.responseText);

					_(statusbox).style.display = 'none';
					_("replytext_"+statusid).style.display = 'none';
					_("replyBtn_"+statusid).style.display = 'none';
				}
			}
		}
		ajax.send("action=delete_status&statusid="+statusid);
	}
}
function deleteReply(replyid,replybox){
	var remove = confirm("Finna delete?"); 
	if(remove){
		var ajax = ajaxObj("POST", "php_parsers/status_system.php");
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				if(ajax.responseText == "delete_ok"){
					_(replybox).style.display = 'none';
				} else {
					// alert(ajax.responseText);
					// Android.showToast(ajax.responseText);
					_(replybox).style.display = 'none';
				}
			}
		}
		ajax.send("action=delete_reply&replyid="+replyid);
	}
}
function likeStatus(statusid,username){
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "like_ok"){
				_("imfinna_"+statusid).innerHTML = 'You are finna! ';
			} else {
				//alert(ajax.responseText);
				_("imfinna_"+statusid).innerHTML = 'You are finna! ';
			}
		}
	}
	ajax.send("action=like_status&statusid="+statusid+"&user="+username);
}
</script>
<div id="statusarea">
  <?php echo $statuslist; ?>
</div>

<script>

$(".replyGroup").hide();


function showComments(statusid){
	var repliestohide = '#replyGroup' + statusid;
	$(repliestohide).toggle("fast");
}

</script>







